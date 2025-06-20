<?php

namespace Database\Seeders;

use App\Models\invoice_concept;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InvoiceConceptsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $invoice_concept = new invoice_concept();
        $invoice_concept->type = 'AF';
        $invoice_concept->description = 'Access Fee';
        $invoice_concept->save();

        $invoice_concept = new invoice_concept();
        $invoice_concept->type = 'MF';
        $invoice_concept->description = 'Management Fee';
        $invoice_concept->save();

        $invoice_concept = new invoice_concept();
        $invoice_concept->type = 'RiRF';
        $invoice_concept->description = 'Retail Refererral Fee';
        $invoice_concept->save();

        $invoice_concept = new invoice_concept();
        $invoice_concept->type = 'BkRF';
        $invoice_concept->description = 'Broker Refererral Fee';
        $invoice_concept->save();

        $invoice_concept = new invoice_concept();
        $invoice_concept->type = 'ReRF';
        $invoice_concept->description = 'Reinsurer Refererral Fee';
        $invoice_concept->save();

        $invoice_concept = new invoice_concept();
        $invoice_concept->type = 'FeSt';
        $invoice_concept->description = 'Feasibility Study';
        $invoice_concept->save();

    }
}
