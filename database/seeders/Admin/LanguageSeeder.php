<?php

namespace Database\Seeders\Admin;

use App\Models\Admin\Language;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'name'          => "English",
                'code'          => "en",
                'status'        => true,
                'last_edit_by'  => 1,
                'created_at'    => now(),
                'dir'           => "ltr",
            ],
            [
                'name'          => "French",
                'code'          => "fr",
                'status'        => false,
                'last_edit_by'  => 1,
                'created_at'    => now(),
                'dir'           => "ltr",
            ],
            [
                'name'          => "Spanish",
                'code'          => "es",
                'status'        => false,
                'last_edit_by'  => 1,
                'created_at'    => now(),
                'dir'           => "ltr",
            ],
            [
                'name'          => "Arabic",
                'code'          => "ar",
                'status'        => false,
                'last_edit_by'  => 1,
                'created_at'    => now(),
                'dir'           => "rtl",
            ],
        ];

        Language::upsert($data,['code'],[]);
    }
}
