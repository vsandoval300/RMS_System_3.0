<?php

namespace Database\Seeders;

use App\Models\industry;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IndustriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $industry = new industry();
        $industry->name ='Aerospace';
        $industry->description = 'The aerospace industry encompasses the manufacture of a wide range of aircraft and spacecraft products (including passenger and military aeroplanes, helicopters, and gliders, as well as spacecraft, launch vehicles, satellites, and other space-related items).';
        $industry->save();

        $industry = new industry();
        $industry->name ='Agriculture & Forestry';
        $industry->description = 'The Agriculture, Forestry, Fishing and Hunting sector comprises establishments primarily engaged in growing crops, raising animals, harvesting timber, and harvesting fish and other animals from a farm, ranch, or their natural habitats.';
        $industry->save();

        $industry = new industry();
        $industry->name ='Apparel';
        $industry->description = 'The apparel industry cuts fabrics and other materials and sews them together to create apparel or accessories, including footwear, outerwear, pants, and tops.';
        $industry->save();

        $industry = new industry();
        $industry->name ='Automotive';
        $industry->description = 'Automotive industry, all those companies and activities involved in the manufacture of motor vehicles, including most components, such as engines and bodies, but excluding tires, batteries, and fuel. ';
        $industry->save();

        $industry = new industry();
        $industry->name ='Banking & Payments';
        $industry->description = 'The banking industry includes systems of financial institutions called banks that help people store and use their money. ';
        $industry->save();

        $industry = new industry();
        $industry->name ='Business and Costumer Services';
        $industry->description = 'Business and Consumer Services means the provision of services to others on a fee or contract basis, such as advertising and mailing; building maintenance; employment service; management and consulting services; protective services; equipment rental and leasing; commercial research; development and testing; photo finishing; and personal supply services.';
        $industry->save();

        $industry = new industry();
        $industry->name ='Chemicals';
        $industry->description = 'Chemical industry is a vast industry that incorporates all different types of product producing industries whose generation is based on heavy use of chemicals. Usually, industries that are involved with industrial chemical generation are broadly known as chemical industry.';
        $industry->save();

        $industry = new industry();
        $industry->name ='Construction';
        $industry->description = 'This industry involves building, repairing and maintaining infrastructure and real estate projects. This includes residential, commercial or industrial construction along with civil engineering projects such as bridges, roads and dams.';
        $industry->save();

        $industry = new industry();
        $industry->name ='Consumer';
        $industry->description = 'The consumer goods sector is a category of stocks and companies that relate to items purchased by individuals and households rather than by manufacturers and industries. These companies make and sell products that are intended for direct use by the buyers for their own use and enjoyment.';
        $industry->save();

        $industry = new industry();
        $industry->name ='Defence & Security';
        $industry->description = 'The Defense Industrial Base Sector is the worldwide industrial complex that enables research and development, as well as design, production, delivery, and maintenance of military weapons systems, subsystems, and components or parts, to meet military requirements. ';
        $industry->save();

        $industry = new industry();
        $industry->name ='Drinks and Beverages';
        $industry->description = 'The drinks and beverages industry consists of manufacturers who produce drinks for distribution to various retail channels; including major multiples, discounters, convenience, cash & carry’s and independents.';
        $industry->save();

        $industry = new industry();
        $industry->name ='Financial Services';
        $industry->description = 'The financial services sector provides financial services to people and corporations. This segment of the economy is made up of a variety of financial firms including banks, investment houses, lenders, finance companies, real estate brokers, and insurance companies.';
        $industry->save();

        $industry = new industry();
        $industry->name ='Foodservice';
        $industry->description = 'Foodservice outlets are facilities that serve meals and snacks for immediate consumption on site (food away from home). Commercial foodservice establishments accounted for the bulk of food-away-from-home expenditures. This category includes full-service restaurants, limited-service outlets, caterers, some cafeterias—and other places that prepare, serve, and sell food to the general public for a profit.';
        $industry->save();

        $industry = new industry();
        $industry->name ='Government and Non-Profit Organisations';
        $industry->description = 'A non-profit government organization is an entity established in the public interest and governed by a board of trustees, officers or directors for charitable, educational, religious or other public service activities. It does not operate for profit and its net earnings are devoted to charitable activities typically benefiting the wider population.';
        $industry->save();

        $industry = new industry();
        $industry->name ='Healthcare';
        $industry->description = 'The healthcare sector consists of businesses that provide medical services, manufacture medical equipment or drugs, provide medical insurance, or otherwise facilitate the provision of healthcare to patients.';
        $industry->save();

        $industry = new industry();
        $industry->name ='Industrial Goods & Machinery';
        $industry->description = 'The industrial machinery manufacturing industry comprises the production of all mechanical machinery for use in the mining, manufacturing, energy, and construction sectors, as well as domestic appliances (e.g., air conditioning).';
        $industry->save();

        $industry = new industry();
        $industry->name ='Insurance';
        $industry->description = 'The insurance sector is made up of companies that offer risk management in the form of insurance contracts. The basic concept of insurance is that one party, the insurer, will guarantee payment for an uncertain future event.';
        $industry->save();

        $industry = new industry();
        $industry->name ='Media and Entertainment';
        $industry->description = 'The Media and Entertainment (M&E) industry has multiple segments that combine into one vertical; Movies/Cinema, Television, Music, Publishing, Radio, Internet, Advertising and Gaming. Moreover, trends and drivers for each of the segments vary across sub-segments, geographies and consumer segments.';
        $industry->save();

        $industry = new industry();
        $industry->name ='Medical';
        $industry->description = 'This industry involves all companies that manufactures medicines and medical equipment for public health treatment and prevention institutions and for the needs of the public.';
        $industry->save();

        $industry = new industry();
        $industry->name ='Mining';
        $industry->description = 'The mining industry is involved in the extraction of precious minerals and other geological materials. The extracted materials are transformed into a mineralized form that serves an economic benefit to the prospector or miner. Typical activities in the mining industry include metals production, metals investing, and metals trading.';
        $industry->save();

        $industry = new industry();
        $industry->name ='Oil & Gas';
        $industry->description = 'Oil and Gas Sector means the sector of industry focused on exploration, data acquisition, development, drilling, production, gathering, refining, distribution and transportation of hydrocarbons and includes but is not limited to major resource holders, national oil companies, multinational oil companies, drilling contractors, services contractors, and other related businesses.';
        $industry->save();

        $industry = new industry();
        $industry->name ='Packaging';
        $industry->description = 'Packaging Industry means without in any limiting the ordinary meaning of the expression, the industry in which employers and their employees are associated for the purpose of packaging goods and products for clients and charge fees for such a service. The goods that are packaged come as complete products in huge bags or containers and just need to be packed in smaller containers which can be tins, paper bags, plastic containers etc.';
        $industry->save();

        $industry = new industry();
        $industry->name ='Pharma';
        $industry->description = 'The pharmaceutical industry is an industry in medicine that discovers, develops, produces, and markets pharmaceutical drugs for use as medications to be administered to patients (or self-administered), with the aim to cure and prevent diseases, or alleviate symptoms.';
        $industry->save();

        $industry = new industry();
        $industry->name ='Power & Utilities';
        $industry->description = 'The utilities sector is a category of stocks consisting of private companies that provide basic amenities, including natural gas, electricity, and water. ';
        $industry->save();

        $industry = new industry();
        $industry->name ='Real State';
        $industry->description = 'The real estate industry refers to the businesses and individuals involved in the buying, selling, and management of real estate properties. Real estate properties can include residential properties, such as houses and apartments, as well as commercial properties, such as office buildings and retail spaces.';
        $industry->save();

        $industry = new industry();
        $industry->name ='Retail';
        $industry->description = 'The retail industry consists of all companies that sell goods and services to consumers. There are many different retail sales and store types worldwide, including grocery, convenience, discounts, independents, department stores, DIY, electrical and speciality. ';
        $industry->save();

        $industry = new industry();
        $industry->name ='Sports';
        $industry->description = 'Sport industry is a market in which people, activities, business, and organizations involved in producing, facilitating, promoting, or organizing any activity, experience, or business enterprise focused on sports.';
        $industry->save();

        $industry = new industry();
        $industry->name ='Technology, Media and Telecom';
        $industry->description = 'The technology, media, and telecom (TMT) sector is an industry grouping that includes the majority of companies focused on new technologies.';
        $industry->save();

        $industry = new industry();
        $industry->name ='Transportation, Infrastructure and Logistics';
        $industry->description = 'The transportation sector is a category of companies that provide services to move people or goods, as well as transportation infrastructure. Technically, transportation is a sub-group of the industrials sector according to the Global Industry Classification Standard (GICS). The transportation sector consists of several industries including air freight and logistics, airlines, marine, road and rail, and transportation infrastructure.';
        $industry->save();

        $industry = new industry();
        $industry->name ='Travel and Tourism';
        $industry->description = 'The travel and tourism sector comprises a wide range of products and services, including leisure and business travel, accommodation, food and drink services, and more.';
        $industry->save();

        $industry = new industry();
        $industry->name ='Particular';
        $industry->description = 'Business related to a single UBO';
        $industry->save();
    }
}
