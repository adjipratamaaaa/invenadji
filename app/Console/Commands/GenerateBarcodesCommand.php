<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Milon\Barcode\DNS1D;

class GenerateBarcodesCommand extends Command
{
    protected $signature = 'barcodes:generate';
    protected $description = 'Generate barcodes for all products that don\'t have one';

    public function handle()
    {
        $productsWithoutBarcode = Product::whereNull('barcode')->get();
        
        $this->info("Found {$productsWithoutBarcode->count()} products without barcode");
        
        $bar = $this->output->createProgressBar($productsWithoutBarcode->count());
        $bar->start();

        foreach ($productsWithoutBarcode as $product) {
            // Generate barcode unik
            do {
                $barcode = 'PC' . rand(10000000, 99999999);
            } while (Product::where('barcode', $barcode)->exists());

            // Update product
            $product->update([
                'barcode' => $barcode,
                'barcode_type' => 'C128'
            ]);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Successfully generated barcodes for all products!');
        
        return Command::SUCCESS;
    }
}