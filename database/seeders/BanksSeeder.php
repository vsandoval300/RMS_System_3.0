<?php

namespace Database\Seeders;

use App\Models\bank;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BanksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $bank=new bank();           $bank->name =  'Bank of New York  ' ;              $bank->address = '1 Wall Street, New York, NY 10286, USA';         $bank->aba_number = '021000018';         $bank->swift_code =  'IRVTUS3N ' ;              $bank->save(); 
        $bank=new bank();           $bank->name =  'Banco Santander International ' ;              $bank->address = '1401 Brickell Avenue Suite 1500, Miami, FL 33131, USA';         $bank->aba_number = '066010597';         $bank->swift_code =  'BDERUS3M ' ;              $bank->save(); 
        $bank=new bank();           $bank->name =  'RBC Capital Markets Corporation ' ;              $bank->address = 'Three World Financial Center, 200 Vesey Street, Floor 5, New York, NY 10281, USA';         $bank->aba_number = '';         $bank->swift_code =  'RCMCUS3N ' ;              $bank->save(); 
        $bank=new bank();           $bank->name =  'Deutsche Bank Trust Company Americas ' ;              $bank->address = '60 Wall Street, New York, NY 10005, USA';         $bank->aba_number = '028001036';         $bank->swift_code =  'BKTRUS33 ' ;              $bank->save(); 
        $bank=new bank();           $bank->name =  'Bancredito International Bank, Corporation ' ;              $bank->address = '250 Muñoz Rivera Avenue, 14th Floor, Suite 1410, San Juan, PR 00918, USA';         $bank->aba_number = '021502231';         $bank->swift_code =  'BIBCPRSJ ' ;              $bank->save(); 
        $bank=new bank();           $bank->name =  'Citibank ' ;              $bank->address = '399 Park Avenue, New York, NY 10043, USA';         $bank->aba_number = '021000089';         $bank->swift_code =  'CITIUS33 ' ;              $bank->save(); 
        $bank=new bank();           $bank->name =  'JP Morgan Chase Bank ' ;              $bank->address = '383 Madison Avenue, New York, NY 10179, USA';         $bank->aba_number = '021000021';         $bank->swift_code =  'CHASUS33FFS ' ;              $bank->save(); 
        $bank=new bank();           $bank->name =  'Cidel Bank & Trust Inc ' ;              $bank->address = 'Suite 100, One Financial Place Lower Collymore Rock, St. Michael Barbados';         $bank->aba_number = '';         $bank->swift_code =  'CTBLBBBB ' ;              $bank->save(); 
        $bank=new bank();           $bank->name =  'Bank of America, N.A. ' ;              $bank->address = '701 Brickell Avenue, 6th Floor Miami, Fl 33131 USA';         $bank->aba_number = '26009593';         $bank->swift_code =  'BOFAUS3M ' ;              $bank->save(); 
        $bank=new bank();           $bank->name =  'J. Safra Sarasin AG ' ;              $bank->address = 'Elisabethenstrasse 62, 4051 Basel, Suiza.';         $bank->aba_number = '';         $bank->swift_code =  'SARACHBB ' ;              $bank->save(); 
        $bank=new bank();           $bank->name =  'U.S. Bank ' ;              $bank->address = 'U.S. Bancorp Center 800 Nicollet Mall Minneapolis, MN 55402 United States';         $bank->aba_number = '91000022';         $bank->swift_code =  'USBKUS44IMT ' ;              $bank->save(); 
        $bank=new bank();           $bank->name =  'Hyposwiss Private Bank Geneve SA ' ;              $bank->address = '3, rue du General Dufour, 1204 Geneva, Switzerland';         $bank->aba_number = '';         $bank->swift_code =  'CCIECHGGXXX ' ;              $bank->save(); 
        $bank=new bank();           $bank->name =  'JP Morgan (Suisse) SA ' ;              $bank->address = 'rue du Rhône 35, 1204 Geneva, Switzerland.';         $bank->aba_number = '021000021';         $bank->swift_code =  'CHASUS33 ' ;              $bank->save(); 

    }
}
