<?php

namespace Database\Seeders;

use App\Models\InvoiceStatus;
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
        $InvoiceStatus = new InvoiceStatus();
        $InvoiceStatus->invoice_status ='Paid';
        $InvoiceStatus->save();

        $InvoiceStatus = new InvoiceStatus();
        $InvoiceStatus->invoice_status ='Unpaid';
        $InvoiceStatus->save();

        $InvoiceStatus = new InvoiceStatus();
        $InvoiceStatus->invoice_status ='Overdue';
        $InvoiceStatus->save();
    }
}

