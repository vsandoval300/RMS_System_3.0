<?php

namespace Database\Seeders;

use App\Models\line_of_business;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LineOfBusinessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $line_of_business=new line_of_business();        $line_of_business->name = 'Agricultural';          $line_of_business->description = 'Provides coverage for farms, livestock, crops, and agricultural equipment against risks like natural disasters, disease, and theft.';        $line_of_business->save(); 
        $line_of_business=new line_of_business();          $line_of_business->name = 'Aircraft';               $line_of_business->description = 'Offers insurance for aircraft owners and operators, covering physical damage to aircraft and liability for passengers, third parties, and cargo.';                  $line_of_business->save(); 
        $line_of_business=new line_of_business();          $line_of_business->name = 'Auto';               $line_of_business->description = 'Covers personal and commercial vehicles against physical damage and liability resulting from accidents, theft, and other incidents.';                  $line_of_business->save(); 
        $line_of_business=new line_of_business();          $line_of_business->name = 'Bond';               $line_of_business->description = 'Provides surety bonds guaranteeing performance and financial obligations, including contract bonds, fidelity bonds, and court bonds.';                  $line_of_business->save(); 
        $line_of_business=new line_of_business();          $line_of_business->name = 'Casualty';               $line_of_business->description = 'Encompasses liability insurance that protects individuals and businesses against legal liabilities, including injury or property damage claims.';                  $line_of_business->save(); 
        $line_of_business=new line_of_business();          $line_of_business->name = 'Comprehensive General Liability';               $line_of_business->description = 'Offers broad coverage for businesses against various liability risks, including bodily injury, property damage, and personal injury.';                  $line_of_business->save(); 
        $line_of_business=new line_of_business();          $line_of_business->name = 'Financial Lines';               $line_of_business->description = 'Includes specialized insurance products like directors and officers (D&O) liability, professional indemnity, and cyber liability, protecting against financial and professional risks.';                  $line_of_business->save(); 
        $line_of_business=new line_of_business();          $line_of_business->name = 'Health';               $line_of_business->description = 'Covers medical expenses and health-related services for individuals and groups, including hospitalization, prescription drugs, and preventive care.';                  $line_of_business->save(); 
        $line_of_business=new line_of_business();          $line_of_business->name = 'Life Group';               $line_of_business->description = 'Provides life insurance coverage to groups, typically through employers, offering death benefits to beneficiaries of insured employees.';                  $line_of_business->save(); 
        $line_of_business=new line_of_business();          $line_of_business->name = 'Marine';               $line_of_business->description = 'Covers loss or damage to ships, cargo, terminals, and any transport or cargo by which property is transferred, acquired, or held between points of origin and final destination.';                  $line_of_business->save(); 
        $line_of_business=new line_of_business();          $line_of_business->name = 'Miscellaneous';               $line_of_business->description = 'Encompasses a variety of niche insurance products that dont fit into other standard categories, such as event insurance or pet insurance.';                  $line_of_business->save(); 
        $line_of_business=new line_of_business();          $line_of_business->name = 'Property';               $line_of_business->description = 'Protects against risks to property, such as fire, theft, and natural disasters, covering buildings, equipment, inventory, and personal property.';                  $line_of_business->save(); 
        $line_of_business=new line_of_business();          $line_of_business->name = 'Several';               $line_of_business->description = 'Refers to multiple lines of business bundled together in a single policy, offering coverage for various risks under one comprehensive plan.';                  $line_of_business->save(); 
        $line_of_business=new line_of_business();          $line_of_business->name = 'Third Party Liability';               $line_of_business->description = 'Provides coverage for claims made by third parties for injuries or damages resulting from the policyholders actions or operations.';                  $line_of_business->save(); 
        $line_of_business=new line_of_business();          $line_of_business->name = 'Transport';               $line_of_business->description = 'Insures goods in transit, covering loss or damage to cargo transported by land, sea, or air.';                  $line_of_business->save(); 
        $line_of_business=new line_of_business();          $line_of_business->name = 'Workers Compensation';               $line_of_business->description = 'Provides medical benefits and wage replacement to employees injured in the course of employment, protecting employers from lawsuits by employees.';                  $line_of_business->save(); 
    }
}
