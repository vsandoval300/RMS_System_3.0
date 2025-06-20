<?php

namespace Database\Seeders;

use App\Models\invoice_statuses;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InvoiceStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $invoice_statuses = new invoice_statuses();
        $invoice_statuses->invoice_status ='Paid';
        $invoice_statuses->save();

        $invoice_statuses = new invoice_statuses();
        $invoice_statuses->invoice_status ='Unpaid';
        $invoice_statuses->save();

        $invoice_statuses = new invoice_statuses();
        $invoice_statuses->invoice_status ='Overdue';
        $invoice_statuses->save();
    }
}

