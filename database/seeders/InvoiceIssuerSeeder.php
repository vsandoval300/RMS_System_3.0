<?php

namespace Database\Seeders;

use App\Models\InvoiceIssuer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InvoiceIssuerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        
        $InvoiceIssuer = new InvoiceIssuer();
        $InvoiceIssuer->name = 'Rainmaker Insurance SCC';
        $InvoiceIssuer->short_name = 'Rainmaker Ins';
        $InvoiceIssuer->acronym = 'RMS';
        $InvoiceIssuer->country_id = '20';
        $InvoiceIssuer->address = 'Altman Annex Derricks, St. James BB24008, Barbados';
        $InvoiceIssuer->bankaccount_id = '1';
        $InvoiceIssuer->save();
        
        $InvoiceIssuer = new InvoiceIssuer();
        $InvoiceIssuer->name = 'Gatekeeper Indemnity SCC';
        $InvoiceIssuer->short_name = 'GKP-IND';
        $InvoiceIssuer->acronym = 'GKP';
        $InvoiceIssuer->country_id = '20';
        $InvoiceIssuer->address = 'Altman Annex Derricks, St. James BB24008, Barbados';
        $InvoiceIssuer->bankaccount_id = '35';
        $InvoiceIssuer->save();

        $InvoiceIssuer = new InvoiceIssuer();
        $InvoiceIssuer->name = 'Integrity Insurance Underwriting & Management Services Inc.';
        $InvoiceIssuer->short_name = 'Integrity';
        $InvoiceIssuer->acronym = 'INT';
        $InvoiceIssuer->country_id = '20';
        $InvoiceIssuer->address = 'Altman Annex Derricks, St. James BB24008, Barbados';
        $InvoiceIssuer->bankaccount_id = '60';
        $InvoiceIssuer->save();

        $InvoiceIssuer = new InvoiceIssuer();
        $InvoiceIssuer->name = 'Rainmaker Group International';
        $InvoiceIssuer->short_name = 'Rainmaker Int';
        $InvoiceIssuer->acronym = 'RMI';
        $InvoiceIssuer->country_id = '236';
        $InvoiceIssuer->address = '1441 brickell Avenue Suite 1210, Miami, Fl 33131';
        $InvoiceIssuer->bankaccount_id = '59';
        $InvoiceIssuer->save();

        $InvoiceIssuer = new InvoiceIssuer();
        $InvoiceIssuer->name = 'Rainmaker Group S.A.P.I. de C.V.';
        $InvoiceIssuer->short_name = 'Rainmaker Group';
        $InvoiceIssuer->acronym = 'RMG';
        $InvoiceIssuer->country_id = '144';
        $InvoiceIssuer->address = 'Monte Líbano 235-401 Lomas de Chapultepec, México, D.F. C.P. 11000';
        $InvoiceIssuer->bankaccount_id = null;
        $InvoiceIssuer->save();

        $InvoiceIssuer = new InvoiceIssuer();
        $InvoiceIssuer->name = 'Tricap México  S.A.P.I. de C.V.';
        $InvoiceIssuer->short_name = 'Tricap Mx';
        $InvoiceIssuer->acronym = 'TRP';
        $InvoiceIssuer->country_id = '144';
        $InvoiceIssuer->address = 'Montes Urales 745 Lomas de Chapultepec, México, D.F. C.P. 11000';
        $InvoiceIssuer->bankaccount_id = null;
        $InvoiceIssuer->save();


    }
}
