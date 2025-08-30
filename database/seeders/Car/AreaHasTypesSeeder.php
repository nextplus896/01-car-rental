<?php

namespace Database\Seeders\Car;

use App\Models\Admin\Cars\AreaHasType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AreaHasTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array(
            array('id' => '1', 'car_area_id' => '1', 'car_type_id' => '1', 'created_at' => '2024-11-26 06:17:21', 'updated_at' => NULL),
            array('id' => '2', 'car_area_id' => '1', 'car_type_id' => '3', 'created_at' => '2024-11-26 06:17:21', 'updated_at' => NULL),
            array('id' => '3', 'car_area_id' => '2', 'car_type_id' => '2', 'created_at' => '2024-11-26 06:17:40', 'updated_at' => NULL),
            array('id' => '4', 'car_area_id' => '2', 'car_type_id' => '3', 'created_at' => '2024-11-26 06:17:40', 'updated_at' => NULL),
            array('id' => '5', 'car_area_id' => '3', 'car_type_id' => '2', 'created_at' => '2024-11-26 06:17:50', 'updated_at' => NULL)
        );

        AreaHasType::insert($data);
    }
}
