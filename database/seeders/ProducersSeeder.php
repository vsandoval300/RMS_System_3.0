<?php

namespace Database\Seeders;

use App\Models\producer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class producersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $producer = new producer();
        $producer->name = 'Direct';
        $producer->acronym = 'DIR';
        $producer->save();

        $producer = new producer();
        $producer->name = 'Carpio Group';
        $producer->acronym = 'CAR';
        $producer->save();

        $producer = new producer();
        $producer->name = 'Dynamic';
        $producer->acronym = 'DYN';
        $producer->save();

        $producer = new producer();
        $producer->name = 'Everest';
        $producer->acronym = 'EVE';
        $producer->save();

        $producer = new producer();
        $producer->name = 'Pacific';
        $producer->acronym = 'PAC';
        $producer->save();

        $producer = new producer();
        $producer->name = 'Patria';
        $producer->acronym = 'PAT';
        $producer->save();

        $producer = new producer();
        $producer->name = 'Rainmaker Group';
        $producer->acronym = 'RMK';
        $producer->save();

        $producer = new producer();
        $producer->name = 'Reasinter';
        $producer->acronym = 'REA';
        $producer->save();

        $producer = new producer();
        $producer->name = 'Tokio_MK';
        $producer->acronym = 'TOK';
        $producer->save();

        $producer = new producer();
        $producer->name = 'XS-Global';
        $producer->acronym = 'XSG';
        $producer->save();

        $producer = new producer();
        $producer->name = 'Eureka Re';
        $producer->acronym = 'EUK';
        $producer->save();
    }
}
