<?php

namespace Database\Seeders;

use App\Models\business_doc_type;
use App\Models\User;
use App\Models\region;
use App\Models\sub_region;
use App\Models\country;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        /*User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]); */
        $this->call([
            
            //Operative Tables 
            /*=============================================*/
            UserSeeder::class,
            //RolesSeeder::class,
            //PermissionsSeeder::class,
            //RoleHasPermissions::class,
            //ModelHasRolesSeeder::class,            
            //Cátalogos de posición geografica
            /*=============================================*/
            RegionsSeeder::class,
            SubRegionsSeeder::class, 
            CountriesSeeder::class,
            //Cátalogo de monedas
            /*=============================================*/
            CurrenciesSeeder::class,
            ClientsSeeder::class,
            BusinessUnitsSeeder::class,
            DepartmentsSeeder::class,
            //Cátalogo de Usuarios
            /*=============================================*/
            IndustriesSeeder::class,
            ClientIndustriesSeeder::class,
            PartnerTypesSeeder::class,

            //Cátalogos de Lineas de negocio y coberturas
            LineOfBusinessSeeder::class,
            CoveragesSeeder::class,

            //Cátalogo de compañías
            CompaniesSeeder::class,

            //Cátalogo de reaseguradores
            ReinsurerTypeSeeder::class,
            DocumentTypesSeeder::class,
            PartnersSeeder::class,
            BanksSeeder::class,
            DirectorsSeeder::class,
            OpStatusesSeeder::class,
            ManagersSeeder::class,
            ReinsurersSeeder::class,
            BankAccountsSeeder::class,
            ReinsurerBankAccountsSeeder::class,
            BoardsSeeder::class,
            BoardDirectorsSeeder::class,
            TransactionsStatuses::class,
            TransactionsTypesSeeder::class,
            HoldingsSeeder::class,
            HoldingReinsurersSeeder::class,
            ProducersSeeder::class,
            ReinsurerBoardsSeeder::class,
            ReinsurerFinancialsSeeder::class,
            BoardMeetingSeeder::class,
            ReinsurerBmeetingsSeeder::class,

            //Revenue catalogs
            InvoiceIssuerSeeder::class,
            InvoiceConceptsSeeder::class,
            InvoiceStatusesSeeder::class,
            DeductionsSeeder::class,
            Businesses_doc_typesSeeder::class,

            //Codigos de Transaccion
            //RemmitanceCodesSeeder::class,

            //Corporative Documents
            ReinsurerCorpDocsSeeder::class,
            
            
            
            
            
            
            
            
            
            /*=============================================*/
            //FILES FOR CELL-MAYAB
            /*=============================================*/
            BusinessesCellMayabSeeder::class, /*1*/
            LiabilityStructureCellMayabSeeder::class, /*2*/
            //Files for Placement Schemes
            /*=============================================*/
           
            CschemeCellMayabSeeder::class, /*6*/
            CostNodesxCellMayabSeeder::class, /*4*/
            //CschemeCnodesCellMayabSeeder::class, /*7*/
            //Files for Business Documents
            /*=============================================*/
            BusinessesDocsCellMayabSeeder::class, /*Details*/
            BusinessDocInsuredsCellMayabSeeder::class, /*Insureds*/
            BusinessDocsSchemesCellMayabSeeder::class, /*Placement Schemes*/
            TransactionsCellMayabSeeder::class, /*9*/
            TransactionsLogCellMayabSeeder::class, /*10*/
            
            //ReferralsCellMayabSeeder::class, /*5*/
            //InvoicesCellMayabSeeder::class, /*11*/
            //InvoiceTransactionsCellMayabSeeder::class, /*12*/

            
            
            
            /*=============================================*/
            //FILES FOR YELMO
            /*=============================================*/
            
            //BusinessesYelmoSeeder::class, /*1*/
            //LiabilityStructureYelmoSeeder::class, /*2*/
            
            //Files for Placement Schemes
            /*=============================================*/
            //CostNodesYelmoSeeder::class, /*4*/
            //CschemeYelmoSeeder::class, /*6*/
            //CschemeCnodesYelmoSeeder::class, /*7*/
            //Files for Business Documents
            /*=============================================*/
            //BusinessesDocsYelmoSeeder::class, /*Details*/
            //BusinessDocInsuredsYelmoSeeder::class, /*Insureds*/
            //BusinessDocsSchemesYelmoSeeder::class, /*Placement Schemes*/
            //TransactionsYelmoSeeder::class, /*9*/
            //TransactionsLogYelmoSeeder::class, /*10*/
            

            //ReferralsYelmoSeeder::class, /*5*/
            //InvoicesYelmoSeeder::class, /*11*/
            //InvoiceTransactionsYelmoSeeder::class, /*12*/
            
            
            /*=============================================*/
            //FILES FOR ADAMAS
            /*=============================================*/
            //BusinessesAdamasSeeder::class, /*1*/
            //LiabilityStructureAdamasSeeder::class, /*2*/
            //Files for Placement Schemes
            /*=============================================*/
            //CostNodesAdamasSeeder::class, /*4*/
            //CschemeAdamasSeeder::class, /*6*/
            //CschemeCnodesAdamasSeeder::class, /*7*/
            //Files for Business Documents
            /*=============================================*/
            //BusinessesDocsAdamasSeeder::class, /*Details*/
            //BusinessDocInsuredsAdamasSeeder::class, /*Insureds*/
            //BusinessDocsSchemesAdamasSeeder::class, /*Placement Schemes*/
            //TransactionsAdamasSeeder::class, /*9*/
            //TransactionsLogAdamasSeeder::class, /*10*/
            

            //ReferralsYelmoSeeder::class, /*5*/
            //InvoicesYelmoSeeder::class, /*11*/
            //InvoiceTransactionsYelmoSeeder::class, /*12*/













            
            //File of Activity
            /*=============================================*/
            //ActivityLogSeeder::class, /*1*/

        ]);




    }
}
