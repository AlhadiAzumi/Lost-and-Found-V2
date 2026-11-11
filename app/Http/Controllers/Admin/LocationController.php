<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLocationRequest;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function index(): View
    {
        return view('admin.locations', [
            'locations' => Location::query()->withCount('items')->orderBy('name')->paginate(20),
        ]);
    }

    public function store(StoreLocationRequest $request): RedirectResponse
    {
        Location::create($request->validated());

        return back()->with('success', 'Lokasi berhasil ditambahkan.');
    }

    public function update(StoreLocationRequest $request, Location $location): RedirectResponse
    {
        $location->update($request->validated());
        $location->items()->update(['location' => $location->name]);

        return back()->with('success', 'Lokasi berhasil diperbarui.');
    }

    public function destroy(Location $location): RedirectResponse
    {
        abort_unless(in_array(request()->user()->role, ['admin', 'satpam'], true), 403);
        abort_if($location->items()->exists(), 422, 'Lokasi yang masih digunakan tidak dapat dihapus.');

        $location->delete();

        return back()->with('success', 'Lokasi berhasil dihapus.');
    }
}
