<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PositionsSeeder extends Seeder
{
    public function run(): void
    {
        $positions = [
            ['position' => 'Underwriting Junior Assistant', 'description' => 'Supports underwriters in reviewing applications and preparing policy documents.'],
            ['position' => 'Underwriting Senior Specialist', 'description' => 'Expert in assessing risk and developing underwriting guidelines.'],
            ['position' => 'Insurance Analyst', 'description' => 'Reviews insurance data and market trends to support decision-making.'],
            ['position' => 'Underwriting & Actuarial Director', 'description' => 'Leads underwriting and actuarial functions: risk selection, pricing, portfolio profitability, reserving and capital modeling, and reinsurance support to drive profitable growth.'],
            ['position' => 'Corporate Governance Director', 'description' => 'Leads the corporate governance framework—board and committee processes, charters and minutes, ethics/compliance policies, disclosures and shareholder governance—ensuring adherence to laws and best practices.'],
            ['position' => 'Insurance Director', 'description' => 'Leads the insurance function—setting product and underwriting strategy, governing claims and reinsurance, ensuring regulatory compliance and customer outcomes, and delivering portfolio profitability.'],
            ['position' => 'Reserves Actuary', 'description' => 'Estimates and monitors technical reserves (case & IBNR) using actuarial methods; analyzes loss development and adequacy, explains movements, and supports financial reporting, audit and reinsurance.'],
            ['position' => 'Business Analyst', 'description' => 'Analyzes business processes and recommends improvements.'],
            ['position' => 'Chief Executive Officer (CEO)', 'description' => 'Sets overall strategy and vision for the organization.'],
            ['position' => 'Chief Financial Officer (CFO)', 'description' => 'Manages financial planning and reporting.'],
            ['position' => 'Executive Assistant', 'description' => 'Provides high-level administrative support to executives.'],
            ['position' => 'Commercial Director', 'description' => 'Leads the commercial strategy and sales organization to achieve revenue and margin targets; manages pipeline, pricing and key accounts, and partners with Marketing for demand.'],
            ['position' => 'Commercial Excecutive', 'description' => 'Engages with clients to sell products or services.'],
            ['position' => 'Information Technology Director', 'description' => 'Leads the IT department’s strategy, infrastructure, cybersecurity, and operations.'],
            ['position' => 'Database Administrator', 'description' => 'Manages and secures enterprise databases, including installation, backups, performance tuning, and troubleshooting.'],
            ['position' => 'IT Developer', 'description' => 'Plans and delivers projects on time, scope, and budget, coordinating teams, resources, and stakeholders.'],
            ['position' => 'Partner', 'description' => 'Company equity owner who exercises voting rights, receives dividends, and participates in key corporate decisions.'],
            ['position' => 'Operations & Administration Director', 'description' => 'Oversees end-to-end operations, optimizing processes, resources, SLAs, and cross-functional coordination to ensure efficient service delivery.'],
            ['position' => 'Country Manager', 'description' => 'Owns the country P&L and strategy; leads the local team across sales, operations and marketing, builds partnerships, ensures compliance, and delivers revenue growth and profitability.'],
            ['position' => 'Office Manager', 'description' => 'Manages day-to-day office operations and administration, coordinating facilities, vendors, procurement and travel; maintains records and compliance, and supports the Country Manager with budgeting, reporting and people logistics.'],
            ['position' => 'Accounting Manager', 'description' => 'Leads the accounting function: monthly close, general ledger, AP/AR, payroll, reconciliations and statutory reporting; ensures accurate financials, strong internal controls, audits and GAAP/IFRS compliance; partners with Finance on budgeting.'],
            ['position' => 'Insurance Specialist', 'description' => 'Provides coverage expertise and policy servicing: prepares quotes and placements, reviews wordings and endorsements, coordinates renewals and claims, and ensures accurate documentation and compliance.'],

        ];

        DB::table('positions')->insert($positions);
    }
}

