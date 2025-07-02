<?php

namespace Database\Seeders;

use App\Models\PartnerType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PartnerTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $partner_type = new PartnerType();
        $partner_type->name ='Insurance Agent';
        $partner_type->description = 'An insurance agent sells and manages insurance policies, helping clients choose appropriate coverage. They explain policy details, assess needs, recommend options, and assist with claims.';
        $partner_type->acronym = 'IAG';
        $partner_type->save();

        $partner_type = new PartnerType();
        $partner_type->name ='Retail Broker';
        $partner_type->description = 'A retail broker in the insurance industry helps clients find and purchase insurance policies from various providers. They assess clients needs, compare options, negotiate terms, and ensure clients get the best coverage at competitive rates.';
        $partner_type->acronym = 'RIB';
        $partner_type->save();

        $partner_type = new PartnerType();
        $partner_type->name ='Insurance Company';
        $partner_type->description = 'An insurance company provides financial protection to individuals and businesses by offering various insurance policies, such as health, life, auto, and property insurance. It collects premiums from policyholders and pays out claims in case of covered events.';
        $partner_type->acronym = 'INC';
        $partner_type->save();

        $partner_type = new PartnerType();
        $partner_type->name ='Reinsurance Broker';
        $partner_type->description = 'A reinsurance broker acts as an intermediary between insurance companies and reinsurance companies. They help insurance companies transfer portions of their risk portfolios to reinsurance firms to mitigate potential losses. Reinsurance brokers assess risks, negotiate terms, place reinsurance contracts, and provide advice on managing large or complex risks.';
        $partner_type->acronym = 'REB';
        $partner_type->save();

        $partner_type = new PartnerType();
        $partner_type->name ='Reinsurance Company';
        $partner_type->description = 'A reinsurance company provides insurance to insurance companies by taking on some of their risk portfolios. This helps insurance companies manage large or unexpected claims, ensuring their financial stability. Reinsurance companies assess and price risk, underwrite reinsurance policies, and pay out claims to their insurance company clients in case of covered events.';
        $partner_type->acronym = 'REC';
        $partner_type->save();

        $partner_type = new PartnerType();
        $partner_type->name ='Managing General Agent';
        $partner_type->description = 'A Managing General Agent (MGA) is an intermediary in the insurance industry that performs specialized functions on behalf of an insurance company. These functions can include underwriting, policy issuance, and claims handling. MGAs have the authority to make decisions and bind coverage, often operating with more autonomy than typical agents or brokers. They leverage their expertise to serve niche markets or specific types of insurance.';
        $partner_type->acronym = 'MGA';
        $partner_type->save();

        $partner_type = new PartnerType();
        $partner_type->name ='Client';
        $partner_type->description = 'Source of business.';
        $partner_type->acronym = 'CLI';
        $partner_type->save();
    }
}
