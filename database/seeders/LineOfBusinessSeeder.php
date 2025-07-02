<?php

namespace Database\Seeders;

use App\Models\LineOfBusiness;
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
        $LineOfBusiness=new LineOfBusiness();        $LineOfBusiness->name = 'Agricultural';          $LineOfBusiness->description = 'Provides coverage for farms, livestock, crops, and agricultural equipment against risks like natural disasters, disease, and theft.';        $LineOfBusiness->save(); 
        $LineOfBusiness=new LineOfBusiness();          $LineOfBusiness->name = 'Aircraft';               $LineOfBusiness->description = 'Offers insurance for aircraft owners and operators, covering physical damage to aircraft and liability for passengers, third parties, and cargo.';                  $LineOfBusiness->save(); 
        $LineOfBusiness=new LineOfBusiness();          $LineOfBusiness->name = 'Auto';               $LineOfBusiness->description = 'Covers personal and commercial vehicles against physical damage and liability resulting from accidents, theft, and other incidents.';                  $LineOfBusiness->save(); 
        $LineOfBusiness=new LineOfBusiness();          $LineOfBusiness->name = 'Bond';               $LineOfBusiness->description = 'Provides surety bonds guaranteeing performance and financial obligations, including contract bonds, fidelity bonds, and court bonds.';                  $LineOfBusiness->save(); 
        $LineOfBusiness=new LineOfBusiness();          $LineOfBusiness->name = 'Casualty';               $LineOfBusiness->description = 'Encompasses liability insurance that protects individuals and businesses against legal liabilities, including injury or property damage claims.';                  $LineOfBusiness->save(); 
        $LineOfBusiness=new LineOfBusiness();          $LineOfBusiness->name = 'Comprehensive General Liability';               $LineOfBusiness->description = 'Offers broad coverage for businesses against various liability risks, including bodily injury, property damage, and personal injury.';                  $LineOfBusiness->save(); 
        $LineOfBusiness=new LineOfBusiness();          $LineOfBusiness->name = 'Financial Lines';               $LineOfBusiness->description = 'Includes specialized insurance products like directors and officers (D&O) liability, professional indemnity, and cyber liability, protecting against financial and professional risks.';                  $LineOfBusiness->save(); 
        $LineOfBusiness=new LineOfBusiness();          $LineOfBusiness->name = 'Health';               $LineOfBusiness->description = 'Covers medical expenses and health-related services for individuals and groups, including hospitalization, prescription drugs, and preventive care.';                  $LineOfBusiness->save(); 
        $LineOfBusiness=new LineOfBusiness();          $LineOfBusiness->name = 'Life Group';               $LineOfBusiness->description = 'Provides life insurance coverage to groups, typically through employers, offering death benefits to beneficiaries of insured employees.';                  $LineOfBusiness->save(); 
        $LineOfBusiness=new LineOfBusiness();          $LineOfBusiness->name = 'Marine';               $LineOfBusiness->description = 'Covers loss or damage to ships, cargo, terminals, and any transport or cargo by which property is transferred, acquired, or held between points of origin and final destination.';                  $LineOfBusiness->save(); 
        $LineOfBusiness=new LineOfBusiness();          $LineOfBusiness->name = 'Miscellaneous';               $LineOfBusiness->description = 'Encompasses a variety of niche insurance products that dont fit into other standard categories, such as event insurance or pet insurance.';                  $LineOfBusiness->save(); 
        $LineOfBusiness=new LineOfBusiness();          $LineOfBusiness->name = 'Property';               $LineOfBusiness->description = 'Protects against risks to property, such as fire, theft, and natural disasters, covering buildings, equipment, inventory, and personal property.';                  $LineOfBusiness->save(); 
        $LineOfBusiness=new LineOfBusiness();          $LineOfBusiness->name = 'Several';               $LineOfBusiness->description = 'Refers to multiple lines of business bundled together in a single policy, offering coverage for various risks under one comprehensive plan.';                  $LineOfBusiness->save(); 
        $LineOfBusiness=new LineOfBusiness();          $LineOfBusiness->name = 'Third Party Liability';               $LineOfBusiness->description = 'Provides coverage for claims made by third parties for injuries or damages resulting from the policyholders actions or operations.';                  $LineOfBusiness->save(); 
        $LineOfBusiness=new LineOfBusiness();          $LineOfBusiness->name = 'Transport';               $LineOfBusiness->description = 'Insures goods in transit, covering loss or damage to cargo transported by land, sea, or air.';                  $LineOfBusiness->save(); 
        $LineOfBusiness=new LineOfBusiness();          $LineOfBusiness->name = 'Workers Compensation';               $LineOfBusiness->description = 'Provides medical benefits and wage replacement to employees injured in the course of employment, protecting employers from lawsuits by employees.';                  $LineOfBusiness->save(); 
    }
}
