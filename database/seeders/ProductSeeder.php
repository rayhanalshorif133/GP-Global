<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    
    public function __construct()
    {
        $this->run();
    }

    public function run()
    {
        
        $product = new Product();
        $product->service_id = 1;
        $product->name = 'Product 1';
        $product->description = 'Product 1 des';
        $product->save();

        $product = new Product();
        $product->service_id = 1;
        $product->name = 'Product 2';
        $product->description = 'Product 2 des';
        $product->save();
    }
}
