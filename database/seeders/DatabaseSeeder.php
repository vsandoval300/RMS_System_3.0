<?php

namespace Database\Seeders;

use App\Models\business_doc_type;
use App\Models\User;
use App\Models\region;
use App\Models\sub_region;
use App\Models\country;
use App\Models\Transaction;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
// NUEVO: para poder invocar comandos Artisan desde el seeder
use Illuminate\Support\Facades\Artisan;
// NUEVO: para limpiar la caché de permisos al final
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         Transaction::$autoBuildLogs = false;
        // User::factory(10)->create();

        /*User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]); */
        try {
        $this->call([
            
            //Operative Tables 
            
            //=============================================
            RegionsSeeder::class,
            SubRegionsSeeder::class, 
            CountriesSeeder::class,
            //Cátalogo de monedas
            //=============================================
            CurrenciesSeeder::class,
            ClientsSeeder::class,
           
            //=============================================
            BusinessUnitsSeeder::class,
            DepartmentsSeeder::class,
            PositionsSeeder::class,
            UserSeeder::class,
            //RolesSeeder::class,
            //PermissionsSeeder::class,
            //RoleHasPermissions::class,
            //ModelHasRolesSeeder::class,            
            //Cátalogos de posición geografica
            
            //Cátalogo de Usuarios
            //=============================================
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
            
            
            
            
            
            
            
            
            
            //=============================================
            //FILES FOR CELL-MAYAB
            //=============================================
               BusinessesCellMayabSeeder::class, //1
               LiabilityStructureCellMayabSeeder::class, //2
            //Files for Placement Schemes==================
               CschemeCellMayabSeeder::class, //6
               CostNodesxCellMayabSeeder::class, //4
            //CschemeCnodesCellMayabSeeder::class, //7
            //Files for Business Documents=================
               BusinessesDocsCellMayabSeeder::class, //Details
               BusinessDocInsuredsCellMayabSeeder::class, //Insureds
               BusinessDocsSchemesCellMayabSeeder::class, //Placement Schemes
               TransactionsCellMayabSeeder::class, //Transactions
               TransactionsLogCellMayabSeeder::class, //TransactionLogs             
            //ReferralsCellMayabSeeder::class, //5
            //InvoicesCellMayabSeeder::class, //11
            //InvoiceTransactionsCellMayabSeeder::class, //12

            //=============================================
            //FILES FOR MAYAB 2026
            //=============================================
               BusinessesMayabSeeder2026::class, //1
               LiabilityStructureMayabSeeder2026::class, //2
            //Files for Placement Schemes==================
               CschemeMayabSeeder2026::class, //6
               CostNodesxMayabSeeder2026::class, //4
            //CschemeCnodesCellMayabSeeder::class, //7
            //Files for Business Documents=================
               BusinessesDocsMayabSeeder2026::class, //Details
               BusinessDocInsuredsMayabSeeder2026::class, //Insureds
               BusinessDocsSchemesMayabSeeder2026::class, //Placement Schemes
               //TransactionsCellMayabSeeder::class, //Transactions
               //TransactionsLogCellMayabSeeder::class, //TransactionLogs             
            //ReferralsCellMayabSeeder::class, //5
            //InvoicesCellMayabSeeder::class, //11
            //InvoiceTransactionsCellMayabSeeder::class, //12

            //=============================================
            //FILES FOR TOBAH 2026
            //=============================================
               BusinessesTobahSeeder2026::class, //1
               LiabilityStructureTobahSeeder2026::class, //2
            //Files for Placement Schemes==================
               CschemeTobahSeeder2026::class, //6
               CostNodesxTobahSeeder2026::class, //4
            //CschemeCnodesCellMayabSeeder::class, //7
            //Files for Business Documents=================
               BusinessesDocsTobahSeeder2026::class, //Details
               BusinessDocInsuredsTobahSeeder2026::class, //Insureds
               BusinessDocsSchemesTobahSeeder2026::class, //Placement Schemes
               //TransactionsCellMayabSeeder::class, //Transactions
               //TransactionsLogCellMayabSeeder::class, //TransactionLogs             
            //ReferralsCellMayabSeeder::class, //5
            //InvoicesCellMayabSeeder::class, //11
            //InvoiceTransactionsCellMayabSeeder::class, //12

            //=============================================
            //FILES FOR OMEGA 2026
            //=============================================
               BusinessesOmegaSeeder2026::class, //1
               LiabilityStructureOmegaSeeder2026::class, //2
            //Files for Placement Schemes==================
               CschemeOmegaSeeder2026::class, //6
               CostNodesxOmegaSeeder2026::class, //4
            //CschemeCnodesCellMayabSeeder::class, //7
            //Files for Business Documents=================
               BusinessesDocsOmegaSeeder2026::class, //Details
               BusinessDocInsuredsOmegaSeeder2026::class, //Insureds
               BusinessDocsSchemesOmegaSeeder2026::class, //Placement Schemes
               //TransactionsCellMayabSeeder::class, //Transactions
               //TransactionsLogCellMayabSeeder::class, //TransactionLogs             
            //ReferralsCellMayabSeeder::class, //5
            //InvoicesCellMayabSeeder::class, //11
            //InvoiceTransactionsCellMayabSeeder::class, //12
            
            //=============================================
            //FILES FOR MESOAMERICA 2026
            //=============================================
               BusinessesMesoamericaSeeder2026::class, //1
               LiabilityStructureMesoamericaSeeder2026::class, //2
            //Files for Placement Schemes==================
               CschemeMesoamericaSeeder2026::class, //6
               CostNodesxMesoamericaSeeder2026::class, //4
            //CschemeCnodesCellMayabSeeder::class, //7
            //Files for Business Documents=================
               BusinessesDocsMesoamericaSeeder2026::class, //Details
               BusinessDocInsuredsMesoamericaSeeder2026::class, //Insureds
               BusinessDocsSchemesMesoamericaSeeder2026::class, //Placement Schemes
               //TransactionsCellMayabSeeder::class, //Transactions
               //TransactionsLogCellMayabSeeder::class, //TransactionLogs             
            //ReferralsCellMayabSeeder::class, //5
            //InvoicesCellMayabSeeder::class, //11
            //InvoiceTransactionsCellMayabSeeder::class, //12
            
            //=============================================
            //FILES FOR INVICTUS 2026
            //=============================================
               BusinessesInvictusSeeder2026::class, //1
               LiabilityStructureInvictusSeeder2026::class, //2
            //Files for Placement Schemes==================
               CschemeInvictusSeeder2026::class, //6
               CostNodesxInvictusSeeder2026::class, //4
            //CschemeCnodesCellMayabSeeder::class, //7
            //Files for Business Documents=================
               BusinessesDocsInvictusSeeder2026::class, //Details
               BusinessDocInsuredsInvictusSeeder2026::class, //Insureds
               BusinessDocsSchemesInvictusSeeder2026::class, //Placement Schemes
               //TransactionsCellMayabSeeder::class, //Transactions
               //TransactionsLogCellMayabSeeder::class, //TransactionLogs             
            //ReferralsCellMayabSeeder::class, //5
            //InvoicesCellMayabSeeder::class, //11
            //InvoiceTransactionsCellMayabSeeder::class, //12
            
            
            
            
            
            
            /*
            //=============================================
            //FILES FOR YELMO
            //=============================================
            BusinessesYelmoSeeder::class, //1
            LiabilityStructureYelmoSeeder::class, //2
            //Files for Placement Schemes
            //=============================================
            CschemeYelmoSeeder::class, //6
            CostNodesxYelmoSeeder::class, //4
            //CschemeCnodesYelmoSeeder::class, //7
            //Files for Business Documents
            //=============================================
            BusinessesDocsYelmoSeeder::class, //Details
            BusinessDocInsuredsYelmoSeeder::class, //Insureds
            BusinessDocsSchemesYelmoSeeder::class, //Placement Schemes
            TransactionsYelmoSeeder::class, //9
            //TransactionsLogYelmoSeeder::class, //10
            

            //ReferralsYelmoSeeder::class, //5
            //InvoicesYelmoSeeder::class, //11
            //InvoiceTransactionsYelmoSeeder::class, //12
            
            
            //=============================================
            //FILES FOR ADAMAS
            //=============================================
            BusinessesAdamasSeeder::class, //1
            LiabilityStructureAdamasSeeder::class, //2
            //Files for Placement Schemes
            //=============================================
            CschemeAdamasSeeder::class, //6
            CostNodesxAdamasSeeder::class, //4
            //CschemeCnodesAdamasSeeder::class, //7
            //Files for Business Documents
            //=============================================
            BusinessesDocsAdamasSeeder::class, //Details
            BusinessDocInsuredsAdamasSeeder::class, //Insureds
            BusinessDocsSchemesAdamasSeeder::class, //Placement Schemes
            TransactionsAdamasSeeder::class, //9 */
            //TransactionsLogAdamasSeeder::class, //10
            

            //ReferralsYelmoSeeder::class, //5
            //InvoicesYelmoSeeder::class, //11
            //InvoiceTransactionsYelmoSeeder::class, //12
            

            //=============================================
            //FILES FOR RISIKO
            //=============================================
               TreatiesRisikoSeeder::class,
               TreatiesDocRisikoSeeder::class,
               BusinessesRisikoSeeder::class, //1
               LiabilityStructureRisikoSeeder::class, //2
            //Files for Placement Schemes==================
               CschemeRisikoSeeder::class, //6
               CostNodesxRisikoSeeder::class, //4
            //CschemeCnodesRisikoSeeder::class, //7
            //Files for Business Documents=================
               BusinessesDocsRisikoSeeder::class, //Details
               BusinessDocInsuredsRisikoSeeder::class, //Insureds
               BusinessDocsSchemesRisikoSeeder::class, //Placement Schemes
               //TransactionsRisikoSeeder::class, //Transactions
               //TransactionsLogRisikoSeeder::class, //TransactionLogs





            //File of Activity
            /*=============================================*/
            //ActivityLogSeeder::class, /*1*/

        ]);


        // =========================
        // NUEVO: Generar permisos de Shield con base en tus Resources/Pages/Widgets
        // =========================
        
        $this->call([
            // 1️⃣ Permisos custom de Business (acciones especiales)
            CustomPermissionsSeeder::class,

            // 2️⃣ Permisos automáticos de Filament Shield
            ShieldGenerateSeeder::class,

            // 3️⃣ Asignación de permisos a roles
            //UnderwriterBasicRoleSeeder::class,
            RolesAndPermissionsSeeder::class,
         ]);

        

        // === Limpiar caché de permisos
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        \Illuminate\Support\Facades\Artisan::call('permission:cache-reset');
        $this->command?->info('Permissions cache reset.');

      } finally {
         Transaction::$autoBuildLogs = true;
      }
   }
}
