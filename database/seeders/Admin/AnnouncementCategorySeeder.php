<?php

namespace Database\Seeders\Admin;

use App\Models\Frontend\AnnouncementCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AnnouncementCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $announcement_categories = array(
            array('id' => '6', 'name' => '{"language":{"en":{"name":"Blog"},"fr":{"name":null},"es":{"name":null},"de":{"name":null},"ru":{"name":null},"tr":{"name":null},"ar":{"name":null},"hi":{"name":null},"ur":{"name":null},"cn":{"name":null},"ig-ng":{"name":null}}}', 'status' => '1', 'created_at' => '2024-11-06 10:22:17', 'updated_at' => '2024-11-06 10:22:17'),
            array('id' => '7', 'name' => '{"language":{"en":{"name":"Booking Car"},"fr":{"name":null},"es":{"name":null},"de":{"name":null},"ru":{"name":null},"tr":{"name":null},"ar":{"name":null},"hi":{"name":null},"ur":{"name":null},"cn":{"name":null},"ig-ng":{"name":null}}}', 'status' => '1', 'created_at' => '2024-11-06 10:22:39', 'updated_at' => '2024-11-06 10:22:39'),
            array('id' => '8', 'name' => '{"language":{"en":{"name":"Car Information"},"fr":{"name":null},"es":{"name":null},"de":{"name":null},"ru":{"name":null},"tr":{"name":null},"ar":{"name":null},"hi":{"name":null},"ur":{"name":null},"cn":{"name":null},"ig-ng":{"name":null}}}', 'status' => '1', 'created_at' => '2024-11-06 10:22:50', 'updated_at' => '2024-11-06 10:22:50'),
            array('id' => '9', 'name' => '{"language":{"en":{"name":"People Saying"},"fr":{"name":null},"es":{"name":null},"de":{"name":null},"ru":{"name":null},"tr":{"name":null},"ar":{"name":null},"hi":{"name":null},"ur":{"name":null},"cn":{"name":null},"ig-ng":{"name":null}}}', 'status' => '1', 'created_at' => '2024-11-06 10:23:02', 'updated_at' => '2024-11-06 10:23:02'),
            array('id' => '10', 'name' => '{"language":{"en":{"name":"Appointment"},"fr":{"name":null},"es":{"name":null},"de":{"name":null},"ru":{"name":null},"tr":{"name":null},"ar":{"name":null},"hi":{"name":null},"ur":{"name":null},"cn":{"name":null},"ig-ng":{"name":null}}}', 'status' => '1', 'created_at' => '2024-11-06 10:23:11', 'updated_at' => '2024-11-06 10:23:11')
        );

        AnnouncementCategory::insert($announcement_categories);
    }
}
