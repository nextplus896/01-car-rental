<?php

namespace Database\Seeders\Car;

use App\Models\Admin\Cars\CarArea;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array(
            array('id' => '1', 'name' => 'New York', 'slug' => 'new-york', 'status' => '1', 'last_edit_by' => '1', 'created_at' => '2024-11-26 06:17:21', 'updated_at' => '2024-11-26 06:17:21'),
            array('id' => '2', 'name' => 'Chicago', 'slug' => 'chicago', 'status' => '1', 'last_edit_by' => '1', 'created_at' => '2024-11-26 06:17:40', 'updated_at' => '2024-11-26 06:17:40'),
            array('id' => '3', 'name' => 'Las Vegas', 'slug' => 'las-vegas', 'status' => '1', 'last_edit_by' => '1', 'created_at' => '2024-11-26 06:17:50', 'updated_at' => '2024-11-26 06:17:50')
        );

        CarArea::insert($data);
    }
}
