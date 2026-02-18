<?php

namespace Database\Seeders;

use App\Models\company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompaniesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $company = new Company();
        $company->name = 'Abastos Y Distribuciones Institucionales, S.A. de C.V.';
        $company->acronym = 'ABADI';
        $company->activity = 'Distribución mayorista de una amplia gama de productos de abarrotes a minoristas y establecimientos de servicios de alimentos.';
        $company->industry_id = '13';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Aceites Especiales, S.A. de C.V.';
        $company->acronym = 'ACE_ESPE';
        $company->activity = 'Manufacturing of Vegetable Oil: Involves the extraction and refining of natural vegetable oils, including avocado, sunflower, canola, and safflower oils, through advanced processing techniques.';
        $company->industry_id = '13';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Aceites y Proteínas, S.A. de C.V.';
        $company->acronym = 'ACE_PROT';
        $company->activity = 'Manufacturing and Distribution of Vegetable Oils and Fats: Specializes in the production and distribution of a variety of vegetable oils and fats for both culinary and industrial use.';
        $company->industry_id = '13';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Adelante Distribuciones, S.A. de C.V.';
        $company->acronym = 'ADELANTE';
        $company->activity = 'Company Dedicated to Retail Trade and Distribution: Focuses on the retail trade and distribution of cleaning supplies, wholesale pharmaceutical products, milk, fabric, and the manufacturing of carpets and rugs.';
        $company->industry_id = '26';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Administración Del Gobierno de La Ciudad de México';
        $company->acronym = 'ADM_GOB_CDMX';
        $company->activity = 'Public Administration: Manages government operations, policy implementation, and public service provision at various levels of government.';
        $company->industry_id = '14';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Sistema de Transporte Colectivo Metro';
        $company->acronym = 'STC_METRO';
        $company->activity = 'Collective Transport: Provides public transportation services, including buses, trains, and other forms of mass transit to facilitate urban and regional travel.';
        $company->industry_id = '14';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Agencia Comercializadora Industrial Del Sureste, S.A. de C.V.';
        $company->acronym = 'AG_COM_ISUR';
        $company->activity = 'Distributor of Automotive Parts and Oils: Specializes in the wholesale distribution of automotive parts, oils, additives, and lubricants to repair shops and automotive service centers.';
        $company->industry_id = '4';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Agencias Mercantiles, S.A. de C.V.';
        $company->acronym = 'AGE_MERC';
        $company->activity = 'International Brand Diesel Truck Distributor: The largest distributor in the country for international diesel truck brands, with service coverage across multiple states including Yucatán, Campeche, Quintana Roo, Tabasco, Chiapas, Puebla, and Tlaxcala.';
        $company->industry_id = '16';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Agrícola Amajac, S.A. de C.V.';
        $company->acronym = 'AGR_AMAJ';
        $company->activity = 'Fruit & Vegetable Store: Operates retail locations focused on the sale of fresh fruits and vegetables, providing quality produce to local customers.';
        $company->industry_id = '3';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Alimentos Envasados Nutriland, S.A. de C.V.';
        $company->acronym = 'NUTRILAND';
        $company->activity = 'Wholesale Trade Intermediaries: Acts as a bridge in the wholesale trade process, facilitating transactions between manufacturers and retailers to streamline supply chains.';
        $company->industry_id = '9';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Alimentos y Servicios Integrales Alservi, S.A. de C.V.';
        $company->acronym = 'ALSERVI';
        $company->activity = 'Food Services Provider: Supplies and distributes products specifically for food service operations, ensuring a steady supply of necessary food items to service providers.';
        $company->industry_id = '13';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Alimentos y Servicios Integrales Alservi, S.A. de C.V.';
        $company->acronym = 'ALSERVI';
        $company->activity = 'Food Services Provider: Supplies and distributes products specifically for food service operations, ensuring a steady supply of necessary food items to service providers.';
        $company->industry_id = '13';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Almacenes y Frigoríficos Ameriben, S.A. de C.V.';
        $company->acronym = 'AMERIBEN';
        $company->activity = 'Refrigerated Storage Services: Offers storage solutions in refrigerated cold rooms, providing temperature-controlled environments for perishable goods with added services for transportation, loading, and unloading.';
        $company->industry_id = '6';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Almacenes y Frigoríficos Sterling, S.A. de C.V.';
        $company->acronym = 'STERLING';
        $company->activity = 'Public Storage Services in Refrigerators: Provides public storage facilities in refrigerated chambers with independent temperature and humidity controls, including logistics support for transportation, loading, and unloading.';
        $company->industry_id = '6';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Asv Argentina Salud, Vida y Patrimoniales Compañía de Seguros, S.A.';
        $company->acronym = 'ASV_ARG';
        $company->activity = 'Insurance Carrier: Provides a range of insurance products including life, health, property, and casualty coverage to protect individuals and businesses.';
        $company->industry_id = '17';
        $company->country_id = '11';
        $company->save();

        $company = new Company();
        $company->name = 'Automotores Seúl, S.A. de C.V.';
        $company->acronym = 'AUTO_SEUL';
        $company->activity = 'Car Storage and Mechanical Workshops: Specializes in the storage and sale of new and used cars, alongside offering mechanical repair and maintenance services through workshops.';
        $company->industry_id = '4';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Azucarera la Grecia, S.A. de C.V.';
        $company->acronym = 'AZUC_GREC';
        $company->activity = 'Sugar and Alcohol Production: Engages in the production of various types of sugar, honey, and alcohols, as well as generating electrical energy as a byproduct.';
        $company->industry_id = '2';
        $company->country_id = '100';
        $company->save();

        $company = new Company();
        $company->name = 'Bachoco';
        $company->acronym = 'BACHOCO';
        $company->activity = 'Largest Egg Producer in Mexico: Recognized as the largest producer of eggs in Mexico, involved in egg production and distribution.';
        $company->industry_id = '13';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Banco Nacional de Obras y Servicios Publicos, S.N.C Institución de Banca de Desarrollo.';
        $company->acronym = 'BANOBRAS';
        $company->activity = 'Development Banking in Mexico: Focuses on creating high-impact infrastructure projects with significant social benefits, supported by innovative financing methods promoted by the Federal Government.';
        $company->industry_id = '12';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Banco Nacional de Obras y Servicios Públicos, S.N.C. Fondo de Desastres Naturales';
        $company->acronym = 'FONDE_NAT';
        $company->activity = 'Worker Credit Services: Provides credit solutions to workers for purchasing goods and services, facilitating financial access for personal and professional needs.';
        $company->industry_id = '14';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Bardahl de México, S.A. de C.V.';
        $company->acronym = 'BARDAHL';
        $company->activity = 'Leading Lubricants Manufacturer: Known for producing high-quality lubricants, greases, fluids, and additives, serving key industries such as agriculture, transportation, mining, and construction.';
        $company->industry_id = '4';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Bebidas Internacionales Bepensa, S.A. de C.V.';
        $company->acronym = 'BEB_INTERN';
        $company->activity = 'Alcoholic Beverages Company: Engages in the comprehensive process of purchasing, manufacturing, bottling, aging, distributing, importing, exporting, and retailing a wide range of alcoholic beverages.';
        $company->industry_id = '13';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Beneficiadora La Paz, S.A. de C.V.';
        $company->acronym = 'BENE_LPAZ';
        $company->activity = 'Crude Ore Purchase: Specializes in the acquisition and trading of crude ore for various industrial and commercial applications.';
        $company->industry_id = '16';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Bepensa Bebidas, S.A. de C.V.';
        $company->acronym = 'BEPENSA';
        $company->activity = 'Bottled Water and Beverage Production: Handles the manufacturing, storage, transportation, and marketing of purified bottled water, juices, concentrates, dairy products, and cleaning supplies.';
        $company->industry_id = '13';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Bepensa Motriz, S.A. de C.V.';
        $company->acronym = 'BEP_MOTRIZ';
        $company->activity = 'Automobile Leasing and Service: Leads in the leasing, servicing, and commercialization of automobiles, trucks, engines, generation plants, and industrial equipment, including spare parts distribution.';
        $company->industry_id = '4';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Bimbo';
        $company->acronym = 'BIMBO';
        $company->activity = 'Largest Baking Company in the World: The world\'s largest company specializing in baking products and services.';
        $company->industry_id = '13';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Bio Etanol, S.A.';
        $company->acronym = 'BIO_ETAN';
        $company->activity = 'Chemical Sector Company in Guatemala: Based in Guatemala City, operates within the chemicals sector focusing on petrochemicals, plastics, and perfumes.';
        $company->industry_id = '24';
        $company->country_id = '92';
        $company->save();

        $company = new Company();
        $company->name = 'Carburantes de Yucatán, S.A. de C.V.';
        $company->acronym = 'CARB_YUC';
        $company->activity = 'Authorized PEMEX Marketer: Serves as an authorized marketer and distributor of PEMEX products, including fuels and lubricants.';
        $company->industry_id = '24';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Carburantes del Caribe, S.A. de C.V.';
        $company->acronym = 'CARB_CAR';
        $company->activity = 'Authorized PEMEX Marketer: Serves as an authorized marketer and distributor of PEMEX products, including fuels and lubricants.';
        $company->industry_id = '24';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'CBC Peruana S.A.C.';
        $company->acronym = 'CBC_PERU';
        $company->activity = 'Beverage Producer: Specializes in the production and distribution of a variety of beverages for consumer markets.';
        $company->industry_id = '13';
        $company->country_id = '175';
        $company->save();

        $company = new Company();
        $company->name = 'Ceda Foods, S.A. de C.V.';
        $company->acronym = 'CEDA_FOODS';
        $company->activity = 'Industry, Production, and Manufacturing: Engages in diverse manufacturing processes across various industry sectors.';
        $company->industry_id = '16';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Centro de Promociones Los Cabos San Lucas, S.A. de C.V.';
        $company->acronym = 'CEN_PROM';
        $company->activity = 'Holiday Centers and Construction of Tourist Complexes: Engages in the development and management of holiday centers and tourist complexes, aiming to create appealing destinations for leisure and tourism activities.';
        $company->industry_id = '30';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Cinepolis de México, S.A. de C.V.';
        $company->acronym = 'CINEPOLIS';
        $company->activity = 'Entertainment Services: Offers a wide range of entertainment options, including cinemas, video games, food and beverage services, confectionery, internet content rental, and operates shopping malls. Also involved in altruistic activities and real estate management.';
        $company->industry_id = '18';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Combustibles de Yucatán, S.A. de C.V.';
        $company->acronym = 'COMB_YUC';
        $company->activity = 'Fuel Service Station: Operates stations providing gasoline, diesel, and other fuel-related services to customers.';
        $company->industry_id = '21';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Combustibles Del Caribe, S.A. de C.V.';
        $company->acronym = 'COMB_CAR';
        $company->activity = 'Petroleum Derivatives Group: Consists of companies involved in the transportation, storage, distribution, and commercialization of petroleum derivatives.';
        $company->industry_id = '21';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Combustibles del Sureste, S.A. de C.V.';
        $company->acronym = 'COMB_SUR';
        $company->activity = 'Authorized PEMEX Marketer and Distributor: An official distributor of PEMEX products, including fuels and lubricants, ensuring their availability and distribution.';
        $company->industry_id = '24';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Comercializadora de Productos Panor Ltda.';
        $company->acronym = 'PANOR_LTD';
        $company->activity = 'Sugar Production Company: Specializes in the production of various types of sugar, serving both culinary and industrial sectors.';
        $company->industry_id = '6';
        $company->country_id = '45';
        $company->save();

        $company = new Company();
        $company->name = 'Comercializadora Agrícola y de Bienes Landsmark, S.A. de C.V.';
        $company->acronym = 'LANDSMARK';
        $company->activity = 'Real Estate Management Services: Provides comprehensive services for managing real estate, including property administration, leasing, and development.';
        $company->industry_id = '6';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Comercializadora Milenio, S.A. de C.V.';
        $company->acronym = 'COMM_MILEN';
        $company->activity = 'Electronic Audio and Video Products Distributor: Distributes and sells electronic audio and video products. Diamond Electronics is a manufacturer and distributor of multimedia products for brands such as Mitsui, Lexus, and Polaroid.';
        $company->industry_id = '9';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Comercializadora Publicitaria Tik S.A. de C.V.';
        $company->acronym = 'COMM_TIK';
        $company->activity = 'Advertising, Public Relations, and Related Services: Operates in the advertising, public relations, and related services industry, with 165 companies under the Comercializadora Publicitaria Tik, S.A. de C.V. corporate family.';
        $company->industry_id = '18';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Comisión Federal de Electricidad';
        $company->acronym = 'CFE';
        $company->activity = 'Federal Electricity Commission: A non-profit public company providing electric power services, crucial for national development and public welfare.';
        $company->industry_id = '24';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Compañía de Jarabes y Bebidas Gaseosas La Mariposa, S.A';
        $company->acronym = 'GAS_MARIP';
        $company->activity = 'Beverage Producer: Involved in the production and distribution of a diverse range of beverages for various markets.';
        $company->industry_id = '13';
        $company->country_id = '92';
        $company->save();

        $company = new Company();
        $company->name = 'Comunicación Segura, S.A. de C.V.';
        $company->acronym = 'COM_SEC';
        $company->activity = 'Engineering Services: Provides evaluation, development, and implementation of projects in telecommunications, security, macro measurement, and telemetry.';
        $company->industry_id = '10';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Comisión Nacional del Agua';
        $company->acronym = 'CONAGUA';
        $company->activity = 'Decentralized Administrative Body of the Ministry of Environment and Natural Resources: A governmental organization managing environmental and natural resource policies.';
        $company->industry_id = '14';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Control Integral de Combustibles, S.A. de C.V.';
        $company->acronym = 'CTRL_COMB';
        $company->activity = 'Electronic Purse for Fleet Fuel Control: Offers electronic solutions for managing and controlling fleet fuel purchases and consumption.';
        $company->industry_id = '21';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Corporación Nacional de Telecomunicaciones, CNT EP';
        $company->acronym = 'CNT_TELE';
        $company->activity = 'National Telecommunications Corporation (CNT EP): An Ecuadorian state-owned telecommunications company offering a range of telecom services since its establishment in 2008.';
        $company->industry_id = '28';
        $company->country_id = '65';
        $company->save();

        $company = new Company();
        $company->name = 'Corporativo Gasolinero Del Caribe, S.A. de C.V.';
        $company->acronym = 'CORP_GAS_CAR';
        $company->activity = 'Fuel Service Station Operator: Manages and operates fuel service stations providing gasoline, diesel, and related products.';
        $company->industry_id = '21';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Cosmopolita de Polanco, S.A. de C.V.';
        $company->acronym = 'COSMOPOL';
        $company->activity = 'Technical, Professional, and Administrative Services: Provides a variety of technical, professional, and administrative support services to commercial and industrial sectors.';
        $company->industry_id = '6';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Dalton Automotores, S. de R.L. de C.V.';
        $company->acronym = 'DAL_AUTM';
        $company->activity = 'Administrative Services to Car and Real Estate Agencies: Specializes in providing administrative support to car dealerships and real estate agencies.';
        $company->industry_id = '4';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Dalton Automotriz, S. de R.L. de C.V.';
        $company->acronym = 'DAL_AUTZ';
        $company->activity = 'Administrative Services to Car and Real Estate Agencies: Specializes in providing administrative support to car dealerships and real estate agencies.';
        $company->industry_id = '4';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Dalton Efectivo Seguro Gdl, S.A. de C.V.';
        $company->acronym = 'DAL_EF_SEC';
        $company->activity = 'Car Rental Company: Offers vehicle rental services for short-term and long-term needs, catering to both individual and business clients.';
        $company->industry_id = '4';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Dalton Servicios, S.A. de C.V.';
        $company->acronym = 'DAL_SERV';
        $company->activity = 'Administrative Services to Car and Real Estate Agencies: Provides comprehensive administrative support to car dealerships and real estate agencies, including management and operational services.';
        $company->industry_id = '4';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Daosa, S.A. de C.V.';
        $company->acronym = 'DAOSA';
        $company->activity = 'Automotive Parts Manufacturing: Specializes in the production of various automotive parts, catering to both domestic and international markets.';
        $company->industry_id = '4';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Diesel Cancún, S.A. de C.V.';
        $company->acronym = 'DIESEL_CAN';
        $company->activity = 'Fuel and Diesel Distributor: Engages in the distribution of fuel and diesel products, serving a range of industrial, commercial, and consumer needs.';
        $company->industry_id = '21';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Dieselera de Mérida, S.A. de C.V.';
        $company->acronym = 'DIESEL_MER';
        $company->activity = 'Fuel Service Station Operator: Manages and operates stations providing gasoline, diesel, and other fuel-related services.';
        $company->industry_id = '21';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Dinant Holding Corporation, S.A.';
        $company->acronym = 'DINANT';
        $company->activity = 'Honduran-Origin Consumer Goods Company: A company with origins in Honduras, with a presence across Central America, the Caribbean, and the United States. Known for offering high-quality products for mass consumption at competitive prices. Main divisions include snacks, fats and oils, foods, housekeeping, and agricultural business.';
        $company->industry_id = '21';
        $company->country_id = '172';
        $company->save();

        $company = new Company();
        $company->name = 'Distribuidora Megamak, S.A. de C.V.';
        $company->acronym = 'MEGAMAK';
        $company->activity = 'Machinery for Construction and Agriculture: Sells innovative and specialized machinery designed for construction and agricultural applications.';
        $company->industry_id = '16';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'E-Transports, S.A. de C.V.';
        $company->acronym = 'E_TRANSP';
        $company->activity = 'Logistics, Customs, and Foreign Trade Services: Offers a range of services related to logistics, customs, foreign trade consulting, and the transportation of goods by various routes and means.';
        $company->industry_id = '29';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'El Carmen, S.A.';
        $company->acronym = 'EL_CARMEN';
        $company->activity = 'Beverage Producer: Engaged in the production and distribution of a variety of beverages for different markets.';
        $company->industry_id = '13';
        $company->country_id = '11';
        $company->save();

        $company = new Company();
        $company->name = 'El Sol Seguros S.A.';
        $company->acronym = 'SOL_SEGU';
        $company->activity = 'Insurance Carrier: Provides various insurance products and services, including coverage for personal, commercial, and industrial needs.';
        $company->industry_id = '17';
        $company->country_id = '174';
        $company->save();

        $company = new Company();
        $company->name = 'Embotelladora La Mariposa, S.A.';
        $company->acronym = 'EMB_MARIP';
        $company->activity = 'Beverage Producer: Involved in producing and distributing beverages across multiple segments.';
        $company->industry_id = '13';
        $company->country_id = '92';
        $company->save();

        $company = new Company();
        $company->name = 'Embotelladora La Reyna, S.A. de C.V.';
        $company->acronym = 'EMB_REYNA';
        $company->activity = 'Beverage Producer: Specializes in the production and distribution of a diverse range of beverages.';
        $company->industry_id = '13';
        $company->country_id = '100';
        $company->save();

        $company = new Company();
        $company->name = 'Embotelladora Nacional, S.A.';
        $company->acronym = 'EMB_NAC';
        $company->activity = 'Beverage Producer: Focuses on producing and distributing a wide assortment of beverage products.';
        $company->industry_id = '13';
        $company->country_id = '160';
        $company->save();

        $company = new Company();
        $company->name = 'Embotelladoras Bepensa, S.A. de C.V.';
        $company->acronym = 'EMB_BEPEN';
        $company->activity = 'Coca-Cola Brand Distributor: Produces, markets, and distributes a portfolio of 35 Coca-Cola Company brands.';
        $company->industry_id = '13';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Empresas Matic, S.A. de C.V.';
        $company->acronym = 'EMP_MATIC';
        $company->activity = 'Administrative Services for Service Station Personnel: Manages administrative functions related to personnel at fuel service stations.';
        $company->industry_id = '21';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Estación de Servicios Caribe Real S.A. de C.V.';
        $company->acronym = 'EST_CAR_REAL';
        $company->activity = 'Fuel Service Station: Operates a station that provides gasoline, diesel, and other fuel-related services to customers.';
        $company->industry_id = '21';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Estación de Servicios Circuito Colonias, S.A. de C.V.';
        $company->acronym = 'EST_CIR_COL';
        $company->activity = 'Gasoline and Diesel Retail Trade: Involves the retail trade of gasoline and diesel products.';
        $company->industry_id = '26';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Estación de Servicios del Caribe, S.A. de C.V.';
        $company->acronym = 'EST_SERV_CAR';
        $company->activity = 'Fuel Service Station: Manages a facility offering gasoline and diesel fuel to consumers.';
        $company->industry_id = '21';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Estación de Servicios Las Palmas, S.A. de C.V.';
        $company->acronym = 'EST_SERV_PAL';
        $company->activity = 'Fuel Service Station: Manages a facility offering gasoline and diesel fuel to consumers.';
        $company->industry_id = '21';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Estación de Servicios Maraveg, S.A. de C.V.';
        $company->acronym = 'EST_SERV_MAR';
        $company->activity = 'Retail of Gasoline and Diesel: Focuses on the retail sale of gasoline and diesel fuel products.';
        $company->industry_id = '21';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Estación de Servicios Mericam, S.A. de C.V.';
        $company->acronym = 'EST_SERV_MERI';
        $company->activity = 'Authorized PEMEX Marketer and Distributor: Officially authorized distributor of PEMEX products, including fuels and lubricants.';
        $company->industry_id = '24';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Estación de Servicios Palmas Cancún, S.A. de C.V.';
        $company->acronym = 'EST_SERV_PAL_CAN';
        $company->activity = 'Retail of Gasoline and Diesel: Engages in the retail sale of gasoline and diesel fuels.';
        $company->industry_id = '21';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Estación de Servicios Palmas Caribe, S.A. de C.V.';
        $company->acronym = 'EST_SERV_PAL_CAR';
        $company->activity = 'Retail of Gasoline and Diesel: Provides gasoline and diesel fuels through retail outlets.';
        $company->industry_id = '21';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Fármacos Darovi, S.A. de C.V.';
        $company->acronym = 'FAR_DAROVI';
        $company->activity = 'Comprehensive Supply and Administration of Medicines: Offers complete supply and administrative services for medicines and healing materials to both public and private institutions.';
        $company->industry_id = '23';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'FB Distribuciones, S de R.L. de C.V.';
        $company->acronym = 'FB_DIST';
        $company->activity = 'Wholesale Trade and Distribution: Specializes in the wholesale trade and distribution of products including perfumery items, toys, pharmaceutical products, snacks, and fried foods via mass media channels such as mail and the internet.';
        $company->industry_id = '26';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Fly Services, S.A. de C.V.';
        $company->acronym = 'FLY_SERV';
        $company->activity = 'Air Transport Services by Helicopter: Provides specialized air transport services using helicopters.';
        $company->industry_id = '29';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Fomento CC, S de R.L de C.V.';
        $company->acronym = 'FOMENTO_CC';
        $company->activity = 'Grupo Hotel Shops Payroll Management: A subsidiary responsible for handling part of Grupo Hotel Shops\' payroll, which focuses on hotel retail, including the commercialization of jewelry, tobacco, boutique items, photography, and artisan markets, with over 100 branches in Cancun, Riviera Maya, Mazatlán, Cabos, and Jamaica.';
        $company->industry_id = '9';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Fomento Gasolinero, S.A. de C.V.';
        $company->acronym = 'FOM_GAS';
        $company->activity = 'Fuel Service Station Operator: Operates fuel service stations providing gasoline, diesel, and other fuel products.';
        $company->industry_id = '21';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Fondo de Ahorro Capitalizable';
        $company->acronym = 'FON_ACAP';
        $company->activity = 'Capitalizable Savings Fund for Public Service Workers: Offers a financial stimulus program for public service workers in Mexico, providing benefits through the Capitalizable Savings Fund for Workers in the Service of the State (Fonac) for the 2023 cycle.';
        $company->industry_id = '14';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Fotur Jamaica Limited';
        $company->acronym = 'FOTUR_JAM';
        $company->activity = 'Other Personal Services (FOTUR LIMITED): Involved in various personal services sectors including automotive repair and maintenance, business services, professional and labor organizations, commercial and industrial machinery repair, and dry cleaning and laundry services.';
        $company->industry_id = '6';
        $company->country_id = '112';
        $company->save();

        $company = new Company();
        $company->name = 'Garpa Arrenda, S.A. de C.V.';
        $company->acronym = 'GARPA';
        $company->activity = 'Passenger Car Rental: Provides rental services for passenger cars, catering to various short-term and long-term transportation needs.';
        $company->industry_id = '14';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'GDSConnections, S.A. de C.V.';
        $company->acronym = 'GDS_CONN';
        $company->activity = 'Crane, loading and maneuvering service throughout the republic: Exceptional nationwide crane services ensuring efficient and safe loading and maneuvering.';
        $company->industry_id = '6';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'GF Bepensa, S.A. de C.V.';
        $company->acronym = 'GF_BEPEN';
        $company->activity = 'Loans for cars, machinery and trucks that are distributed by the company: Reliable and flexible financial solutions for acquiring cars, machinery, and trucks.';
        $company->industry_id = '5';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'GF Servicios Corporativos, S de R.L. de C.V.';
        $company->acronym = 'GF_SERV_CORP';
        $company->activity = 'Hotel retail company, which is dedicated to the commercialization of jewelry, tobacco, boutique, photography, artisan markets, among others with more than 100 branches throughout Cancun, Riviera Maya, Mazatlán, Los Cabos and Jamaica.: Prominent hotel retail chain offering an exquisite range of products including jewelry, boutique items, and artisan crafts, with extensive coverage across popular vacation destinations.';
        $company->industry_id = '26';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Gobierno de La Ciudad de México';
        $company->acronym = 'GOB_CDMX';
        $company->activity = 'Public administration.: Dedicated to efficient and transparent management of public services and resources.';
        $company->industry_id = '14';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'GPF Servicios Corporativos S de R.L. de C.V.';
        $company->acronym = 'GPF_SERV';
        $company->activity = 'Hotel retail company, which is dedicated to the commercialization of jewelry, tobacco, boutique, photography, artisan markets, among others with more than 100 branches throughout Cancun, Riviera Maya, Mazatlán, Los Cabos and Jamaica.: Renowned for its extensive network of branches, offering premium products and services in top tourist destinations.';
        $company->industry_id = '26';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Gran Armee Del Cabo, S.A. de C.V. And/Or Operadora de Campos de Golf Pueblo Bonito, S.A. de C.V.';
        $company->acronym = 'GRAN_ARME';
        $company->activity = 'Offers workers in the country credits for the acquisition of goods and services.: Supports workers with accessible credit options to enhance their purchasing power for various goods and services.';
        $company->industry_id = '12';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Grupo Agroindustrial Numar, S.A.';
        $company->acronym = 'GRUPO_NUMAR';
        $company->activity = 'Is a conglomerate that produces and markets its own brands in the category of butters, margarines, vegetable oils and coffee.: Leading producer and marketer of high-quality butters, margarines, vegetable oils, and coffee brands.';
        $company->industry_id = '16';
        $company->country_id = '54';
        $company->save();

        $company = new Company();
        $company->name = 'Grupo Ideal';
        $company->acronym = 'GRUPO_IDEAL';
        $company->activity = 'Development of infrastructure projects.: Expertise in the planning and execution of impactful infrastructure projects.';
        $company->industry_id = '8';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Grupo Inmobiliario Tervia, S.C.';
        $company->acronym = 'GRUPO_TERVIA';
        $company->activity = 'Purchase and sale of all kinds of real estate, leasing and subleasing of all kinds of movable and immovable property, building and construction of houses, office and residential buildings, the construction of all kinds of commercial complexes.: Comprehensive real estate services including transactions, leasing, construction, and development.';
        $company->industry_id = '25';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Grupo Jaremar';
        $company->acronym = 'GRUPO_JARE';
        $company->activity = 'Jaremar Group, agroindustrial sector, works with national independent producers and surrounding communities, for the development of their oil palm plantations. It also manufactures home care products, flour, cookies and culinary products.: Agroindustrial leader supporting local producers and communities while offering a diverse range of products.';
        $company->industry_id = '2';
        $company->country_id = '100';
        $company->save();

        $company = new Company();
        $company->name = 'Grupo México S.A. de C.V.';
        $company->acronym = 'GR_MEXICO';
        $company->activity = 'Is a Mexican conglomerate that operates three divisions (Minera México, Grupo México Transportes and Grupo México infraestructura) and Fundación Grupo México.: Major Mexican conglomerate with diversified operations in mining, transportation, infrastructure, and social impact through its foundation.';
        $company->industry_id = '20';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Grupo Profesional de Administración y Consultoría, S.C';
        $company->acronym = 'GRUPO_GP_AC';
        $company->activity = 'Providing technical, professional and administrative services to commercial and industrial companies.: Offers comprehensive technical and administrative support to enhance commercial and industrial operations.';
        $company->industry_id = '6';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Grupo Seguritech Privada, S.A.P.I. de C.V.';
        $company->acronym = 'GR_SEGUR';
        $company->activity = 'Serving private, corporate and government clients in the American continent, they contribute to the achievement of National Security projects.: Dedicated to supporting national security and achieving key projects across the American continent.';
        $company->industry_id = '6';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Grupo Televisa, S.A.B. de C.V.';
        $company->acronym = 'GR_TELEVISA';
        $company->activity = 'Mexican media company. This company is involved in the production and transmission of television programs.: Prominent media company specializing in the production and broadcasting of engaging television content.';
        $company->industry_id = '28';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Grupo Vical';
        $company->acronym = 'GR_VICAL';
        $company->activity = 'VICAL is the Central American Glass group mainly focused in manufacturing and marketing glass containers. Its operation starts in 1964; satisfying the Central American and Export markets needs.: Established Central American leader in glass container manufacturing, serving both regional and international markets.';
        $company->industry_id = '8';
        $company->country_id = '54';
        $company->save();

        $company = new Company();
        $company->name = 'Hidrocarburos Del Sureste, S.A. de C.V.';
        $company->acronym = 'HIDRO_SURE';
        $company->activity = 'Receipt, storage and delivery of gasoline, diesel and jet fuel.: Expert services in the receipt, storage, and distribution of fuel products.';
        $company->industry_id = '21';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Hspro, S de R.L. de C.V.';
        $company->acronym = 'HSPRO';
        $company->activity = 'Hotel retail company, which is dedicated to the commercialization of jewelry, tobacco, boutique, photography, artisan markets, among others with more than 100 branches throughout Cancun, Riviera Maya, Mazatlán, Los Cabos and Jamaica.: A well-established retail chain with extensive branches in top tourist destinations, offering a variety of premium products.';
        $company->industry_id = '30';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Industrial La Fama, S.A. de C.V.';
        $company->acronym = 'IND_LA_FAMA';
        $company->activity = 'Manufacturing of laundry soaps, glycerin, bleaches and detergents.: Specializes in the production of high-quality laundry soaps, glycerin, bleaches, and detergents.';
        $company->industry_id = '6';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Ingenio Concepción, S.A.';
        $company->acronym = 'ING_CONCEP';
        $company->activity = 'Is an Argentine company that is located in the city of Banda del Rio Sali in the province of Tucuman. The company operates in the sugar cane growing industry.: Argentine leader in the sugar cane industry, contributing significantly to regional agriculture.';
        $company->industry_id = '2';
        $company->country_id = '11';
        $company->save();

        $company = new Company();
        $company->name = 'Ingenio el Mante, S.A. de C.V.';
        $company->acronym = 'ING_MANTE';
        $company->activity = 'Company dedicated to the planting of sugar cane and its use for sugar refining.: Focused on cultivating sugar cane and refining it into high-quality sugar products.';
        $company->industry_id = '2';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Ingenio La Cabaña, S.A. de C.V.';
        $company->acronym = 'ING_CABANA';
        $company->activity = 'Company in the sugar sector.: Significant player in the sugar industry with a commitment to quality and sustainability.';
        $company->industry_id = '2';
        $company->country_id = '49';
        $company->save();

        $company = new Company();
        $company->name = 'Ingenio La Unión, S.A.';
        $company->acronym = 'ING_UNION';
        $company->activity = 'Develop energy based on cane, sugar.: Innovative company utilizing sugar cane for energy production.';
        $company->industry_id = '2';
        $company->country_id = '92';
        $company->save();

        $company = new Company();
        $company->name = 'Ingenio Magdalena';
        $company->acronym = 'ING_MAGDAL';
        $company->activity = 'Food, agricultural and energy products, committed to business sustainability, social and environmental development.: Diverse company dedicated to sustainable practices in food, agriculture, and energy.';
        $company->industry_id = '2';
        $company->country_id = '67';
        $company->save();

        $company = new Company();
        $company->name = 'Ingenio Monte Rosa, S.A.';
        $company->acronym = 'ING_MONTE';
        $company->activity = 'Sugar Mill, Sugar Refinery, Cogeneration, Electricity Generation, Distillery.: Comprehensive operations in sugar milling, refining, cogeneration, electricity production, and distillation.';
        $company->industry_id = '2';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Ingenio Pantaleon, S.A.';
        $company->acronym = 'ING_PANTA';
        $company->activity = 'Sugar Mill, Sugar Refinery, Cogeneration, Electricity Generation, Distillery.: Leading facility in the integrated processing of sugar and energy production.';
        $company->industry_id = '2';
        $company->country_id = '92';
        $company->save();

        $company = new Company();
        $company->name = 'Ingenio Panuco S.A.P.I. de C.V.';
        $company->acronym = 'ING_PANUCO';
        $company->activity = 'Sugar Mill, Sugar Refinery, Cogeneration, Electricity Generation, Distillery.: Renowned for its extensive capabilities in sugar milling, refining, and energy generation.';
        $company->industry_id = '2';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Ingenio San Diego';
        $company->acronym = 'ING_S_DIEGO';
        $company->activity = 'Is a leading company in the production of sugar and energy based in Guatemala and with more than 130 years of operation.: Historic Guatemalan company with over 130 years of leadership in sugar production and energy.';
        $company->industry_id = '2';
        $company->country_id = '92';
        $company->save();

        $company = new Company();
        $company->name = 'Inmobiliaria Del Zazil Ha, S.A. de C.V.';
        $company->acronym = 'INMO_ZAZIL';
        $company->activity = 'Fuel service station.: Reliable fuel service station providing quality fuel solutions.';
        $company->industry_id = '21';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Inmobiliaria Jadica, S.A. de C.V.';
        $company->acronym = 'INMO_JADICA';
        $company->activity = 'Real estate selling.: Expert in real estate sales with a strong market presence.';
        $company->industry_id = '26';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Instituto Costarricense de Electrificación';
        $company->acronym = 'INST_COST';
        $company->activity = 'The Costa Rican Electricity Institute (ICE) is a state company that provides electricity and telecommunications services in Costa Rica.: Central institution in Costa Rica for electricity and telecommunications services, ensuring comprehensive coverage.';
        $company->industry_id = '24';
        $company->country_id = '54';
        $company->save();

        $company = new Company();
        $company->name = 'Instituto de Seguridad Social Al Servicio de Los Trabajadores Del Estado';
        $company->acronym = 'ISSSTE';
        $company->activity = 'Health and social security administration.: Focused on managing and delivering essential health and social security services.';
        $company->industry_id = '14';
        $company->country_id = '155';
        $company->save();

        $company = new Company();
        $company->name = 'Instituto Mexicano del Seguro Social';
        $company->acronym = 'IMSS';
        $company->activity = 'Its mission is to be the basic instrument of social security, established as a national public service, for all workers and their families.: Dedicated to providing fundamental social security services to workers and their families.';
        $company->industry_id = '14';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Instituto Nacional de Electrificación';
        $company->acronym = 'INST_ELECT';
        $company->activity = 'Promoting the rational, efficient and sustainable use of natural resources, promoting the productive and domestic use of electricity generated from native energy sources.: Advocating for sustainable and efficient use of natural resources and native energy sources.';
        $company->industry_id = '24';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Interprotección';
        $company->acronym = 'INTERPRO';
        $company->activity = 'Insurance, surety and reinsurance broker.: Specializes in providing comprehensive insurance, surety, and reinsurance services.';
        $company->industry_id = '17';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Invest Port, S. de R.L. de C.V.';
        $company->acronym = 'INVEST_PORT';
        $company->activity = 'Investments.: Focused on strategic investment opportunities and financial growth.';
        $company->industry_id = '9';
        $company->country_id = '92';
        $company->save();

        $company = new Company();
        $company->name = 'Investigación Farmacéutica, S.A. de C.V.';
        $company->acronym = 'INV_FAR';
        $company->activity = 'Manufacturing, distribution and sale of various lines of medicine with their different divisions and medical devices.: Extensive operations in manufacturing, distributing, and selling pharmaceuticals and medical devices.';
        $company->industry_id = '23';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Isla 17 S de R.L. de C.V.';
        $company->acronym = 'ISLA_17';
        $company->activity = 'Hotel retail company, which is dedicated to the commercialization of jewelry, tobacco, boutique, photography, artisan markets, among others with more than 100 branches throughout Cancun, Riviera Maya, Mazatlán, Los Cabos and Jamaica.: Highly regarded hotel retail company with an extensive network offering premium products across top destinations.';
        $company->industry_id = '30';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Ivresse Internacional, S.A. de C.V.';
        $company->acronym = 'IVRESSE';
        $company->activity = 'Purchase, sale, commission, consignment, import, export, distribution, manufacturing, elaboration, processing, preparation, packaging, maquila and in general.: Comprehensive services covering all aspects of trade, manufacturing, and distribution.';
        $company->industry_id = '9';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Kol Tov, S.A. de C.V.';
        $company->acronym = 'KOL_TOV';
        $company->activity = 'Trade in raw materials, finished or semi-finished products.: Expert in the trade of raw materials and various stages of finished products.';
        $company->industry_id = '9';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'La Cosmopolitana, S.A. de C.V.';
        $company->acronym = 'COSMOPOL';
        $company->activity = 'Purchase, sale, import, export and distribution of all kinds of meat, sausages, dairy products, groceries, fruits and vegetables, as well as the provision of comprehensive food and general services, among others.: Versatile supplier specializing in a broad range of food products and comprehensive services.';
        $company->industry_id = '13';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Livsmart Americas, S.A. de C.V.';
        $company->acronym = 'LIVSMART';
        $company->activity = 'Beverage producer.: Innovative company dedicated to crafting high-quality beverages.';
        $company->industry_id = '13';
        $company->country_id = '67';
        $company->save();

        $company = new Company();
        $company->name = 'Lodemo y Asociados, S.C.P.';
        $company->acronym = 'LODEMO_ASOC';
        $company->activity = 'Payroll administrative services, technical and financial advisory and consulting services.: Expert provider of payroll administration and comprehensive advisory services.';
        $company->industry_id = '12';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Luma Gasolinerias, S.A. de C.V.';
        $company->acronym = 'LUMA_GAS';
        $company->activity = 'Retail of gasoline and diesel.: Reliable retailer offering high-quality gasoline and diesel.';
        $company->industry_id = '21';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Mariposa El Salvador, S.A. de C.V.';
        $company->acronym = 'MARIPOSA_SAL';
        $company->activity = 'Beverage producer.: Renowned producer known for a diverse range of exceptional beverages.';
        $company->industry_id = '13';
        $company->country_id = '67';
        $company->save();

        $company = new Company();
        $company->name = 'Megaempack, S.A. de C.V.';
        $company->acronym = 'MEGA_EMP';
        $company->activity = 'Manufacturing of rigid plastic packaging, with high quality processes and technology.: Leading manufacturer of durable and high-quality rigid plastic packaging.';
        $company->industry_id = '16';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Mexarrend';
        $company->acronym = 'MEXARREND';
        $company->activity = 'Its main activity is the leasing and financing of equipment for companies or individuals with business activity. It is also engaged in leasing.: Specializes in leasing and financing equipment for businesses and individuals.';
        $company->industry_id = '12';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Minera Santa María de La Paz y Anexas';
        $company->acronym = 'MIN_SANTA_MAR';
        $company->activity = 'Exploitation of mining estates and sale of crude minerals, mainly copper.: Focused on mining and trading of crude minerals with a specialization in copper.';
        $company->industry_id = '20';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Multiservcios Petrolíferos de Quintana Roo, S.A. de C.V.';
        $company->acronym = 'MULT_PET_QR';
        $company->activity = 'Marketer and distributor authorized by PEMEX.: Authorized marketer and distributor of PEMEX products.';
        $company->industry_id = '24';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Multiservicios Peninsular, S.A. de C.V.';
        $company->acronym = 'MULT_PEN';
        $company->activity = 'Marketer and distributor authorized by PEMEX.: Trusted distributor of PEMEX products with extensive market reach.';
        $company->industry_id = '24';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Multiservicios Petrolíferos de Cancún, S.A. de C.V.';
        $company->acronym = 'MULT_PET_CAN';
        $company->activity = 'Marketer and distributor authorized by PEMEX.: Reliable PEMEX distributor ensuring high-quality fuel products.';
        $company->industry_id = '24';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Negocios Alternos CPL, S.A. de C.V.';
        $company->acronym = 'NEG_ALTERN';
        $company->activity = 'Entertainment services, consisting of but not limited to cinemas, video games, preparation and sale of beverages, food, confectionery, internet content rental service, altruistic activities and real estate operator of shopping malls.: Diverse entertainment provider offering cinemas, video games, and various food and beverage options, along with real estate services for shopping malls.';
        $company->industry_id = '18';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Novaforest';
        $company->acronym = 'NOVAFOREST';
        $company->activity = 'Integrated and sustainable agroforestry company based in Guatemala that fosters innovation and offers certified products made from owned renewable resources that protect biodiversity.: Pioneering agroforestry company in Guatemala focused on sustainability and innovation.';
        $company->industry_id = '2';
        $company->country_id = '92';
        $company->save();

        $company = new Company();
        $company->name = 'OHS Limited';
        $company->acronym = 'OHS';
        $company->activity = 'OHS Limited is a market leading independent health and safety consultancy, delivering legal compliance to organisations throughout the UK.: Leading health and safety consultancy in the UK, ensuring legal compliance and organizational safety.';
        $company->industry_id = '15';
        $company->country_id = '235';
        $company->save();

        $company = new Company();
        $company->name = 'Operadora Comercial Liverpool, S.A.B. de C.V.';
        $company->acronym = 'OP_C_LIVER';
        $company->activity = 'Marketing of products for commission owned by third parties.: Specialized in marketing third-party products on a commission basis.';
        $company->industry_id = '9';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Operadora de Comedores Saludables, S.A. de C.V.';
        $company->acronym = 'OP_COM_SAL';
        $company->activity = 'Specialized in canteen services for companies and institutions. It was created and founded in 2019-04, currently 51 to 100 people work in this company or business.: Focused provider of canteen services with a growing team and expertise in institutional food services.';
        $company->industry_id = '13';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Palo Blanco, S.A.';
        $company->acronym = 'PAL_BLANCO';
        $company->activity = 'Sowing, cultivation, packaging, marketing and distribution of agricultural products. Food processing.: Comprehensive agricultural company managing everything from cultivation to food processing and distribution.';
        $company->industry_id = '13';
        $company->country_id = '92';
        $company->save();

        $company = new Company();
        $company->name = 'Pan Filler, S.A. de C.V.';
        $company->acronym = 'PAN_FILLER';
        $company->activity = 'Preparation of other foods (biscuits, pasta for soup and premixed flours).: Expert in the production of various food items including biscuits, pasta, and premixed flours.';
        $company->industry_id = '13';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Pantaleon Chile Spa.';
        $company->acronym = 'PANTA_CHIL';
        $company->activity = 'Sugar Mill, Sugar Refinery, Cogeneration, Electricity Generation, Distillery.: Integrated operations in sugar milling, refining, cogeneration, and energy production.';
        $company->industry_id = '2';
        $company->country_id = '45';
        $company->save();

        $company = new Company();
        $company->name = 'Pasaje Electrónica El Salvador, S.A. de C.V.';
        $company->acronym = 'PAS_ELECTRO';
        $company->activity = 'Dedicated to leasing, subleasing, acquisition in trust, sale, usufruct, administration, construction, subdivision, transformation, remodeling, repair, restoration, preservation of all types of rights and real estate.: Comprehensive real estate services including leasing, acquisition, and property management.';
        $company->industry_id = '9';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Peltur Jamaica Limited';
        $company->acronym = 'PELTUR_JAM';
        $company->activity = 'Buyer and Importer of Boxes with sandals, T-shirts, Photo in USA.: Engaged in importing and purchasing of various goods including sandals and T-shirts.';
        $company->industry_id = '26';
        $company->country_id = '112';
        $company->save();

        $company = new Company();
        $company->name = 'Peninsula 7 S de R.L. de C.V.';
        $company->acronym = 'PENINS_7';
        $company->activity = 'Hotel retail company, which is dedicated to the commercialization of jewelry, tobacco, boutique, photography, artisan markets, among others with more than 100 branches throughout Cancun, Riviera Maya, Mazatlán, Los Cabos and Jamaica.: Well-established retail chain with extensive offerings and numerous branches in prime tourist locations.';
        $company->industry_id = '30';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Pepsi Cola Jamaica Bottling Company Limited';
        $company->acronym = 'PEPSI_JAM';
        $company->activity = 'Beverage producer.: Leading producer specializing in a wide range of beverages.';
        $company->industry_id = '13';
        $company->country_id = '112';
        $company->save();

        $company = new Company();
        $company->name = 'Pepsi Cola Puerto Rico Distributing, LLC';
        $company->acronym = 'PEPSI_PR';
        $company->activity = 'Beverage producer.: Renowned for its diverse and high-quality beverage products.';
        $company->industry_id = '13';
        $company->country_id = '180';
        $company->save();

        $company = new Company();
        $company->name = 'Photopro S de R.L. de C.V.';
        $company->acronym = 'PHOTOPRO';
        $company->activity = 'Hotel retail company, which is dedicated to the commercialization of jewelry, tobacco, boutique, photography, artisan markets, among others with more than 100 branches throughout Cancun, Riviera Maya, Mazatlán, Los Cabos and Jamaica.: Extensive hotel retail network offering a variety of premium products across multiple locations.';
        $company->industry_id = '30';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Photur S de R.L. de C.V.';
        $company->acronym = 'PHOTUR';
        $company->activity = 'Hotel retail company, which is dedicated to the commercialization of jewelry, tobacco, boutique, photography, artisan markets, among others with more than 100 branches throughout Cancun, Riviera Maya, Mazatlán, Los Cabos and Jamaica.: Popular retail chain with a wide array of products and a strong presence in key tourist destinations.';
        $company->industry_id = '30';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Picorp de México, S.A. de C.V.';
        $company->acronym = 'PICORP';
        $company->activity = 'Development of public security infrastructure construction projects.: Expert in the development of critical public security infrastructure projects.';
        $company->industry_id = '10';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Playagas, S.A. de C.V.';
        $company->acronym = 'PLAYAGAS';
        $company->activity = 'Retail of gasoline and diesel.: Consistent retailer of high-quality gasoline and diesel fuels.';
        $company->industry_id = '21';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Plaza López Cotilla, S.A. de C.V.';
        $company->acronym = 'PLAZA_LC';
        $company->activity = 'Purchase and sale of all kinds of real estate, leasing and subleasing of all kinds of movable and immovable property, building and construction of houses, office and residential buildings, the construction of all kinds of commercial complexes.: Comprehensive real estate services including transactions, leasing, and construction.';
        $company->industry_id = '25';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Prestige Motors Certificados, S.A. de C.V.';
        $company->acronym = 'PRESTIGE';
        $company->activity = 'Provision of administrative services to Mercedez-Benz car agencies and real estate agencies.: Provides specialized administrative support to Mercedez-Benz and real estate agencies.';
        $company->industry_id = '4';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Prevención y Readaptación Social';
        $company->acronym = 'PREV_READ';
        $company->activity = 'Public administration.: Focused on effective and transparent public management and service delivery.';
        $company->industry_id = '14';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Productos Serel, S.A. de C.V.';
        $company->acronym = 'PROD_SEREL';
        $company->activity = 'Manufacturing, industrialization, process, purchase, sale, import, export and distribution of all kinds of food and beverages, comprehensive food services, mainly employee canteen service.: A comprehensive provider involved in all stages of the food and beverage supply chain, with a focus on employee canteen services.';
        $company->industry_id = '13';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Promotora Afa, S.A. de C.V.';
        $company->acronym = 'PROMO_AFA';
        $company->activity = 'Real estate rental services.: Specializes in offering diverse rental solutions for various real estate needs.';
        $company->industry_id = '25';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Proveedora de Quimicos y Medicamentos Care Lab, S.A. de C.V.';
        $company->acronym = 'PROV_QUIM';
        $company->activity = 'Integral Logistics Operator in the Pharmaceutical Industry: Expert in managing complex logistics operations specifically for the pharmaceutical industry.';
        $company->industry_id = '23';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Puerto 85 S de R.L. de C.V.';
        $company->acronym = 'PUERTO_85';
        $company->activity = 'Hotel retail company, which is dedicated to the commercialization of jewelry, tobacco, boutique, photography, artisan markets, among others with more than 100 branches throughout Cancun, Riviera Maya, Mazatlán, Los Cabos and Jamaica.: A prominent hotel retail chain offering a wide range of products including jewelry, tobacco, and artisan goods across numerous locations.';
        $company->industry_id = '30';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Puerto Arenas S de R.L. de C.V.';
        $company->acronym = 'PUERTO_ARE';
        $company->activity = 'Hotel retail company, which is dedicated to the commercialization of jewelry, tobacco, boutique, photography, artisan markets, among others with more than 100 branches throughout Cancun, Riviera Maya, Mazatlán, Los Cabos and Jamaica.: A well-established hotel retail network specializing in luxury and artisan products, with extensive coverage in popular tourist areas.';
        $company->industry_id = '30';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Puerto HS S de R.L. de C.V.';
        $company->acronym = 'PUERTO_HS';
        $company->activity = 'Hotel retail company, which is dedicated to the commercialization of jewelry, tobacco, boutique, photography, artisan markets, among others with more than 100 branches throughout Cancun, Riviera Maya, Mazatlán, Los Cabos and Jamaica.: Leading hotel retail company known for its diverse product range and significant presence in major travel destinations.';
        $company->industry_id = '30';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Rechtien International Trucks, Inc.';
        $company->acronym = 'RECHTIEN';
        $company->activity = 'Commercial truck dealership Company.: Focused on the sale and service of commercial trucks, providing both new and used vehicles.';
        $company->industry_id = '16';
        $company->country_id = '236';
        $company->save();

        $company = new Company();
        $company->name = 'Recursos y Soluciones Especializadas Land, S.A. de C.V.';
        $company->acronym = 'REC_SOL_LAND';
        $company->activity = 'Providing technical, professional and administrative services to commercial and industrial companies.: Offers a broad range of technical, professional, and administrative services to support commercial and industrial operations.';
        $company->industry_id = '6';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'RH Land, S.A. de C.V.';
        $company->acronym = 'RH_LAND';
        $company->activity = 'Providing technical, professional and administrative services to commercial and industrial companies.: Delivers specialized services to businesses, ensuring operational efficiency and expertise.';
        $company->industry_id = '6';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Secretaría de Administración y Finanzas de la Ciudad de México';
        $company->acronym = 'SEC_ADM_CDMX';
        $company->activity = 'Is the administrative unit of the government of Mexico City in charge of the dispatch of matters related to the development of income policies and tax administration, programming, budgeting and evaluation of public spending in Mexico City.: Key governmental unit responsible for financial management and public budgeting in Mexico City.';
        $company->industry_id = '14';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Secretaría de La Defensa Nacional';
        $company->acronym = 'SEDENA';
        $company->activity = 'Organization and training of the armed forces, the defense of the country and to help civil society.: Dedicated to the organization, training, and support of the armed forces, focusing on national defense and community assistance.';
        $company->industry_id = '10';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Seguritech Privada, S.A. de C.V.';
        $company->acronym = 'SEGURITECH';
        $company->activity = 'Provides the general public with all kinds of private, industrial and commercial security services.: Comprehensive security service provider for private, industrial, and commercial needs.';
        $company->industry_id = '10';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Servicio Acoro, S.A. de C.V.';
        $company->acronym = 'SERV_ACORO';
        $company->activity = 'Fuel service station.: Reliable fuel station offering quality gasoline and diesel.';
        $company->industry_id = '21';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Servicio Chit, S.A. de C.V.';
        $company->acronym = 'SERV_CHIT';
        $company->activity = 'Fuel service station.: Consistent provider of fuel with reliable service.';
        $company->industry_id = '21';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Servicio de Administración Tributaria';
        $company->acronym = 'SAT';
        $company->activity = 'Entertainment services, consisting of but not limited to cinemas, video games, preparation and sale of beverages, food, confectionery, internet content rental service, altruistic activities and real estate operator of shopping malls.: Diverse entertainment provider including cinemas, video games, and various food and beverage services.';
        $company->industry_id = '14';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Servicios de Consultoría Integrados, S.A.';
        $company->acronym = 'SERV_CONS';
        $company->activity = 'Entertainment services, consisting of but not limited to cinemas, video games, preparation and sale of beverages, food, confectionery, internet content rental service, altruistic activities and real estate operator of shopping malls.: Broad range of entertainment services with a focus on comprehensive customer experiences.';
        $company->industry_id = '6';
        $company->country_id = '92';
        $company->save();

        $company = new Company();
        $company->name = 'Servicios de Personal Cinemas, S.A. de C.V.';
        $company->acronym = 'SERV_CINEMA';
        $company->activity = '';
        $company->industry_id = '18';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Servicios de Personal Novaduo, S.A. de C.V.';
        $company->acronym = 'SERV_NOVAD';
        $company->activity = '';
        $company->industry_id = '18';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'STAR Patria, S.A. de C.V.';
        $company->acronym = 'STAR_PATRIA';
        $company->activity = 'Marketing of new units, spare parts, accessories, maintenance and repair of Mercedes-Benz vehicles.: Specializes in the sales, maintenance, and repair of Mercedes-Benz vehicles.';
        $company->industry_id = '4';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Super Mayoreo Naturista, S.A. de C.V.';
        $company->acronym = 'SUP_MAYO';
        $company->activity = 'Sale of packaged naturist foods, imports and exports, manufacturing, processing and trade.: Dedicated to naturist foods with expertise in manufacturing, trade, and international transactions.';
        $company->industry_id = '9';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Tampico Renewable Energy, S.A.P.I. de C.V.';
        $company->acronym = 'TAMP_RENEW';
        $company->activity = 'Company dedicated to the generation of electrical energy under the cogeneration modality.: Expert in generating electrical energy through cogeneration.';
        $company->industry_id = '24';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Tas Tevel, S.A. de C.V.';
        $company->acronym = 'TAS_TEVEL';
        $company->activity = 'Ground transportation services to companies and individuals.: Provides efficient ground transportation solutions for both companies and individuals.';
        $company->industry_id = '29';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Tenedora de Cines, S.A. de C.V.';
        $company->acronym = 'TEN_CINES';
        $company->activity = 'Entertainment services, consisting of but not limited to cinemas, video games, preparation and sale of beverages, food, confectionery, internet content rental service, altruistic activities and real estate operator of shopping malls.: Comprehensive entertainment services including cinemas, food, and real estate management.';
        $company->industry_id = '18';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'The Central America Bottling Corporation';
        $company->acronym = 'CENT_AMER_BOTT';
        $company->activity = 'Beverage bottling company with the largest portfolio in the region, made up of global brands and with a presence in more than 35 countries.: Leading beverage bottler with an extensive portfolio and international reach.';
        $company->industry_id = '11';
        $company->country_id = '251';
        $company->save();

        $company = new Company();
        $company->name = 'The Tesalia Springs Company S.A.';
        $company->acronym = 'TESALIA_SPR';
        $company->activity = 'Beverage producer.: Renowned producer specializing in high-quality beverages.';
        $company->industry_id = '13';
        $company->country_id = '65';
        $company->save();

        $company = new Company();
        $company->name = 'Transportes Camecam, S.A. de C.V.';
        $company->acronym = 'TRANSP_CAME';
        $company->activity = 'Company dedicated to the establishment and operation of the public motor transport service of cargo, including specialized cargo such as fuel.: Provides public motor transport services with a focus on specialized cargo, including fuel.';
        $company->industry_id = '29';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Transportes Yucarro, S.A. de C.V.';
        $company->acronym = 'TRANSP_YUC';
        $company->activity = 'Transportation of fuels and diesel.: Specializes in the transportation of fuel and diesel products.';
        $company->industry_id = '29';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Tres10, S.A. de C.V.';
        $company->acronym = 'TRES10';
        $company->activity = 'Development of public security infrastructure construction projects.: Focused on developing infrastructure for public security projects.';
        $company->industry_id = '10';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Tron Hermanos, S.A. de C.V.';
        $company->acronym = 'TRON_HERM';
        $company->activity = 'Extraction of pure safflower and canola oils.: Expert in extracting high-quality safflower and canola oils.';
        $company->industry_id = '13';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Typhoon Offshore S.A.P.I de C.V.';
        $company->acronym = 'TYPHOON_OFF';
        $company->activity = 'Third-party contracting activities for well interventions and structure maintenance.: Handles contracting for well interventions and structural maintenance services.';
        $company->industry_id = '21';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'UNIFIN Financiera';
        $company->acronym = 'UNIFIN';
        $company->activity = 'Offer specialized financing to companies such as pure leasing, financial factoring and automotive credit.: Provides specialized financing options including leasing, factoring, and automotive credit.';
        $company->industry_id = '12';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Vamsa Niños Héroes, S.A. de C.V.';
        $company->acronym = 'VAMSA_NIÑOS';
        $company->activity = 'Car dealership.: Offers a wide range of vehicles for sale and related services.';
        $company->industry_id = '4';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Vertice Servicios Integrados, S.A. de C.V.';
        $company->acronym = 'VERTICE';
        $company->activity = 'Commerce.: Engaged in a variety of commercial activities.';
        $company->industry_id = '9';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Vitro, S.A.B. de C.V.';
        $company->acronym = 'VITRO';
        $company->activity = 'Multinational company specialized in glass based in Mexico, which has positioned itself as the largest glass manufacturer in Mexico.: Leading glass manufacturer in Mexico with a strong multinational presence.';
        $company->industry_id = '16';
        $company->country_id = '144';
        $company->save();

        $company = new Company();
        $company->name = 'Promotora Saludanat S.A. De C.V.';
        $company->acronym = 'SALUDANAT';
        $company->activity = 'Mexican company associated with “Súper Naturista,” focused on the commercialization of natural products and healthy lifestyle solutions. It operates mainly in Mexico City (CDMX) and the State of Mexico, offering sales positions, customer service roles, and supervisory opportunities, with statutory benefits and direct hiring.';
        $company->industry_id = '26';
        $company->country_id = '144';
        $company->save();
    }
}
