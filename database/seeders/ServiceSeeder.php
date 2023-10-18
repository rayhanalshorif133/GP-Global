<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function __construct()
    {
        $this->run();
    }

    public function run()
    {
        
            $service = new Service();
            $service->name = 'Game Thief';
            $service->keyword = 'GT';
            $service->type = 'subscription';
            $service->validity = 'daily';
            $service->redirect_url = 'https://www.google.com/';
            $service->charge = 10.00;
            $service->save();

    }

}
