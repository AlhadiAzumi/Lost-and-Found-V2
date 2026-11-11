<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompleteHandoverRequest;
use App\Http\Requests\ModerateItemRequest;
use App\Http\Requests\VerifyClaimRequest;
use App\Models\Claim;
use App\Models\Handover;
use App\Models\Item;
use App\Services\ClaimService;
use App\Services\HandoverService;
use App\Services\ItemService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminWorkflowController extends Controller
{
    public function dashboard(): View
    {
        $totalItems = Item::query()->count();
        $returnedFound = Item::query()->where('type', 'found')->where('status', 'returned')->count();
        $totalFound = Item::query()->where('type', 'found')->count();
        $stats = [
            'total_items' => $totalItems,
            'lost_items' => Item::query()->where('type', 'lost')->count(),
            'found_items' => $totalFound,
            'pending_claims' => Claim::query()->where('status', 'pending')->count(),
            'returned_items' => $returnedFound,
            'returned_percentage' => $totalFound === 0 ? 0 : round(($returnedFound / $totalFound) * 100, 1),
        ];

        return view('dashboard', [
            'stats' => $stats,
            'recentReports' => Item::query()->with(['category', 'user'])->latest()->limit(5)->get(),
            'recentClaims' => Claim::query()->with(['item', 'user'])->where('status', 'pending')->latest()->limit(5)->get(),
        ]);
    }

    public function reports(): View
    {
        return view('admin.reports', [
            'items' => Item::query()->with(['category', 'user'])->latest()->paginate(12),
        ]);
    }

    public function moderate(
        ModerateItemRequest $request,
        Item $item,
        ItemService $service
    ): RedirectResponse {
        $this->authorize('moderate', Item::class);
        $service->moderate(
            $item,
            $request->user(),
            $request->validated('status'),
            $request->validated('rejection_reason')
        );

        return back()->with('success', 'Status laporan berhasil diperbarui.');
    }

    public function claims(): View
    {
        return view('admin.claims', [
            'claims' => Claim::query()
                ->with(['item', 'user', 'verifier'])
                ->latest()
                ->paginate(12),
        ]);
    }

    public function verify(
        VerifyClaimRequest $request,
        Claim $claim,
        ClaimService $service
    ): RedirectResponse {
        $this->authorize('verify', $claim);
        $updated = $service->decide(
            $claim,
            $request->user(),
            $request->validated('action'),
            $request->validated('admin_note')
        );

        return back()->with(
            'success',
            $updated->status === 'approved'
                ? "Klaim disetujui. Kode serah terima: {$updated->handover_code}"
                : 'Klaim berhasil ditolak.'
        );
    }

    public function handovers(Request $request): View
    {
        $claimToHandover = null;
        $code = trim((string) $request->query('code', ''));

        if ($code !== '') {
            $claimToHandover = Claim::query()
                ->with(['item', 'user', 'handover'])
                ->where('handover_code', $code)
                ->first();
        }

        return view('admin.handovers', [
            'claimToHandover' => $claimToHandover,
            'searchCode' => $code,
            'handovers' => Handover::query()
                ->with(['item', 'recipient', 'officer'])
                ->latest('handover_date')
                ->paginate(12),
        ]);
    }

    public function processHandover(
        CompleteHandoverRequest $request,
        Claim $claim,
        HandoverService $service
    ): RedirectResponse {
        $service->complete(
            $claim,
            $request->user(),
            $request->validated('code'),
            $request->validated('notes')
        );

        return redirect()
            ->route('campus.admin.handovers', ['campus' => $claim->campus->slug])
            ->with('success', 'Serah terima berhasil dicatat dan barang berstatus dikembalikan.');
    }
}
