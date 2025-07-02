<?php

namespace Database\Seeders;

use App\Models\TransactionStatus;
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
        $TransactionStatus = new TransactionStatus();
        $TransactionStatus->transaction_status ='Pending';
        $TransactionStatus->save();

        $TransactionStatus = new TransactionStatus();
        $TransactionStatus->transaction_status ='In process';
        $TransactionStatus->save();

        $TransactionStatus = new TransactionStatus();
        $TransactionStatus->transaction_status ='Completed';
        $TransactionStatus->save();

    }
}
