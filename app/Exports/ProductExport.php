<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;


class ProductExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Product::with('brand')->get()->map(function ($product) {
            return [
                'ID' => $product->id,
                'Name' => $product->name,
                'Price' => $product->price,
                'Brand' => $product->brand ? $product->brand->name : 'N/A',
            ];
        });
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Price', 'Brand'];
    }
}
