<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('admin.categories', [
            'categories' => Category::query()->withCount('items')->orderBy('name')->paginate(20),
        ]);
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        Category::create($request->validated());

        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(StoreCategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update($request->validated());

        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        abort_unless(in_array(request()->user()->role, ['admin', 'satpam'], true), 403);
        abort_if($category->items()->exists(), 422, 'Kategori yang sudah digunakan tidak dapat dihapus.');

        $category->delete();

        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}
