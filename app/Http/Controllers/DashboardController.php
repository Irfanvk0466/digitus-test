<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Inertia\Inertia;


class DashboardController extends Controller
{
    /**
     * Display the dashboard with the latest products.
     */
    public function index()
    {
        $products = Product::with('brand')->latest()->paginate(6);
        return Inertia::render('Dashboard', ['products' => $products]);
    }
}
