<?php

namespace App\Exports;

use App\Models\Product;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;

class ProductsExport implements WithHeadings,WithMapping, WithColumnFormatting
{
    use Exportable;
    /**
    * @var Product $Product
    */
    public function map($product): array
    {
        return [
            $product->product_name,
            $product->regular_price,
            $product->sale_price,
            $product->stock,
            $product->product_sku,
        ];
    }

    public function headings(): array
    {
        return [
            'Product Name',
            'Regular Price',
            'Sale Price',
            'Stock',
            'Product Sku',
        ];
    }

    /**
     * @return array
     */
    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE,
            'C' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE,
        ];
    }
}
