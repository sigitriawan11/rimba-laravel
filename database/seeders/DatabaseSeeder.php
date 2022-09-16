<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Item;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Item::create([
            'kode_item'  => 'A0001',
            'nama_item' => 'Beras',
            'unit' => 'kg',
            'stok' => 16,
            'harga_satuan' => 8500,
            'barang' => 'Test.jpg'
        ]);
    }
}
