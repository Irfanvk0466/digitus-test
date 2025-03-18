<?php

namespace App\Imports;

use App\Models\Brand;
use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class ProductImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Product([
            'name'     => trim($row['name']),
            'price'    => trim($row['price']),
            'brand_id' => Brand::where('name', trim($row['brand']))->value('id'),
        ]);
    }
}
