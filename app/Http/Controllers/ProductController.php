<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')
            ->withCount('storagePlacements')
            ->oldest()
            ->paginate(10);

        $totalAktif = product::where('is_active', true)->count();

        $totalNonaktif = product::where('is_active', false)->count();

        return view('products.index', compact(
            'products' ,
            'totalAktif',
            'totalNonaktif'
        ));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)
            ->orderBy('category_name')
            ->get();

        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => [
                'required',
                'exists:categories,id',
            ],
            'product_code' => [
                'required',
                'string',
                'max:50',
                'unique:products,product_code',
            ],
            'product_name' => [
                'required',
                'string',
                'max:150',
            ],
            'cartons_per_pallet' => [
                'required',
                'integer',
                'min:1',
            ],
            'description' => [
                'nullable',
                'string',
                'max:500',
            ],
        ]);

        $category = Category::findOrFail($validated['category_id']);

        if (!$category->is_active) {
            return back()->withInput()->withErrors([
                'category_id' => 'Kategori yang dipilih sedang nonaktif.',
            ]);
        }

        Product::create([
            'category_id' => $validated['category_id'],
            'product_code' => strtoupper($validated['product_code']),
            'product_name' => $validated['product_name'],
            'cartons_per_pallet' => $validated['cartons_per_pallet'],
            'description' => $validated['description'] ?? null,
            'is_active' => true,
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)
            ->orWhere('id', $product->category_id)
            ->orderBy('category_name')
            ->get();

        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => [
                'required',
                'exists:categories,id',
            ],
            'product_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('products', 'product_code')
                    ->ignore($product->id),
            ],
            'product_name' => [
                'required',
                'string',
                'max:150',
            ],
            'cartons_per_pallet' => [
                'required',
                'integer',
                'min:1',
            ],
            'description' => [
                'nullable',
                'string',
                'max:500',
            ],
        ]);

        $category = Category::findOrFail($validated['category_id']);

        if (!$category->is_active) {
            return back()->withInput()->withErrors([
                'category_id' => 'Kategori yang dipilih sedang nonaktif.',
            ]);
        }

        $product->update([
            'category_id' => $validated['category_id'],
            'product_code' => strtoupper($validated['product_code']),
            'product_name' => $validated['product_name'],
            'cartons_per_pallet' => $validated['cartons_per_pallet'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Data produk berhasil diperbarui.');
    }

    public function toggleStatus(Product $product)
    {
        $product->update([
            'is_active' => !$product->is_active,
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Status produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        if (
            $product->storagePlacements()->exists() ||
            $product->storageTransactions()->exists()
        ) {
            return redirect()->route('products.index')
                ->with('error', 'Produk tidak dapat dihapus karena sudah memiliki data penyimpanan atau transaksi.');
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}
