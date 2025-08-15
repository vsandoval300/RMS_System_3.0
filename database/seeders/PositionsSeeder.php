<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PositionsSeeder extends Seeder
{
    public function run(): void
    {
        $positions = [
            ['position' => 'ART Director', 'description' => 'Responsible for overseeing captive insurance programs and managing captive operations.'],
            ['position' => 'Collections Specialist', 'description' => 'Handles debt recovery and coordinates with clients to resolve outstanding balances.'],
            ['position' => 'Claims representative', 'description' => 'Assists policyholders through the claims process, ensuring timely and fair resolutions.'],
            ['position' => 'Claims processor', 'description' => 'Processes insurance claims, verifying accuracy and completeness of documentation.'],
            ['position' => 'Claims adjuster', 'description' => 'Investigates insurance claims to determine coverage, liability, and settlement amounts.'],
            ['position' => 'Claims Resolution Specialist', 'description' => 'Specializes in resolving disputed or complex claims efficiently.'],
            ['position' => 'Claims Assistant', 'description' => 'Provides administrative support in handling insurance claims.'],
            ['position' => 'Underwriter', 'description' => 'Evaluates insurance applications to determine coverage terms and premiums.'],
            ['position' => 'Underwriting Assistant', 'description' => 'Supports underwriters in reviewing applications and preparing policy documents.'],
            ['position' => 'Underwriting Specialist', 'description' => 'Expert in assessing risk and developing underwriting guidelines.'],
            ['position' => 'Case Administrator', 'description' => 'Manages case files and ensures compliance with administrative procedures.'],
            ['position' => 'Account Manager', 'description' => 'Manages client accounts, maintaining relationships and ensuring satisfaction.'],
            ['position' => 'Risk Analyst', 'description' => 'Analyzes potential risks and provides recommendations to mitigate them.'],
            ['position' => 'Insurance Analyst', 'description' => 'Reviews insurance data and market trends to support decision-making.'],
            ['position' => 'Loss Control Representative', 'description' => 'Works with clients to minimize losses through risk management strategies.'],
            ['position' => 'Risk Consultant', 'description' => 'Advises organizations on risk assessment and mitigation strategies.'],
            ['position' => 'Risk Control Consultant', 'description' => 'Provides expert advice on controlling and reducing organizational risks.'],
            ['position' => 'Risk management Consultant', 'description' => 'Specializes in implementing risk management frameworks and policies.'],
            ['position' => 'Risk Manager', 'description' => 'Oversees an organization’s risk management program.'],
            ['position' => 'Business Risk Manager', 'description' => 'Focuses on identifying and managing business-specific risks.'],
            ['position' => 'Cosporate Risk Manager', 'description' => 'Manages risk at the corporate level, aligning with strategic goals.'],
            ['position' => 'Supervisor', 'description' => 'Oversees daily operations and manages team performance.'],
            ['position' => 'Actuarial manager', 'description' => 'Leads actuarial teams in analyzing statistical data for insurance pricing.'],
            ['position' => 'Insurance Underwriter', 'description' => 'Assesses risks for insurance policies and determines appropriate premiums.'],
            ['position' => 'Finacial Analyst', 'description' => 'Analyzes financial data to support business and investment decisions.'],
            ['position' => 'Captives Sales Agent', 'description' => 'Markets and sells captive insurance products to clients.'],
            ['position' => 'Insurance Broker', 'description' => 'Acts as an intermediary between clients and insurers.'],
            ['position' => 'Risk Manager', 'description' => 'Develops strategies to minimize risk exposure within the organization.'],
            ['position' => 'Actuary', 'description' => 'Uses statistical models to assess financial risks in insurance.'],
            ['position' => 'Business Analyst', 'description' => 'Analyzes business processes and recommends improvements.'],
            ['position' => 'Compliance Officer', 'description' => 'Ensures organizational compliance with laws and regulations.'],
            ['position' => 'President', 'description' => 'Leads the organization and makes strategic decisions.'],
            ['position' => 'Office & Corporate manager', 'description' => 'Oversees office operations and corporate administrative tasks.'],
            ['position' => 'Senior Account Manager', 'description' => 'Manages high-value client accounts and complex relationships.'],
            ['position' => 'Administration Assistant', 'description' => 'Provides clerical and administrative support.'],
            ['position' => 'Compliance Assistant', 'description' => 'Supports compliance officers in maintaining regulatory standards.'],
            ['position' => 'Vice President', 'description' => 'Supports the president and oversees major organizational functions.'],
            ['position' => 'Captive Owner', 'description' => 'Holds ownership of a captive insurance company.'],
            ['position' => 'Chief Operating Officer (COO)', 'description' => 'Oversees daily operations of the company.'],
            ['position' => 'Chief Executive Officer (CEO)', 'description' => 'Sets overall strategy and vision for the organization.'],
            ['position' => 'Chief Financial Officer or Controller (CFO)', 'description' => 'Manages financial planning and reporting.'],
            ['position' => 'Chief Marketing Officer (CMO)', 'description' => 'Leads marketing strategy and brand positioning.'],
            ['position' => 'Chief Technology Officer (CTO)', 'description' => 'Oversees technology strategy and IT operations.'],
            ['position' => 'Executive Assistants', 'description' => 'Provides high-level administrative support to executives.'],
            ['position' => 'Marketing Manager', 'description' => 'Leads marketing campaigns and initiatives.'],
            ['position' => 'Product Manager', 'description' => 'Oversees product lifecycle from concept to launch.'],
            ['position' => 'Project Manager', 'description' => 'Plans and manages project execution.'],
            ['position' => 'Finance Manager', 'description' => 'Manages company financial operations and budgets.'],
            ['position' => 'Marketing Specialist', 'description' => 'Executes marketing tactics and activities.'],
            ['position' => 'Sales Representative', 'description' => 'Engages with clients to sell products or services.'],
            ['position' => 'Customer Service Representative', 'description' => 'Assists customers with inquiries and issues.'],
            ['position' => 'Administrative Assistant', 'description' => 'Provides administrative support to departments.'],
            ['position' => 'Director Information Technology', 'description' => 'Leads the IT department’s strategy, infrastructure, cybersecurity, and operations.'],
            ['position' => 'Database Administrator', 'description' => 'Manages and secures enterprise databases, including installation, backups, performance tuning, and troubleshooting.'],
            ['position' => 'Project Manager', 'description' => 'Plans and delivers projects on time, scope, and budget, coordinating teams, resources, and stakeholders.'],
            ['position' => 'Shareholder', 'description' => 'Company equity owner who exercises voting rights, receives dividends, and participates in key corporate decisions.'],
            ['position' => 'Operations Manager', 'description' => 'Oversees end-to-end operations, optimizing processes, resources, SLAs, and cross-functional coordination to ensure efficient service delivery.'],



        ];

        DB::table('positions')->insert($positions);
    }
}

