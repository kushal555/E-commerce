<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Auth;

class ProductsImport implements ToModel , WithValidation, WithHeadingRow,SkipsOnFailure
{
    use Importable,SkipsFailures;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Product([
            'product_name' => $row['product_name'],
            'regular_price' => $row['regular_price'] ,
            'sale_price' => $row['sale_price'] ,
            'stock' => $row['stock'] ,
            'product_sku'=> $row['product_sku'] ,
            'user_id'=> Auth::user()->id,
        ]);
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function rules(): array
    {
        return [
            'product_name' => 'required',
            'regular_price' => 'required|regex:/^\d*(\.\d{1,2})?$/',
            'sale_price' => 'required|regex:/^\d*(\.\d{1,2})?$/',
            'stock' => 'required|numeric',
            'product_sku' => 'required',
        ];
    }
}
