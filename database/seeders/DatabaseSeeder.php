<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\User\UserSeeder;
use Database\Seeders\Admin\RoleSeeder;
use Database\Seeders\Admin\AdminSeeder;
use Database\Seeders\Admin\CurrencySeeder;
use Database\Seeders\Admin\LanguageSeeder;
use Database\Seeders\Admin\SetupKycSeeder;
use Database\Seeders\Admin\SetupSeoSeeder;
use Database\Seeders\Admin\ExtensionSeeder;
use Database\Seeders\Admin\SetupPageSeeder;
use Database\Seeders\Admin\UsefulLinkSeeder;
use Database\Seeders\Admin\AppSettingsSeeder;
use Database\Seeders\Admin\AdminHasRoleSeeder;
use Database\Seeders\Admin\AnnouncementCategorySeeder;
use Database\Seeders\Admin\AnnouncementSeeder;
use Database\Seeders\Admin\AppOnBoardSeeder;
use Database\Seeders\Admin\SiteSectionsSeeder;
use Database\Seeders\Admin\BasicSettingsSeeder;
use Database\Seeders\Admin\PaymentGatewaySeeder;
use Database\Seeders\Admin\SystemMaintenanceSeeder;
use Database\Seeders\Admin\TransactionSettingSeeder;
use Database\Seeders\Admin\VendorAppOnBoardScreenSeeder;
use Database\Seeders\Car\AreaHasTypesSeeder;
use Database\Seeders\Car\AreaSeeder;
use Database\Seeders\Car\CarSeeder;
use Database\Seeders\Car\TypeSeeder;
use Database\Seeders\Vendor\VendorSeeder;
use Database\Seeders\Vendor\VendorWalletSeeder;
use Database\Seeders\FreshSeeder\BasicSettingsSeeder as FreshBasicSettings;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // demo seeder
        // $this->call([
        //     AdminSeeder::class,
        //     RoleSeeder::class,
        //     TransactionSettingSeeder::class,
        //     AnnouncementCategorySeeder::class,
        //     AnnouncementSeeder::class,
        //     CurrencySeeder::class,
        //     BasicSettingsSeeder::class,
        //     SetupSeoSeeder::class,
        //     AppSettingsSeeder::class,
        //     AppOnBoardSeeder::class,
        //     SiteSectionsSeeder::class,
        //     SetupKycSeeder::class,
        //     ExtensionSeeder::class,
        //     AdminHasRoleSeeder::class,
        //     UserSeeder::class,
        //     VendorSeeder::class,
        //     VendorWalletSeeder::class,
        //     SetupPageSeeder::class,
        //     LanguageSeeder::class,
        //     UsefulLinkSeeder::class,
        //     PaymentGatewaySeeder::class,
        //     AreaSeeder::class,
        //     TypeSeeder::class,
        //     AreaHasTypesSeeder::class,
        //     CarSeeder::class,
        //     SystemMaintenanceSeeder::class,
        //     VendorAppOnBoardScreenSeeder::class,
        // ]);


        $this->call([
            AdminSeeder::class,
            RoleSeeder::class,
            TransactionSettingSeeder::class,
            AnnouncementCategorySeeder::class,
            AnnouncementSeeder::class,
            CurrencySeeder::class,
            FreshBasicSettings::class,
            SetupSeoSeeder::class,
            AppSettingsSeeder::class,
            SiteSectionsSeeder::class,
            SetupKycSeeder::class,
            ExtensionSeeder::class,
            AdminHasRoleSeeder::class,
            AppOnBoardSeeder::class,
            SetupPageSeeder::class,
            LanguageSeeder::class,
            UsefulLinkSeeder::class,
            PaymentGatewaySeeder::class,
            VendorAppOnBoardScreenSeeder::class,
            SystemMaintenanceSeeder::class,
        ]);
    }
}
