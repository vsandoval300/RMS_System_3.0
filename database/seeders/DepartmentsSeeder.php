<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentsSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'Underwriting', 'description' => 'Risk evaluation and policy underwriting for ART.', 'business_unit_id' => 1],
            ['name' => 'Operations', 'description' => 'Operational execution and internal support for ART.', 'business_unit_id' => 1],
            ['name' => 'Sales', 'description' => 'Client acquisition and ART product sales.', 'business_unit_id' => 1],
            ['name' => 'Compliance & Legal', 'description' => 'Regulatory compliance and legal support for ART.', 'business_unit_id' => 1],
            ['name' => 'Corporate Management', 'description' => 'Oversight and coordination of strategic, operational, and administrative activities across multiple departments to ensure alignment with the organization’s objectives.', 'business_unit_id' => 1],
            ['name' => 'Finance & Accounting', 'description' => 'Financial control, budgeting, billing, and accounting.', 'business_unit_id' => 1],
            ['name' => 'Risk Management', 'description' => 'Identification, assessment, and mitigation of corporate risks.', 'business_unit_id' => 1],
            ['name' => 'Human Resources', 'description' => 'Recruitment, training, employee relations, and organizational culture.', 'business_unit_id' => 1],
            ['name' => 'Marketing & Communications', 'description' => 'Marketing strategy, branding, and internal/external communications.', 'business_unit_id' => 1],
            ['name' => 'Client Services', 'description' => 'Customer support, relationship management, and retention.', 'business_unit_id' => 1],
            ['name' => 'Strategic Planning & Business', 'description' => 'Development of new business opportunities and long-term planning.', 'business_unit_id' => 1],
            ['name' => 'Shareholders Department', 'description' => 'Department composed of the company’s shareholders, responsible for representing ownership interests, participating in strategic decision-making, and overseeing the organization’s overall direction.', 'business_unit_id' => 1],
            ['name' => 'Information Technology', 'description' => 'Management of technology infrastructure, software development, cybersecurity, and IT support services.', 'business_unit_id' => 1],
            ['name' => 'Family Office', 'description' => 'Management of personal and family investments, estate planning, and financial administration.', 'business_unit_id' => 1],
        ];

        foreach ($departments as $dept) {
            Department::create($dept);
        }
    }
}