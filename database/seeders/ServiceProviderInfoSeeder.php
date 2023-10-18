<?php

namespace Database\Seeders;

use App\Models\ServiceProviderInfo;
use Illuminate\Database\Seeder;

class ServiceProviderInfoSeeder extends Seeder
{
    public function __construct()
    {
        $this->run();
    }


    public function run()
    {
        
        $serviceProviderInfo = new ServiceProviderInfo();
        $serviceProviderInfo->username = 'b2mtech';
        $serviceProviderInfo->password = 'vCHth06udnTiQBsllDA4KEg147wVqlIt';
        $serviceProviderInfo->url = 'https://api.dob-staging.telenordigital.com';
        $serviceProviderInfo->operatorId = 'https://api.dob-staging.telenordigital.com';
        $serviceProviderInfo->save();
    }
}
