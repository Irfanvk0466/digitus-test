<?php

namespace App\Http\Controllers;

use App\Exports\ProductExport;
use App\Http\Requests\ProductImportRequest;
use App\Models\Brand;
use App\Models\Product;
use Inertia\Inertia;
use App\Http\Requests\ProductRequest;
use App\Imports\ProductImport;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;



class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with('brand')->paginate(6);
        return Inertia::render('Frontend/Product/Index', ['products' => $products]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $brands = Brand::all();
        return Inertia::render('Frontend/Product/Create', ['brands' => $brands]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
       Product::create($request->validated());
       return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::with('brand')->findOrFail($id);
        return Inertia::render('Frontend/Product/Show', ['product' => $product]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = Product::findOrFail($id);
        $brands = Brand::all();
        return Inertia::render('Frontend/Product/Edit', ['product' => $product, 'brands' => $brands]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, string $id)
    {
        $product = Product::findOrFail($id);
        $product->update($request->validated());
        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }

    /**
     * Import products from an Excel file.
     */
    public function import(ProductImportRequest $request)
    {
        try {
            Excel::import(new ProductImport, $request->file('file'));
            return redirect()->route('products.index')->with('success', 'Products imported successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error importing file: ' . $e->getMessage());
        }
    }

    /**
     * Export products as an Excel file.
     */
    public function export()
    {
        return Excel::download(new ProductExport, 'products.xlsx');
    }
}
