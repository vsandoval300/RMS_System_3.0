<?php

namespace Database\Seeders;

use App\Models\director;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DirectorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $director = new director();
        $director->name = 'Bernell Llewellyn';
        $director->surname = 'Arrindell';
        $director->gender= 'Male';
        $director->email = 'arrindell.ben8@gmail.com';
        $director->phone ='';
        $director->address ='Windrush, 3Westmoreland, St. James, Barbados';
        $director->occupation ='Business Executive';
        $director->image = '';
        $director->country_id = '20';
        $director->save();

        $director = new director();
        $director->name = 'Carolyn Frances';
        $director->surname = 'Humphrey';
        $director->gender= 'Female';
        $director->email = 'ch@integritymanagers.com';
        $director->phone ='+1 (246) 234-2001';
        $director->address ='6C Prior Park Close, St James, Barbados';
        $director->occupation ='Business Executive';
        $director->image = '';
        $director->country_id = '20';
        $director->save();

        $director = new director();
        $director->name = 'Gonzalo ';
        $director->surname = 'García Torres Septien';
        $director->gender= 'Male';
        $director->email = 'gg@rainmakergroup.com';
        $director->phone ='';
        $director->address ='Miguel De Mendoza 43 Deparamento 301. Colonia Merced Gomez, Alvaro Obregon, CP 01600 Mexico';
        $director->occupation ='Business Executive';
        $director->image = '';
        $director->country_id = '144';
        $director->save();

        $director = new director();
        $director->name = 'Alastair Bruce ';
        $director->surname = 'Dent';
        $director->gender= 'Male';
        $director->email = 'alastair.dent@orionbarbados.com';
        $director->phone ='+1 (246) 537 8296';
        $director->address ='24 Bella Vista, Mount Wilton, Saint Thomas Barbados';
        $director->occupation ='Business Executive';
        $director->image = '';
        $director->country_id = '20';
        $director->save();

        $director = new director();
        $director->name = 'Andres ';
        $director->surname = 'Pons González';
        $director->gender= 'Male';
        $director->email = 'andresponsg@yahoo.com.mx';
        $director->phone ='';
        $director->address ='Prol. Paseo de Tolsa 37 Santa Fe, Cuajimalpa C.P. 05348, México';
        $director->occupation ='Business Executive';
        $director->image = '';
        $director->country_id = '144';
        $director->save();

        $director = new director();
        $director->name = 'Archie ';
        $director->surname = 'Cuke';
        $director->gender= 'Male';
        $director->email = '';
        $director->phone ='+1 (866) 873 9616';
        $director->address ='Apartment 2, Miramar, Navy Gardens, Christ Church, Barbados, West Indies';
        $director->occupation ='Business Executive';
        $director->image = '';
        $director->country_id = '20';
        $director->save();

        $director = new director();
        $director->name = 'Ashok ';
        $director->surname = 'Merai';
        $director->gender= 'Male';
        $director->email = 'ashokjmerai@gmail.com';
        $director->phone ='';
        $director->address ='134 Surf View Row, Atlantic Shores, Christ Church Barbados';
        $director->occupation ='Insurance Executive';
        $director->image = '';
        $director->country_id = '41';
        $director->save();

        $director = new director();
        $director->name = 'Christopher';
        $director->surname = 'Glyn Evans';
        $director->gender= 'Male';
        $director->email = 'cgevans56@hotmail.com';
        $director->phone ='';
        $director->address ='2 Harbour View Road, Lodge Hill. St. Michael BB12001 Barbados';
        $director->occupation ='Business Executive';
        $director->image = '';
        $director->country_id = '20';
        $director->save();

        $director = new director();
        $director->name = 'David Andrew ';
        $director->surname = 'Clarke Alleyne';
        $director->gender= 'Male';
        $director->email = '';
        $director->phone ='';
        $director->address ='11 Southern Avenue, Fort George Heights, Christ Church Barbados';
        $director->occupation ='Insurance Executive';
        $director->image = '';
        $director->country_id = '20';
        $director->save();

        $director = new director();
        $director->name = 'Mark Edward';
        $director->surname = 'Young';
        $director->gender= 'Male';
        $director->email = 'youngmark1968@gmail.com';
        $director->phone ='';
        $director->address ='Woodland, St George, Barbados, BB19130';
        $director->occupation ='Business Executive';
        $director->image = '';
        $director->country_id = '235';
        $director->save();

        $director = new director();
        $director->name = 'Monica ';
        $director->surname = 'Kibrit Snaiderman';
        $director->gender= 'Female';
        $director->email = 'monica@mkibrit.com';
        $director->phone ='';
        $director->address ='Hacienda del Ciervo 7A-2002, Edo Mex, 52763, Mexico';
        $director->occupation ='Business Executive';
        $director->image = '';
        $director->country_id = '20';
        $director->save();

        $director = new director();
        $director->name = 'Marjorie Patricia ';
        $director->surname = 'Downes-Grant';
        $director->gender= 'Female';
        $director->email = '';
        $director->phone ='';
        $director->address ='7 Edghill Heights, St. Thomas, barbados';
        $director->occupation ='Business Executive';
        $director->image = '';
        $director->country_id = '20';
        $director->save();

        $director = new director();
        $director->name = 'Peter Alan ';
        $director->surname = 'Walford';
        $director->gender= 'Male';
        $director->email = '';
        $director->phone ='';
        $director->address ='Bannatyne Cottage, Bannatyne, Christ Church Barbados';
        $director->occupation ='Business Executive';
        $director->image = '';
        $director->country_id = '20';
        $director->save();

        $director = new director();
        $director->name = 'Randy H. ';
        $director->surname = 'Graham';
        $director->gender= 'Male';
        $director->email = '';
        $director->phone ='';
        $director->address ='13, Mt. Wilton, Mount Wilton, St. Thomas, Barbados';
        $director->occupation ='Business Executive';
        $director->image = '';
        $director->country_id = '236';
        $director->save();

        $director = new director();
        $director->name = 'Vinston Elisha';
        $director->surname = 'Hampden';
        $director->gender= 'Male';
        $director->email = 'vin_hampden@hotmail.com';
        $director->phone ='';
        $director->address ='197, 2nd Ave. Homestead Drive Frere Pilgrim Christ Church BB17006 Barbados';
        $director->occupation ='Professional accountant';
        $director->image = '';
        $director->country_id = '20';
        $director->save();

        $director = new director();
        $director->name = 'Wayne Harold ';
        $director->surname = 'Kirton';
        $director->gender= 'Male';
        $director->email = '';
        $director->phone ='';
        $director->address ='Lot 8 Locust Hall, St. George BB19920, Barbados';
        $director->occupation ='Business Executive';
        $director->image = '';
        $director->country_id = '20';
        $director->save();

        $director = new director();
        $director->name = 'Wismar Anderson ';
        $director->surname = 'Greaves';
        $director->gender= 'Male';
        $director->email = '';
        $director->phone ='';
        $director->address ='#3 Colleton, St. Lucy BB27188, Barbados';
        $director->occupation ='Business Executive';
        $director->image = '';
        $director->country_id = '20';
        $director->save();

        $director = new director();
        $director->name = 'Mauricio Salvador ';
        $director->surname = 'Esquino Urdaneta';
        $director->gender= 'Male';
        $director->email = 'me@rainmakergroup.com';
        $director->phone ='';
        $director->address ='Ave Corrigidores 925, Dep 302, Piso 3, Lomas Virreyes, Mexico, DF, C.P. 11000-CR-11002';
        $director->occupation ='Business Executive';
        $director->image = '';
        $director->country_id = '236';
        $director->save();

        $director = new director();
        $director->name = 'Trevor Austin';
        $director->surname = 'Carmichael';
        $director->gender= 'Male';
        $director->email = 'tac@chancerychambers.com';
        $director->phone ='';
        $director->address ='Staple Grove House, St. Davids, Christ Church, Barbados';
        $director->occupation ='Attorney-at-Law';
        $director->image = '';
        $director->country_id = '20';
        $director->save();

        $director = new director();
        $director->name = 'Kira Almendra ';
        $director->surname = 'González Tello';
        $director->gender= 'Female';
        $director->email = 'ago@integritymanagers.com';
        $director->phone ='';
        $director->address ='168 Cassia Row, Sunset Crest, Saint James Barbados';
        $director->occupation ='Insurance Executive';
        $director->image = '';
        $director->country_id = '144';
        $director->save();

        $director = new director();
        $director->name = 'Henry Archibald ';
        $director->surname = 'Cuke';
        $director->gender= 'Male';
        $director->email = '';
        $director->phone ='';
        $director->address ='Apartment 2, Miramar, Navy Gardens, Christ Church Barbados';
        $director->occupation ='Insurance Executive';
        $director->image = '';
        $director->country_id = '20';
        $director->save();

        $director = new director();
        $director->name = 'Grafton N. ';
        $director->surname = 'Williams';
        $director->gender= 'Male';
        $director->email = 'Grafton.Williams@massygroup.com';
        $director->phone ='+1 (246) 467-1481¬†';
        $director->address ='9 Walkers Park West, St. George, Barbados';
        $director->occupation ='Business Executive';
        $director->image = '';
        $director->country_id = '20';
        $director->save();

        $director = new director();
        $director->name = 'Alfredo ';
        $director->surname = 'Sánchez Torrado';
        $director->gender= 'Male';
        $director->email = 'sanchezt@chevez.com.mx';
        $director->phone ='';
        $director->address ='Vasco de Quiroga No. 2121 Piso 4 Col. Pena Blanca Santa Fe, 01210 Ciudad de Mexico';
        $director->occupation ='Business Executive';
        $director->image = '';
        $director->country_id = '144';
        $director->save();

        $director = new director();
        $director->name = 'Jeremy Andrew Marryshow';
        $director->surname = 'Marryshow';
        $director->gender= 'Male';
        $director->email = 'jamarry@hotmail.com';
        $director->phone ='';
        $director->address ='16 Turtleback Ridge, Sion Hill, St. James BB24025, Barbados';
        $director->occupation ='Business Executive';
        $director->image = '';
        $director->country_id = '20';
        $director->save();

        $director = new director();
        $director->name = 'Enrique ';
        $director->surname = 'Goicolea García';
        $director->gender= 'Male';
        $director->email = '';
        $director->phone ='';
        $director->address ='5 Avenida 16-72, Zona 14, Guatemala City, Guatemala.';
        $director->occupation ='Business Executive';
        $director->image = '';
        $director->country_id = '20';
        $director->save();

        $director = new director();
        $director->name = 'Diego ';
        $director->surname = 'Herrera Eonnet';
        $director->gender= 'Male';
        $director->email = 'dherrera@pantaleon.com';
        $director->phone ='';
        $director->address ='10 Avenida 5-05, Zona 14, ResidencialesCapuchinas 23-1, Guatemala City, Guatemala';
        $director->occupation ='Business Executive';
        $director->image = '';
        $director->country_id = '144';
        $director->save();

        $director = new director();
        $director->name = 'Gonzalo ';
        $director->surname = 'Enrique Jalles';
        $director->gender= 'Male';
        $director->email = '';
        $director->phone ='';
        $director->address ='12 Crystal Drive, Crystal Harbour, West Bay, Grand Cayman';
        $director->occupation ='Business Executive';
        $director->image = '';
        $director->country_id = '20';
        $director->save();

        $director = new director();
        $director->name = 'Stephen ';
        $director->surname = 'Jackson';
        $director->gender= 'Male';
        $director->email = '';
        $director->phone ='';
        $director->address ='96 N Shore Dr, Miami Beach FL. 33141, United States of America';
        $director->occupation ='Business Executive';
        $director->image = '';
        $director->country_id = '235';
        $director->save();

        $director = new director();
        $director->name = 'Cristóbal ';
        $director->surname = 'Fernández Abascal';
        $director->gender= 'Male';
        $director->email = '';
        $director->phone ='';
        $director->address ='Avenida 14-80 Zona 10, casa 19, Guatemala City, Guatemala';
        $director->occupation ='Business Executive';
        $director->image = '';
        $director->country_id = '144';
        $director->save();

        $director = new director();
        $director->name = 'Diego Esteban ';
        $director->surname = 'Bolanos Colona';
        $director->gender= 'Male';
        $director->email = '';
        $director->phone ='';
        $director->address ='Caribbean Plaza, 2nd Floor, North Building 878 West Bay Road, P.O. Box 1159, KY1-1102 Grand Cayman Cayman Islands';
        $director->occupation ='Business Executive';
        $director->image = '';
        $director->country_id = '144';
        $director->save();

        $director = new director();
        $director->name = 'Gabriel ';
        $director->surname = 'Holschneider Osuna';
        $director->gender= 'Male';
        $director->email = 'gho@rainmakergroup.com';
        $director->phone ='';
        $director->address ='115 Makvas Ct. Coral Gables FL, USA';
        $director->occupation ='Business Executive';
        $director->image = '';
        $director->country_id = '236';
        $director->save();

        $director = new director();
        $director->name = 'José';
        $director->surname = 'García Aguilera';
        $director->gender= 'Male';
        $director->email = '';
        $director->phone ='';
        $director->address ='Homero 1824, Los Morales Polanco, Ciudad de México, C.P. 11510 CDMX.';
        $director->occupation ='Business Executive';
        $director->image = '';
        $director->country_id = '144';
        $director->save();

        $director = new director();
        $director->name = 'Dave Carleton';
        $director->surname = 'Tatlock';
        $director->gender= 'Male';
        $director->email = 'Dave.tatlock@strategicrisks.com';
        $director->phone ='';
        $director->address ='159 Bank Street, Fourth Floor, Burlington, VT 05401';
        $director->occupation ='Business Executive';
        $director->image = '';
        $director->country_id = '236';
        $director->save();

        $director = new director();
        $director->name = 'Robert Malcolm ';
        $director->surname = 'Koffler';
        $director->gender= 'Male';
        $director->email = '';
        $director->phone ='';
        $director->address ='400 Harbour Dr, Key Biscayne, FL, USA';
        $director->occupation ='Business Executive';
        $director->image = '';
        $director->country_id = '175';
        $director->save();

        $director = new director();
        $director->name = 'Benjamín';
        $director->surname = 'López Morales';
        $director->gender= 'Male';
        $director->email = 'blopez@eurekare.com';
        $director->phone ='';
        $director->address ='168 Cassia Row, Sunset Crest, Saint James Barbados';
        $director->occupation ='Business Executive';
        $director->image = '';
        $director->country_id = '144';
        $director->save();
    }
}
