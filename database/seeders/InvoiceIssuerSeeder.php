<?php

namespace Database\Seeders;

use App\Models\invoice_issuer;
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
        
        $invoice_issuer = new invoice_issuer();
        $invoice_issuer->name = 'Rainmaker Insurance SCC';
        $invoice_issuer->short_name = 'Rainmaker Ins';
        $invoice_issuer->acronym = 'RMS';
        $invoice_issuer->country_id = '20';
        $invoice_issuer->address = 'Altman Annex Derricks, St. James BB24008, Barbados';
        $invoice_issuer->bankaccount_id = '1';
        $invoice_issuer->save();
        
        $invoice_issuer = new invoice_issuer();
        $invoice_issuer->name = 'Gatekeeper Indemnity SCC';
        $invoice_issuer->short_name = 'GKP-IND';
        $invoice_issuer->acronym = 'GKP';
        $invoice_issuer->country_id = '20';
        $invoice_issuer->address = 'Altman Annex Derricks, St. James BB24008, Barbados';
        $invoice_issuer->bankaccount_id = '35';
        $invoice_issuer->save();

        $invoice_issuer = new invoice_issuer();
        $invoice_issuer->name = 'Integrity Insurance Underwriting & Management Services Inc.';
        $invoice_issuer->short_name = 'Integrity';
        $invoice_issuer->acronym = 'INT';
        $invoice_issuer->country_id = '20';
        $invoice_issuer->address = 'Altman Annex Derricks, St. James BB24008, Barbados';
        $invoice_issuer->bankaccount_id = '60';
        $invoice_issuer->save();

        $invoice_issuer = new invoice_issuer();
        $invoice_issuer->name = 'Rainmaker Group International';
        $invoice_issuer->short_name = 'Rainmaker Int';
        $invoice_issuer->acronym = 'RMI';
        $invoice_issuer->country_id = '236';
        $invoice_issuer->address = '1441 brickell Avenue Suite 1210, Miami, Fl 33131';
        $invoice_issuer->bankaccount_id = '59';
        $invoice_issuer->save();

        $invoice_issuer = new invoice_issuer();
        $invoice_issuer->name = 'Rainmaker Group S.A.P.I. de C.V.';
        $invoice_issuer->short_name = 'Rainmaker Group';
        $invoice_issuer->acronym = 'RMG';
        $invoice_issuer->country_id = '144';
        $invoice_issuer->address = 'Monte Líbano 235-401 Lomas de Chapultepec, México, D.F. C.P. 11000';
        $invoice_issuer->bankaccount_id = null;
        $invoice_issuer->save();

        $invoice_issuer = new invoice_issuer();
        $invoice_issuer->name = 'Tricap México  S.A.P.I. de C.V.';
        $invoice_issuer->short_name = 'Tricap Mx';
        $invoice_issuer->acronym = 'TRP';
        $invoice_issuer->country_id = '144';
        $invoice_issuer->address = 'Montes Urales 745 Lomas de Chapultepec, México, D.F. C.P. 11000';
        $invoice_issuer->bankaccount_id = null;
        $invoice_issuer->save();


    }
}
