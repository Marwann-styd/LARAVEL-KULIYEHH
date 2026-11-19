<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')
            ->latest()
            ->paginate(10);

        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_code' => [
                'required',
                'string',
                'max:20',
                'unique:categories,category_code',
            ],
            'category_name' => [
                'required',
                'string',
                'max:100',
            ],
            'description' => [
                'nullable',
                'string',
                'max:500',
            ],
        ]);

        Category::create([
            'category_code' => strtoupper($validated['category_code']),
            'category_name' => $validated['category_name'],
            'description' => $validated['description'] ?? null,
            'is_active' => true,
        ]);

        return redirect()->route('categories.index')
            ->with('success', 'Kategori produk berhasil ditambahkan.');
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'category_code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('categories', 'category_code')
                    ->ignore($category->id),
            ],
            'category_name' => [
                'required',
                'string',
                'max:100',
            ],
            'description' => [
                'nullable',
                'string',
                'max:500',
            ],
        ]);

        $category->update([
            'category_code' => strtoupper($validated['category_code']),
            'category_name' => $validated['category_name'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('categories.index')
            ->with('success', 'Kategori produk berhasil diperbarui.');
    }

    public function toggleStatus(Category $category)
    {
        $category->update([
            'is_active' => !$category->is_active,
        ]);

        return redirect()->route('categories.index')
            ->with('success', 'Status kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->exists()) {
            return redirect()->route('categories.index')
                ->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh produk.');
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}
