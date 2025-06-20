<?php

namespace Database\Seeders;

use App\Models\transaction_status;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class transactionsStatuses extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $transaction_status = new transaction_status();
        $transaction_status->transaction_status ='Pending';
        $transaction_status->save();

        $transaction_status = new transaction_status();
        $transaction_status->transaction_status ='In process';
        $transaction_status->save();

        $transaction_status = new transaction_status();
        $transaction_status->transaction_status ='Completed';
        $transaction_status->save();

    }
}
