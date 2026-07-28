<?php


namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductsExport implements FromCollection, WithHeadings, WithMapping
{
    /** @var int|string|null Current branch id, or 'all'/null for every branch. */
    protected $branchId;

    public function __construct($branchId = null)
    {
        $this->branchId = $branchId;
    }

    public function collection()
    {
        // Export only the current branch's products (client #5). 'all'/null =
        // no filter, so an unlocked admin viewing all branches still gets everything.
        $query = Product::with('category');

        if ($this->branchId && $this->branchId !== 'all') {
            $query->where('branch_id', $this->branchId);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Name',
            'Barcode',
            'Category',
            'Description',
            'Price',
            'Cost Price',
            'Stock Quantity',
            'Reorder Level',
            'Active'
        ];
    }

    public function map($product): array
    {
        return [
            $product->name,
            $product->barcode,
            $product->category->name,
            $product->description,
            $product->price,
            $product->cost_price,
            $product->stock_quantity,
            $product->reorder_level,
            $product->is_active ? 'Yes' : 'No'
        ];
    }
}
