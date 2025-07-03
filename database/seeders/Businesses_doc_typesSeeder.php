<?php

namespace Database\Seeders;

use App\Models\BusinessDocType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Businesses_doc_typesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $BusinessDocType = new BusinessDocType();
        $BusinessDocType->name = 'Slip';
        $BusinessDocType->description ='Slip: A slip is a document used in the insurance industry, particularly in the placement of reinsurance, which outlines the basic details of the insurance coverage being proposed. It includes essential information such as the terms, conditions, and limits of the coverage, and it is presented to potential reinsurers to negotiate and bind the coverage. The slip serves as an initial summary before a formal policy is issued.';
        $BusinessDocType->save();

        $BusinessDocType = new BusinessDocType();
        $BusinessDocType->name = 'End A';
        $BusinessDocType->description ='Endorsement with Premium Change: These are endorsements that modify the insurance policys premium due to various situations, such as adding or removing coverages, changing data that affects the risk, and other similar factors.';
        $BusinessDocType->save();

        $BusinessDocType = new BusinessDocType();
        $BusinessDocType->name = 'End B';
        $BusinessDocType->description ='Endorsement without Premium Change: These endorsements involve changes (such as data corrections) that do not alter the risk initially assumed by the insurer. Therefore, the insurance premium remains unchanged.';
        $BusinessDocType->save();

        $BusinessDocType = new BusinessDocType();
        $BusinessDocType->name = 'End C';
        $BusinessDocType->description ='Cancellation Endorsement: This endorsement is used when the policyholder wishes to terminate their insurance coverage before the policys expiration date.';
        $BusinessDocType->save();
    }
}
