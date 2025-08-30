<?php

namespace Database\Seeders\Car;

use App\Models\Vendor\Cars\Car;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['id' => '1', 'vendor_id' => '1', 'car_area_id' => '2', 'car_type_id' => '3', 'slug' => '53830420-1f87-4199-b879-a4579b18b42c', 'car_title' => '{"en":{"car_title":"hello"},"fr":{"car_title":"ki"},"es":{"car_title":"kill"},"ar":{"car_title":"aki"}}', 'car_model' => 'Toyota', 'seat' => '4', 'experience' => '4', 'car_number' => 'K-911', 'fees' => '20.00000000', 'image' => '58478a6a-468d-4f55-84ac-00cb7df05945.webp', 'status' => '1', 'approval' => '1', 'created_at' => '2025-01-08 07:23:34', 'updated_at' => '2025-02-03 04:42:06'],
            ['id' => '2', 'vendor_id' => '1', 'car_area_id' => '3', 'car_type_id' => '2', 'slug' => '083a1311-794e-4de8-a1a3-695b65a9563d', 'car_title' => '{"en":{"car_title":"hello"},"fr":{"car_title":"ki"},"es":{"car_title":"kill"},"ar":{"car_title":"aki"}}', 'car_model' => 'Sedan', 'seat' => '4', 'experience' => '4', 'car_number' => 'K-187', 'fees' => '25.00000000', 'image' => '50b2cbca-a0c4-420b-bea8-b1588ca3ebbc.webp', 'status' => '1', 'approval' => '1', 'created_at' => '2025-01-08 07:24:46', 'updated_at' => '2025-02-03 04:42:24'],
            ['id' => '3', 'vendor_id' => '1', 'car_area_id' => '1', 'car_type_id' => '3', 'slug' => 'c9ce8095-0d61-4c61-8a71-3531cb8569ba', 'car_title' => '{"en":{"car_title":"hello"},"fr":{"car_title":"ki"},"es":{"car_title":"kill"},"ar":{"car_title":"aki"}}', 'car_model' => 'Ford', 'seat' => '6', 'experience' => '4', 'car_number' => 'J-878', 'fees' => '35.00000000', 'image' => '96c3cfc9-5bd9-4f59-941a-5d7034caa88f.webp', 'status' => '1', 'approval' => '1', 'created_at' => '2025-01-08 07:25:43', 'updated_at' => '2025-02-03 04:42:51'],
            ['id' => '4', 'vendor_id' => '2', 'car_area_id' => '2', 'car_type_id' => '3', 'slug' => '18d94a85-b5df-47c6-9bce-9e6e2958c1d8', 'car_title' => '{"en":{"car_title":"hello"},"fr":{"car_title":"ki"},"es":{"car_title":"kill"},"ar":{"car_title":"aki"}}', 'car_model' => 'Ford', 'seat' => '6', 'experience' => '5', 'car_number' => 'F-104', 'fees' => '20.00000000', 'image' => '74825a4b-cb68-44ab-bdd7-b4ac2946bcfa.webp', 'status' => '1', 'approval' => '1', 'created_at' => '2025-01-08 09:10:23', 'updated_at' => '2025-01-08 09:12:58'],
            ['id' => '5', 'vendor_id' => '2', 'car_area_id' => '3', 'car_type_id' => '2', 'slug' => '634174c1-a34d-4c65-b2b6-0ef747d24ebc', 'car_title' => '{"en":{"car_title":"hello"},"fr":{"car_title":"ki"},"es":{"car_title":"kill"},"ar":{"car_title":"aki"}}', 'car_model' => 'Ford', 'seat' => '4', 'experience' => '2', 'car_number' => 'F-828', 'fees' => '60.00000000', 'image' => 'a9b9d6a8-b4c7-4116-ba41-41297b918ab3.webp', 'status' => '1', 'approval' => '1', 'created_at' => '2025-01-08 09:11:13', 'updated_at' => '2025-01-08 09:12:58'],
            ['id' => '6', 'vendor_id' => '2', 'car_area_id' => '1', 'car_type_id' => '1', 'slug' => '583e8422-c187-4cc3-b76c-42fe4af4881c', 'car_title' => '{"en":{"car_title":"hello"},"fr":{"car_title":"ki"},"es":{"car_title":"kill"},"ar":{"car_title":"aki"}}', 'car_model' => 'KIA-12', 'seat' => '4', 'experience' => '5', 'car_number' => 'k-987', 'fees' => '80.00000000', 'image' => 'e06c51c2-7043-4223-ac63-b0fdc31fa74c.webp', 'status' => '1', 'approval' => '1', 'created_at' => '2025-01-08 09:12:24', 'updated_at' => '2025-01-08 09:12:56'],
            ['id' => '7', 'vendor_id' => '2', 'car_area_id' => '1', 'car_type_id' => '3', 'slug' => 'a1f61317-3501-4bd4-b102-e784ae92eb15', 'car_title' => '{"en":{"car_title":"hello"},"fr":{"car_title":"ki"},"es":{"car_title":"kill"},"ar":{"car_title":"aki"}}', 'car_model' => 'Jeep-44', 'seat' => '6', 'experience' => '5', 'car_number' => 'j-696', 'fees' => '90.00000000', 'image' => '048d57f8-3f44-44ab-ad9d-64bca2469e39.webp', 'status' => '1', 'approval' => '1', 'created_at' => '2025-01-08 09:13:55', 'updated_at' => '2025-01-08 09:14:09'],
        ];

        Car::insert($data);
    }
}
