<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClaimRequest;
use App\Models\Claim;
use App\Models\Item;
use App\Services\ClaimService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ClaimController extends Controller
{
    public function create(Item $item): View
    {
        $this->authorize('create', [Claim::class, $item]);

        return view('claims.create', compact('item'));
    }

    public function store(StoreClaimRequest $request, Item $item, ClaimService $service): RedirectResponse
    {
        $this->authorize('create', [Claim::class, $item]);

        $claim = $service->submit(
            $item,
            $request->user(),
            $request->validated('security_answer'),
            $request->file('proof_image')
        );

        return redirect()
            ->route('campus.my.history', ['campus' => $claim->campus->slug, 'tab' => 'claims'])
            ->with('success', 'Pengajuan klaim berhasil dikirim dan sedang menunggu verifikasi petugas.');
    }

    public function proof(Claim $claim)
    {
        abort_unless(
            in_array(request()->user()?->role, ['admin', 'satpam'], true),
            403,
            'Bukti kepemilikan hanya dapat dilihat oleh petugas.'
        );

        abort_unless(filled($claim->proof_image), 404);
        abort_unless(Storage::disk('local')->exists($claim->proof_image), 404);

        return Storage::disk('local')->response($claim->proof_image);
    }
}
