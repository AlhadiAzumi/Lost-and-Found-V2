<?php

namespace Tests\Feature;

use App\Models\Campus;
use App\Models\CampusApplication;
use App\Models\Category;
use App\Models\Claim;
use App\Models\Item;
use App\Models\User;
use App\Support\CampusContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Mail\CampusAdminInvitation;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class LostAndFoundBackendTest extends TestCase
{
    use RefreshDatabase;

    public function test_found_report_requires_verification_data_and_hashes_its_answer(): void
    {
        $reporter = User::factory()->create();
        $category = $this->category();
        $campus = $reporter->campus;

        $this->actingAs($reporter)
            ->post(route('campus.reports.store', ['campus' => $campus->slug]), [
                'type' => 'found',
                'category_id' => $category->id,
                'title' => 'Dompet biru',
                'description' => 'Ditemukan di dekat perpustakaan.',
                'location' => 'Perpustakaan',
                'storage_location' => 'Pos satpam utama',
                'date_event' => now()->toDateString(),
                'security_question' => 'Apa warna lapisan bagian dalam?',
                'security_answer' => 'merah',
            ])
            ->assertSessionHasNoErrors();

        $item = Item::query()->where('title', 'Dompet biru')->firstOrFail();

        $this->assertSame('pending', $item->status);
        $this->assertTrue(Hash::check('merah', $item->getRawOriginal('security_answer')));
        $this->assertTrue($item->matchesSecurityAnswer(' MERAH '));
        $this->assertArrayNotHasKey('security_answer', $item->toArray());
    }

    public function test_report_moderation_requires_a_rejection_reason(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $item = $this->foundItem();

        $this->actingAs($admin)
            ->post(route('campus.admin.reports.status', [
                'campus' => $admin->campus->slug,
                'item' => $item,
            ]), [
                'status' => 'rejected',
            ])
            ->assertSessionHasErrors('rejection_reason');

        $this->assertSame('pending', $item->fresh()->status);
    }

    public function test_claim_approval_and_handover_complete_the_item_workflow(): void
    {
        $admin = User::factory()->create(['role' => 'satpam']);
        $owner = User::factory()->create();
        $claimant = User::factory()->create();
        $item = $this->foundItem($owner, 'Flashdisk hitam');
        $campus = $admin->campus;

        $this->actingAs($admin)
            ->post(route('campus.admin.reports.status', ['campus' => $campus->slug, 'item' => $item]), [
                'status' => 'approved',
            ])
            ->assertSessionHasNoErrors();
        $this->assertSame('approved', $item->fresh()->status);

        $this->actingAs($claimant)
            ->post(route('campus.claims.store', ['campus' => $campus->slug, 'item' => $item]), [
                'security_answer' => 'label biru',
            ])
            ->assertSessionHasNoErrors();

        $claim = Claim::query()->where('item_id', $item->id)->firstOrFail();
        $this->assertSame('pending', $claim->status);
        $this->assertSame('claim_in_progress', $item->fresh()->status);

        $this->actingAs($admin)
            ->post(route('campus.admin.claims.verify', ['campus' => $campus->slug, 'claim' => $claim]), [
                'action' => 'approve',
            ])
            ->assertSessionHasNoErrors();

        $claim->refresh();
        $this->assertSame('approved', $claim->status);
        $this->assertMatchesRegularExpression('/^LFP-[A-Z0-9]{4}-[A-Z0-9]{4}$/', $claim->handover_code);

        $this->actingAs($admin)
            ->post(route('campus.admin.handovers.process', ['campus' => $campus->slug, 'claim' => $claim]), [
                'code' => 'KODE-SALAH',
            ])
            ->assertSessionHasErrors('code');

        $this->post(route('campus.admin.handovers.process', ['campus' => $campus->slug, 'claim' => $claim]), [
            'code' => $claim->handover_code,
        ])->assertSessionHasNoErrors();

        $this->assertSame('completed', $claim->fresh()->status);
        $this->assertSame('returned', $item->fresh()->status);
        $this->assertDatabaseHas('handovers', ['claim_id' => $claim->id]);
    }

    public function test_wrong_claim_answer_cannot_be_approved(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $owner = User::factory()->create();
        $claimant = User::factory()->create();
        $item = $this->foundItem($owner, 'Payung hitam');
        $campus = $admin->campus;

        $item->update(['status' => 'approved']);
        $this->actingAs($claimant)
            ->post(route('campus.claims.store', ['campus' => $campus->slug, 'item' => $item]), [
                'security_answer' => 'jawaban salah',
            ])
            ->assertSessionHasNoErrors();

        $claim = Claim::query()->where('item_id', $item->id)->firstOrFail();
        $this->actingAs($admin)
            ->post(route('campus.admin.claims.verify', ['campus' => $campus->slug, 'claim' => $claim]), [
                'action' => 'approve',
            ])
            ->assertSessionHasErrors('security_answer');

        $this->assertSame('pending', $claim->fresh()->status);
    }

    public function test_tenant_catalog_does_not_show_another_campuss_items(): void
    {
        $mainCampus = Campus::query()->where('slug', config('campus.default_slug'))->firstOrFail();
        $otherCampus = Campus::query()->create([
            'name' => 'Kampus Dua',
            'slug' => 'kampus-dua',
            'is_active' => true,
        ]);
        app(CampusContext::class)->set($otherCampus);
        $otherUser = User::factory()->create();
        $item = $this->foundItem($otherUser, 'Barang kampus dua');
        $item->update(['status' => 'approved']);

        $this->get(route('campus.catalog', ['campus' => $mainCampus->slug]))
            ->assertOk()
            ->assertDontSee('Barang kampus dua');

        $this->get(route('campus.catalog', ['campus' => $otherCampus->slug]))
            ->assertOk()
            ->assertSee('Barang kampus dua');
    }

    public function test_claim_proof_is_only_available_to_campus_staff(): void
    {
        Storage::fake('local');
        $admin = User::factory()->create(['role' => 'admin']);
        $claimant = User::factory()->create();
        $item = $this->foundItem(User::factory()->create());
        $path = Storage::disk('local')->putFile('claims', UploadedFile::fake()->create('proof.pdf', 20, 'application/pdf'));
        $claim = Claim::query()->create([
            'item_id' => $item->id,
            'user_id' => $claimant->id,
            'security_answer' => 'jawaban',
            'proof_image' => $path,
            'status' => 'pending',
        ]);

        $this->actingAs($claimant)
            ->get(route('campus.claims.proof', ['campus' => $claimant->campus->slug, 'claim' => $claim]))
            ->assertForbidden();

        $this->actingAs($admin)
            ->get(route('campus.claims.proof', ['campus' => $admin->campus->slug, 'claim' => $claim]))
            ->assertOk();
    }

    public function test_platform_admin_approval_issues_one_time_campus_setup_invitation(): void
    {
        Mail::fake();
        $admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'platform@example.test',
        ]);
        config(['campus.platform_admin_email' => $admin->email]);

        $this->post(route('campus-applications.store'), [
            'name' => 'Kampus Contoh',
            'email' => 'applicant@example.test',
        ])->assertSessionHasNoErrors();

        $application = CampusApplication::query()->where('email', 'applicant@example.test')->firstOrFail();
        $this->actingAs($admin)
            ->post(route('platform.campuses.applications.approve', ['application' => $application]))
            ->assertSessionHasNoErrors();

        $application->refresh();
        $this->assertSame('approved', $application->status);
        $this->assertNotNull($application->campus_id);
        $token = null;
        Mail::assertSent(CampusAdminInvitation::class, function (CampusAdminInvitation $mail) use (&$token): bool {
            $token = $mail->token;

            return true;
        });
        $setup = route('campus-applications.setup', [
            'application' => $application,
            'token' => $token,
        ]);

        $this->get($setup)->assertOk();
        $this->post($setup, [
            'name' => 'Admin Kampus Contoh',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('campus.home', ['campus' => $application->campus->slug]));

        $this->assertDatabaseHas('users', [
            'email' => 'applicant@example.test',
            'role' => 'admin',
            'campus_id' => $application->campus_id,
        ]);

        $this->post($setup, [
            'name' => 'Admin Kedua',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('campuses.index'));
    }

    private function category(): Category
    {
        return Category::query()->firstOrCreate(
            ['slug' => 'lainnya'],
            ['name' => 'Lainnya', 'icon' => 'box']
        );
    }

    private function foundItem(?User $owner = null, string $title = 'Barang temuan'): Item
    {
        $owner ??= User::factory()->create();

        return Item::query()->create([
            'user_id' => $owner->id,
            'category_id' => $this->category()->id,
            'type' => 'found',
            'title' => $title,
            'description' => 'Deskripsi internal barang temuan.',
            'location' => 'Perpustakaan',
            'storage_location' => 'Pos satpam',
            'date_event' => now()->toDateString(),
            'security_question' => 'Apa warna label?',
            'security_answer' => 'label biru',
            'status' => 'pending',
        ]);
    }
}
