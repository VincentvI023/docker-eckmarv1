<?php

use Illuminate\Database\Seeder;
use App\Category;
use App\Product;

class ProductSeeder extends Seeder
{
    private $jsonFilePath = 'products.json';

    public function run()
    {
        $start = microtime(true);
        
        $this->command->info('Reading JSON file...');
        $productsData = $this->readJsonFile();

        foreach ($productsData as $productData) {
            $this->createProduct($productData);
        }

        $end = (microtime(true) - $start);
        $this->command->info('Successfully created products from JSON. Elapsed time: ' . $this->formatTime($end));
    }

    private function readJsonFile()
    {
        $filePath = storage_path($this->jsonFilePath);
        if (!file_exists($filePath)) {
            $this->command->error('JSON file not found: ' . $filePath);
            return [];
        }

        $jsonContent = file_get_contents($filePath);
        return json_decode($jsonContent, true);
    }

    private function createProduct($data)
    {
        $this->command->info('Creating product: ' . $data['name']);

        $category = Category::firstOrCreate(['name' => $data['category']]);

        $newProduct = new Product();
        $newProduct->name = $data['name'];
        $newProduct->description = $data['description'];
        $newProduct->rules = $data['rules'];
        $newProduct->quantity = $data['quantity'];
        $newProduct->mesure = $data['mesure'];
        $newProduct->coins = $data['coins'];
        $newProduct->category_id = $category->id;
        $newProduct->user_id = \App\Vendor::inRandomOrder()->first()->id;
        $newProduct->save();

        if ($data['type'] === 'physical' && isset($data['physical'])) {
            $this->createPhysicalProduct($newProduct->id, $data['physical']);
        } elseif ($data['type'] === 'digital' && isset($data['digital'])) {
            $this->createDigitalProduct($newProduct->id, $data['digital']);
        }

        if (isset($data['shipping'])) {
            $this->createShipping($newProduct->id, $data['shipping']);
        }

        if (isset($data['offer'])) {
            $this->createOffer($newProduct->id, $data['offer']);
        }
    }

    private function createPhysicalProduct($productId, $data)
    {
        $physicalProduct = new \App\PhysicalProduct();
        $physicalProduct->id = $productId;
        $physicalProduct->countries_option = $data['countries_option'];
        $physicalProduct->countries = $data['countries'] ?? '';
        $physicalProduct->country_from = $data['country_from'];
        $physicalProduct->save();
    }

    private function createDigitalProduct($productId, $data)
    {
        $digitalProduct = new \App\DigitalProduct();
        $digitalProduct->id = $productId;
        $digitalProduct->autodelivery = $data['autodelivery'];
        $digitalProduct->content = $data['content'] ?? '';
        $digitalProduct->save();
    }

    private function createShipping($productId, $data)
    {
        $shipping = new \App\Shipping();
        $shipping->product_id = $productId;
        $shipping->name = $data['name'];
        $shipping->price = $data['price'];
        $shipping->duration = $data['duration'];
        $shipping->from_quantity = $data['from_quantity'];
        $shipping->to_quantity = $data['to_quantity'];
        $shipping->save();
    }

    private function createOffer($productId, $data)
    {
        $offer = new \App\Offer();
        $offer->product_id = $productId;
        $offer->min_quantity = $data['min_quantity'];
        $offer->price = $data['price'];
        $offer->save();
    }

    private function formatTime($s)
    {
        $h = floor($s / 3600);
        $s -= $h * 3600;
        $m = floor($s / 60);
        $s -= $m * 60;
        return $h . ':' . sprintf('%02d', $m) . ':' . sprintf('%02d', $s);
    }
}