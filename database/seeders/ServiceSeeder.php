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
        $service->name = 'Bd gamers';
        $service->keyword = 'BDG';
        $service->amount = 5.00;
        $service->type = 'subscription';
        $service->validity = 'P1D';
        $service->redirect_url = 'http://bdgamers.club/';
        $service->description = 'Bd gamers';
        $service->save();

        $service = new Service();
        $service->name = 'Gajal';
        $service->keyword = 'GAJAL';
        $service->type = 'subscription';
        $service->validity = 'P1D';
        $service->redirect_url = 'http://gajal.b2mwap.com/index.php/home/home';
        $service->description = 'Gajal';
        $service->amount = 5.00;
        $service->save();

        $service = new Service();
        $service->name = 'Sports Fan news';
        $service->keyword = 'SFN';
        $service->type = 'subscription';
        $service->validity = 'P1D';
        $service->redirect_url = 'http://gajal.b2mwap.com/index.php/home/home';
        $service->description = 'Sports Fan news';
        $service->amount = 5.00;
        $service->save();
    }

}
