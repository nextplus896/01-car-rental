<?php

namespace Database\Seeders\Car;

use App\Models\Admin\Cars\CarType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array(
            array('id' => '1', 'slug' => 'sedan', 'name' => 'Sedan', 'status' => '1', 'last_edit_by' => '1', 'created_at' => '2024-11-26 06:16:24', 'updated_at' => '2024-11-26 06:16:24'),
            array('id' => '2', 'slug' => 'car', 'name' => 'Car', 'status' => '1', 'last_edit_by' => '1', 'created_at' => '2024-11-26 06:16:36', 'updated_at' => '2024-11-26 06:16:36'),
            array('id' => '3', 'slug' => 'suv', 'name' => 'Suv', 'status' => '1', 'last_edit_by' => '1', 'created_at' => '2024-11-26 06:16:44', 'updated_at' => '2024-11-26 06:16:44')
        );

        CarType::insert($data);
    }
}
