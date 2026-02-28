<?php

namespace Database\Seeders;

use App\Models\company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompaniesSeeder2 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $company=new company();         $company->name = 'Grupo Clc, S.a.';        $company->acronym = 'CLC';        $company->activity = 'CLC is a logistics operator that provides cross-border land transportation services across Central America, focusing on reliable delivery, efficient operations, and personalized customer service to support its clients’ supply chains.';          $company->industry_id = '29';          $company->country_id = '92';         $company->save(); 
        $company=new company();         $company->name = 'Beliv Us';        $company->acronym = 'BEL';        $company->activity = 'Beliv is a fast-growing beverage business unit of Grupo Mariposa, a multinational food and beverage corporation operating across Central America, the Caribbean and South America with a very large product portfolio.';          $company->industry_id = '11';          $company->country_id = '236';         $company->save(); 
        $company=new company();         $company->name = 'Lacteos Balcanicos Glad S.a.';        $company->acronym = 'LAG';        $company->activity = 'Lácteos Balcánicos Glad S.A. is a Guatemala-based company operating in the food and beverage sector, primarily in the production and distribution of dairy products. It is active in both liquid and solid food segments and serves the general food market within Guatemala.';          $company->industry_id = '11';          $company->country_id = '92';         $company->save(); 
        $company=new company();         $company->name = 'Naturalismo S.a.';        $company->acronym = 'NAT';        $company->activity = 'Naturalísimo S.A. is a food and beverage company based in Guatemala that specializes in the production, marketing, and distribution of natural fruit beverages and juices. The company focuses on creating 100% natural fruit juices without preservatives, extracted from high-quality regional fruits for local consumption.';          $company->industry_id = '11';          $company->country_id = '92';         $company->save(); 
        $company=new company();         $company->name = 'Beliv Llc';        $company->acronym = 'BEV';        $company->activity = 'Beliv LLC (often referred to simply as Beliv) is a beverage company focused on developing, producing, and marketing a wide range of natural, functional, and healthier drinks with Latin American inspiration. The company’s mission is to “surprise the world with the best of nature through beverages that do good,” emphasizing innovation, natural ingredients, and a consumer-centric approach.';          $company->industry_id = '11';          $company->country_id = '180';         $company->save(); 
        $company=new company();         $company->name = 'Bebida Norte S.a.c.';        $company->acronym = 'BEN';        $company->activity = 'Bebida Norte S.A.C. is a Peruvian company based in Lima that operates in the production of non-alcoholic beverages, including carbonated soft drinks and bottled waters. It is legally registered as a Sociedad Anónima Cerrada and began operations in April 2022. The company’s activities focus on beverage manufacturing under standardized processes in the food and drinks sector.';          $company->industry_id = '11';          $company->country_id = '175';         $company->save(); 
        $company=new company();         $company->name = 'Cbc Market S.a.c.';        $company->acronym = 'CBM';        $company->activity = 'CBC Market S.A.C. is a Peruvian wholesale distribution company headquartered in Lima. It operates in the grocery and related product merchant wholesalers sector, focusing on the commercialization and distribution of food, beverage, and consumer goods to retail and business channels. The company supports supply chain operations, inventory management, and route-to-market execution for brands and products that require broad market reach in Peru.';          $company->industry_id = '11';          $company->country_id = '175';         $company->save(); 
        $company=new company();         $company->name = 'T & S Logistics S.a.c.';        $company->acronym = 'TSL';        $company->activity = 'T & S Logistics S.A.C. is a Peruvian logistics and transportation company based in Lima. It operates within the transport and logistics services sector, providing services related to the organization and support of cargo transportation. The company is registered as a private corporation (Sociedad Anónima Cerrada), and its activities include logistical support linked to transportation services in Peru.';          $company->industry_id = '11';          $company->country_id = '175';         $company->save(); 

    }
}
