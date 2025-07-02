<?php

namespace Database\Seeders;

use App\Models\InvoiceConcept;
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
        $InvoiceConcept = new InvoiceConcept();
        $InvoiceConcept->type = 'AF';
        $InvoiceConcept->description = 'Access Fee';
        $InvoiceConcept->save();

        $InvoiceConcept = new InvoiceConcept();
        $InvoiceConcept->type = 'MF';
        $InvoiceConcept->description = 'Management Fee';
        $InvoiceConcept->save();

        $InvoiceConcept = new InvoiceConcept();
        $InvoiceConcept->type = 'RiRF';
        $InvoiceConcept->description = 'Retail Refererral Fee';
        $InvoiceConcept->save();

        $InvoiceConcept = new InvoiceConcept();
        $InvoiceConcept->type = 'BkRF';
        $InvoiceConcept->description = 'Broker Refererral Fee';
        $InvoiceConcept->save();

        $InvoiceConcept = new InvoiceConcept();
        $InvoiceConcept->type = 'ReRF';
        $InvoiceConcept->description = 'Reinsurer Refererral Fee';
        $InvoiceConcept->save();

        $InvoiceConcept = new InvoiceConcept();
        $InvoiceConcept->type = 'FeSt';
        $InvoiceConcept->description = 'Feasibility Study';
        $InvoiceConcept->save();

    }
}
