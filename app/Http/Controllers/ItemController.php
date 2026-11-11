<?php

namespace App\Http\Controllers;

use App\Http\Requests\CatalogFilterRequest;
use App\Http\Requests\StoreItemReportRequest;
use App\Models\Category;
use App\Models\Item;
use App\Models\Location;
use App\Services\ItemService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ItemController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect()->route('catalog');
    }

    public function catalog(CatalogFilterRequest $request): View
    {
        $filters = $request->validated();
        $search = trim((string) ($filters['search'] ?? $filters['q'] ?? ''));
        $items = Item::query()
            ->with(['category', 'locationRecord'])
            ->publiclyVisible()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($searchQuery) use ($search): void {
                    $value = '%'.mb_strtolower($search).'%';
                    $searchQuery->whereRaw('LOWER(title) LIKE ?', [$value])
                        ->orWhereRaw('LOWER(description) LIKE ?', [$value]);
                });
            })
            ->when($filters['type'] ?? null, fn ($query, $type) => $query->where('type', $type))
            ->when($filters['category_id'] ?? null, fn ($query, $categoryId) => $query->where('category_id', $categoryId))
            ->when($filters['location'] ?? null, fn ($query, $location) => $query->where('location', $location))
            ->when($filters['from'] ?? null, fn ($query, $date) => $query->whereDate('date_event', '>=', $date))
            ->when($filters['to'] ?? null, fn ($query, $date) => $query->whereDate('date_event', '<=', $date))
            ->orderBy('date_event', ($filters['sort'] ?? 'terbaru') === 'terlama' ? 'asc' : 'desc')
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        return view('items.catalog', [
            'items' => $items,
            'categories' => Category::query()->orderBy('name')->get(),
            'locations' => Location::query()->orderBy('name')->get(),
            'filters' => $filters + ['search' => $search],
        ]);
    }

    public function create(Request $request): View
    {
        return view('items.create', [
            'categories' => Category::query()->orderBy('name')->get(),
            'locations' => Location::query()->orderBy('name')->get(),
            'item' => null,
            'defaultType' => $request->route('type', 'lost'),
        ]);
    }

    public function store(StoreItemReportRequest $request, ItemService $service): RedirectResponse
    {
        $item = $service->createReport(
            $request->validated(),
            $request->user(),
            $request->file('image')
        );

        return redirect()
            ->route('campus.items.show', ['campus' => $item->campus->slug, 'item' => $item])
            ->with('success', 'Laporan berhasil dikirim dan sedang menunggu moderasi petugas.');
    }

    public function show(Item $item): View
    {
        $user = request()->user();
        $isPublic = in_array($item->status, ['approved', 'claim_in_progress', 'returned'], true);

        abort_unless($isPublic || ($user !== null && $user->can('view', $item)), 404);

        $item->load(['category', 'locationRecord', 'user']);

        if ($item->type === 'found'
            && $item->user_id !== $user?->id
            && ! in_array($user?->role, ['admin', 'satpam'], true)) {
            $item->description = 'Rincian khusus barang hanya digunakan untuk verifikasi kepemilikan.';
        }

        if ($item->is_sensitive
            && $item->user_id !== $user?->id
            && ! in_array($user?->role, ['admin', 'satpam'], true)) {
            $item->description = 'Detail laporan ini disembunyikan karena ditandai sensitif.';
        }

        return view('items.show', compact('item'));
    }

    public function edit(Item $item): View
    {
        $this->authorize('update', $item);

        return view('items.create', [
            'categories' => Category::query()->orderBy('name')->get(),
            'locations' => Location::query()->orderBy('name')->get(),
            'item' => $item,
            'defaultType' => $item->type,
        ]);
    }

    public function update(StoreItemReportRequest $request, Item $item, ItemService $service): RedirectResponse
    {
        $this->authorize('update', $item);

        $updated = $service->updateReport(
            $item,
            $request->validated(),
            $request->file('image')
        );

        return redirect()
            ->route('campus.items.show', ['campus' => $updated->campus->slug, 'item' => $updated])
            ->with('success', 'Perubahan laporan berhasil disimpan dan menunggu moderasi ulang.');
    }

    public function close(Item $item, ItemService $service): RedirectResponse
    {
        $this->authorize('close', $item);
        $service->close($item, request()->user());

        return back()->with('success', 'Laporan barang hilang berhasil ditutup.');
    }
}
