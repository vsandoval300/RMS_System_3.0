<?php

namespace Database\Seeders;

use App\Models\holding;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HoldingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $holding = new holding();
        $holding->name = 'Blenio Holdings S.A.';
        $holding->short_name = 'Blenio';
        $holding->country_id = '172';
        $holding->client_id = '3';
        $holding->save();

        $holding = new holding();
        $holding->name = 'Divan Holdings Corporation ';
        $holding->short_name = 'Divan';
        $holding->country_id = '172';
        $holding->client_id = '2';
        $holding->save();

        $holding = new holding();
        $holding->name = 'AVP Financial Limited';
        $holding->short_name = 'AVP';
        $holding->country_id = '235';
        $holding->client_id = '4';
        $holding->save();

        $holding = new holding();
        $holding->name = 'Bituaj Trust';
        $holding->short_name = 'Bituaj';
        $holding->country_id = '159';
        $holding->client_id = '4';
        $holding->save();

        $holding = new holding();
        $holding->name = 'MCH LUX III, S.A.R.L.';
        $holding->short_name = 'MCH';
        $holding->country_id = '131';
        $holding->client_id = '5';
        $holding->save();

        $holding = new holding();
        $holding->name = 'Colbert Industries LTD';
        $holding->short_name = 'Colbert';
        $holding->country_id = '114';
        $holding->client_id = '6';
        $holding->save();

        $holding = new holding();
        $holding->name = 'Bufner Investments Corp.';
        $holding->short_name = 'Bufner';
        $holding->country_id = '172';
        $holding->client_id = '7';
        $holding->save();

        $holding = new holding();
        $holding->name = 'Orion Beach Limited';
        $holding->short_name = 'Orion';
        $holding->country_id = '172';
        $holding->client_id = '2';
        $holding->save();

        $holding = new holding();
        $holding->name = 'Humber Holding Ltd.';
        $holding->short_name = 'Humber';
        $holding->country_id = '20';
        $holding->client_id = '8';
        $holding->save();

        $holding = new holding();
        $holding->name = 'Proteus Corporation';
        $holding->short_name = 'Proteus';
        $holding->country_id = '20';
        $holding->client_id = '10';
        $holding->save();

        $holding = new holding();
        $holding->name = 'Athos Holdings LLC';
        $holding->short_name = 'Athos';
        $holding->country_id = '236';
        $holding->client_id = '15';
        $holding->save();

        $holding = new holding();
        $holding->name = 'DM Investors, LLC';
        $holding->short_name = 'DM';
        $holding->country_id = '144';
        $holding->client_id = '28';
        $holding->save();

        $holding = new holding();
        $holding->name = 'Ardell Enterprises, LLC';
        $holding->short_name = 'Ardell';
        $holding->country_id = '236';
        $holding->client_id = '11';
        $holding->save();

        $holding = new holding();
        $holding->name = 'Ruston Enterprises, LLC';
        $holding->short_name = 'Ruston';
        $holding->country_id = '236';
        $holding->client_id = '11';
        $holding->save();

        $holding = new holding();
        $holding->name = 'Grand Stone Holdings Inc.';
        $holding->short_name = 'G Stone';
        $holding->country_id = '172';
        $holding->client_id = '9';
        $holding->save();

        $holding = new holding();
        $holding->name = 'Inversiones de Cinema de Costa Rica, S.A.';
        $holding->short_name = 'Inv. Cinema';
        $holding->country_id = '54';
        $holding->client_id = '5';
        $holding->save();

        $holding = new holding();
        $holding->name = 'Insurance Risk Management LLC';
        $holding->short_name = 'Ins. Risk Mtn';
        $holding->country_id = '244';
        $holding->client_id = '5';
        $holding->save();

        $holding = new holding();
        $holding->name = 'Toscana Holdings, LLC';
        $holding->short_name = 'Toscana';
        $holding->country_id = '236';
        $holding->client_id = '13';
        $holding->save();

        $holding = new holding();
        $holding->name = 'LM Supply LLC';
        $holding->short_name = 'LM Supply';
        $holding->country_id = '236';
        $holding->client_id = '18';
        $holding->save();

        $holding = new holding();
        $holding->name = 'Pantaleon Sugar Holdings Company Ltd.';
        $holding->short_name = 'Pantaleon';
        $holding->country_id = '172';
        $holding->client_id = '22';
        $holding->save();

        $holding = new holding();
        $holding->name = 'Leitka Commercial S.A.';
        $holding->short_name = 'Leitka';
        $holding->country_id = '172';
        $holding->client_id = '17';
        $holding->save();

        $holding = new holding();
        $holding->name = 'Invictus Re Solutions LLC';
        $holding->short_name = 'Invictus';
        $holding->country_id = '236';
        $holding->client_id = '20';
        $holding->save();

        $holding = new holding();
        $holding->name = 'Rainmaker Thor, LLC ';
        $holding->short_name = 'Rainmaker';
        $holding->country_id = '236';
        $holding->client_id = '1';
        $holding->save();

        $holding = new holding();
        $holding->name = 'Gatekeeper Thor, LLC ';
        $holding->short_name = 'Gatekeeper';
        $holding->country_id = '236';
        $holding->client_id = '1';
        $holding->save();

        $holding = new holding();
        $holding->name = 'L.I.T. Insurance Holdings, LLC';
        $holding->short_name = 'LIT';
        $holding->country_id = '236';
        $holding->client_id = '43';
        $holding->save();

        $holding = new holding();
        $holding->name = 'Petro Re Holding LLC';
        $holding->short_name = 'Petro';
        $holding->country_id = '236';
        $holding->client_id = '36';
        $holding->save();

        $holding = new holding();
        $holding->name = 'REC Reinsurance Ltd.';
        $holding->short_name = 'REC';
        $holding->country_id = '20';
        $holding->client_id = '42';
        $holding->save();

        $holding = new holding();
        $holding->name = 'The Central America Bottling Corporation';
        $holding->short_name = 'Bottling Corp';
        $holding->country_id = '243';
        $holding->client_id = '26';
        $holding->save();

        $holding = new holding();
        $holding->name = 'Caumed, LLC';
        $holding->short_name = 'Caumed';
        $holding->country_id = '236';
        $holding->client_id = '12';
        $holding->save();

        $holding = new holding();
        $holding->name = 'Cormach Investments Limited';
        $holding->short_name = 'Cormach';
        $holding->country_id = '235';
        $holding->client_id = '14';
        $holding->save();

        $holding = new holding();
        $holding->name = 'The San Miguel Trust';
        $holding->short_name = 'San Miguel';
        $holding->country_id = '159';
        $holding->client_id = '16';
        $holding->save();

        $holding = new holding();
        $holding->name = 'Icecore Finance Limited';
        $holding->short_name = 'Icecore';
        $holding->country_id = '243';
        $holding->client_id = '19';
        $holding->save();

        $holding = new holding();
        $holding->name = 'Jaguar Consulting DMCC';
        $holding->short_name = 'Jaguar';
        $holding->country_id = '234';
        $holding->client_id = '20';
        $holding->save();

        $holding = new holding();
        $holding->name = 'Leadehall Intermediary Services, Inc';
        $holding->short_name = 'Leadehall';
        $holding->country_id = '236';
        $holding->client_id = '21';
        $holding->save();

        $holding = new holding();
        $holding->name = 'OHS Limited';
        $holding->short_name = 'OHS';
        $holding->country_id = '189';
        $holding->client_id = '23';
        $holding->save();

        $holding = new holding();
        $holding->name = 'Corporación Gromeron, S.A. de C.V.';
        $holding->short_name = 'Gromeron';
        $holding->country_id = '144';
        $holding->client_id = '24';
        $holding->save();

        $holding = new holding();
        $holding->name = 'Ezas Inversiones, S.L.';
        $holding->short_name = 'Ezas';
        $holding->country_id = '210';
        $holding->client_id = '25';
        $holding->save();

        $holding = new holding();
        $holding->name = 'Dromaius Capital LLC';
        $holding->short_name = 'Dromaius';
        $holding->country_id = '236';
        $holding->client_id = '41';
        $holding->save();

        $holding = new holding();
        $holding->name = 'Mabadare II S.A. de C.V.';
        $holding->short_name = 'Mabadare';
        $holding->country_id = '144';
        $holding->client_id = '44';
        $holding->save();

        $holding = new holding();
        $holding->name = 'Risiko LLC';
        $holding->short_name = 'Risiko';
        $holding->country_id = '236';
        $holding->client_id = '29';
        $holding->save();

        $holding = new holding();
        $holding->name = 'Reasegurar LC';
        $holding->short_name = 'Reasegurar';
        $holding->country_id = '236';
        $holding->client_id = '30';
        $holding->save();

        $holding = new holding();
        $holding->name = 'Goduzam Corp.';
        $holding->short_name = 'Goduzam';
        $holding->country_id = '172';
        $holding->client_id = '31';
        $holding->save();

        $holding = new holding();
        $holding->name = 'Prosperidad Holdings LP.';
        $holding->short_name = 'Prosperidad';
        $holding->country_id = '236';
        $holding->client_id = '32';
        $holding->save();

        $holding = new holding();
        $holding->name = 'Marclar Property Holdings LLC.';
        $holding->short_name = 'Marclar';
        $holding->country_id = '236';
        $holding->client_id = '45';
        $holding->save();

        $holding = new holding();
        $holding->name = 'LMA Reaseguros LLC';
        $holding->short_name = 'LMA';
        $holding->country_id = '236';
        $holding->client_id = '34';
        $holding->save();

        $holding = new holding();
        $holding->name = 'Log Insurance LLC';
        $holding->short_name = 'Log';
        $holding->country_id = '236';
        $holding->client_id = '35';
        $holding->save();

        $holding = new holding();
        $holding->name = 'High Value Corp.';
        $holding->short_name = 'High-Value';
        $holding->country_id = '172';
        $holding->client_id = '38';
        $holding->save();

        $holding = new holding();
        $holding->name = 'Grupo Autoprotección Segura';
        $holding->short_name = 'Autoprotección';
        $holding->country_id = '236';
        $holding->client_id = '46';
        $holding->save();

        $holding = new holding();
        $holding->name = 'Power Re FJRPO Equities Limited';
        $holding->short_name = 'Power-Re';
        $holding->country_id = '235';
        $holding->client_id = '40';
        $holding->save();

        $holding = new holding();
        $holding->name = 'The Quesca Unit Trust';
        $holding->short_name = 'Quesca';
        $holding->country_id = '114';
        $holding->client_id = '47';
        $holding->save();

        $holding = new holding();
        $holding->name = 'Broxel Processing S.R.L. de C.V.';
        $holding->short_name = 'Broxel_P';
        $holding->country_id = '144';
        $holding->client_id = '48';
        $holding->save();

        $holding = new holding();
        $holding->name = 'Grupo Daosa and Toscana Holdings LLC';
        $holding->short_name = 'Daosa';
        $holding->country_id = '251';
        $holding->client_id = '13';
        $holding->save();
    }
}
