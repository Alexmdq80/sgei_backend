<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NacionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('nacions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $nacionesRaw = [
            // AMÉRICA DEL SUR (continente_id: 5)
            ['id_georef' => 340, 'continente_id' => 5, 'nombre' => 'ARGENTINA', 'nacionalidad' => 'ARGENTINA/O'],
            ['id_georef' => 341, 'continente_id' => 5, 'nombre' => 'BOLIVIA', 'nacionalidad' => 'BOLIVIANA/O'],
            ['id_georef' => 342, 'continente_id' => 5, 'nombre' => 'BRASIL', 'nacionalidad' => 'BRASILEÑA/O'],
            ['id_georef' => 343, 'continente_id' => 5, 'nombre' => 'COLOMBIA', 'nacionalidad' => 'COLOMBIANA/O'],
            ['id_georef' => 344, 'continente_id' => 5, 'nombre' => 'CHILE', 'nacionalidad' => 'CHILENA/O'],
            ['id_georef' => 345, 'continente_id' => 5, 'nombre' => 'ECUADOR', 'nacionalidad' => 'ECUATORIANA/O'],
            ['id_georef' => 346, 'continente_id' => 5, 'nombre' => 'GUYANA', 'nacionalidad' => 'GUYANESA/O'],
            ['id_georef' => 347, 'continente_id' => 5, 'nombre' => 'PARAGUAY', 'nacionalidad' => 'PARAGUAYA/O'],
            ['id_georef' => 348, 'continente_id' => 5, 'nombre' => 'PERÚ', 'nacionalidad' => 'PERUANA/O'],
            ['id_georef' => 349, 'continente_id' => 5, 'nombre' => 'SURINAM', 'nacionalidad' => 'SURINAMESA/O'],
            ['id_georef' => 350, 'continente_id' => 5, 'nombre' => 'URUGUAY', 'nacionalidad' => 'URUGUAYA/O'],
            ['id_georef' => 351, 'continente_id' => 5, 'nombre' => 'VENEZUELA', 'nacionalidad' => 'VENEZOLANA/O'],

            // CENTROAMÉRICA Y CARIBE (continente_id: 4)
            ['id_georef' => 331, 'continente_id' => 4, 'nombre' => 'ANTIGUA Y BARBUDA', 'nacionalidad' => 'ANTIGUANA/O'],
            ['id_georef' => 332, 'continente_id' => 4, 'nombre' => 'BAHAMAS', 'nacionalidad' => 'BAHAMEÑA/O'],
            ['id_georef' => 333, 'continente_id' => 4, 'nombre' => 'BARBADOS', 'nacionalidad' => 'BARBADENSE'],
            ['id_georef' => 334, 'continente_id' => 4, 'nombre' => 'BELICE', 'nacionalidad' => 'BELICEÑA/O'],
            ['id_georef' => 320, 'continente_id' => 4, 'nombre' => 'COSTA RICA', 'nacionalidad' => 'COSTARRICENSE'],
            ['id_georef' => 321, 'continente_id' => 4, 'nombre' => 'CUBA', 'nacionalidad' => 'CUBANA/O'],
            ['id_georef' => 322, 'continente_id' => 4, 'nombre' => 'DOMINICA', 'nacionalidad' => 'DOMINIQUÉS/A'],
            ['id_georef' => 323, 'continente_id' => 4, 'nombre' => 'EL SALVADOR', 'nacionalidad' => 'SALVADOREÑA/O'],
            ['id_georef' => 335, 'continente_id' => 4, 'nombre' => 'GRANADA', 'nacionalidad' => 'GRANADINA/O'],
            ['id_georef' => 324, 'continente_id' => 4, 'nombre' => 'GUATEMALA', 'nacionalidad' => 'GUATEMALTECA/O'],
            ['id_georef' => 325, 'continente_id' => 4, 'nombre' => 'HAITÍ', 'nacionalidad' => 'HAITIANA/O'],
            ['id_georef' => 326, 'continente_id' => 4, 'nombre' => 'HONDURAS', 'nacionalidad' => 'HONDUREÑA/O'],
            ['id_georef' => 327, 'continente_id' => 4, 'nombre' => 'JAMAICA', 'nacionalidad' => 'JAMAIQUINA/O'],
            ['id_georef' => 328, 'continente_id' => 4, 'nombre' => 'NICARAGUA', 'nacionalidad' => 'NICARAGÜENSE'],
            ['id_georef' => 329, 'continente_id' => 4, 'nombre' => 'PANAMÁ', 'nacionalidad' => 'PANAMEÑA/O'],
            ['id_georef' => 330, 'continente_id' => 4, 'nombre' => 'REPÚBLICA DOMINICANA', 'nacionalidad' => 'DOMINICANA/O'],
            ['id_georef' => 336, 'continente_id' => 4, 'nombre' => 'SAN CRISTÓBAL Y NIEVES', 'nacionalidad' => 'SANCRISTOBALEÑA/O'],
            ['id_georef' => 337, 'continente_id' => 4, 'nombre' => 'SAN VICENTE Y LAS GRANADINAS', 'nacionalidad' => 'SANVICENTINA/O'],
            ['id_georef' => 338, 'continente_id' => 4, 'nombre' => 'SANTA LUCÍA', 'nacionalidad' => 'SANTALUCIENSE'],
            ['id_georef' => 339, 'continente_id' => 4, 'nombre' => 'TRINIDAD Y TOBAGO', 'nacionalidad' => 'TRINITENSE'],

            // AMÉRICA DEL NORTE (continente_id: 3)
            ['id_georef' => 301, 'continente_id' => 3, 'nombre' => 'CANADÁ', 'nacionalidad' => 'CANADIENSE'],
            ['id_georef' => 302, 'continente_id' => 3, 'nombre' => 'ESTADOS UNIDOS', 'nacionalidad' => 'ESTADOUNIDENSE'],
            ['id_georef' => 303, 'continente_id' => 3, 'nombre' => 'MÉXICO', 'nacionalidad' => 'MEXICANA/O'],

            // EUROPA (continente_id: 1)
            ['id_georef' => 101, 'continente_id' => 1, 'nombre' => 'ALBANIA', 'nacionalidad' => 'ALBANÉS/A'],
            ['id_georef' => 126, 'continente_id' => 1, 'nombre' => 'ALEMANIA', 'nacionalidad' => 'ALEMANA/O'],
            ['id_georef' => 134, 'continente_id' => 1, 'nombre' => 'ANDORRA', 'nacionalidad' => 'ANDORRANA/O'],
            ['id_georef' => 102, 'continente_id' => 1, 'nombre' => 'AUSTRIA', 'nacionalidad' => 'AUSTRÍACA/O'],
            ['id_georef' => 103, 'continente_id' => 1, 'nombre' => 'BÉLGICA', 'nacionalidad' => 'BELGA'],
            ['id_georef' => 140, 'continente_id' => 1, 'nombre' => 'BIELORRUSIA', 'nacionalidad' => 'BIELORRUSA/O'],
            ['id_georef' => 135, 'continente_id' => 1, 'nombre' => 'BOSNIA Y HERZEGOVINA', 'nacionalidad' => 'BOSNIA/O'],
            ['id_georef' => 104, 'continente_id' => 1, 'nombre' => 'BULGARIA', 'nacionalidad' => 'BÚLGARA/O'],
            ['id_georef' => 106, 'continente_id' => 1, 'nombre' => 'CHIPRE', 'nacionalidad' => 'CHIPRIOTA'],
            ['id_georef' => 137, 'continente_id' => 1, 'nombre' => 'CIUDAD DEL VATICANO', 'nacionalidad' => 'VATICANA/O'],
            ['id_georef' => 143, 'continente_id' => 1, 'nombre' => 'CROACIA', 'nacionalidad' => 'CROATA'],
            ['id_georef' => 107, 'continente_id' => 1, 'nombre' => 'DINAMARCA', 'nacionalidad' => 'DANÉS/A'],
            ['id_georef' => 144, 'continente_id' => 1, 'nombre' => 'ESLOVAQUIA', 'nacionalidad' => 'ESLOVACA/O'],
            ['id_georef' => 145, 'continente_id' => 1, 'nombre' => 'ESLOVENIA', 'nacionalidad' => 'ESLOVENA/O'],
            ['id_georef' => 108, 'continente_id' => 1, 'nombre' => 'ESPAÑA', 'nacionalidad' => 'ESPAÑOLA/O'],
            ['id_georef' => 141, 'continente_id' => 1, 'nombre' => 'ESTONIA', 'nacionalidad' => 'ESTONIA/O'],
            ['id_georef' => 109, 'continente_id' => 1, 'nombre' => 'FINLANDIA', 'nacionalidad' => 'FINLANDÉS/A'],
            ['id_georef' => 110, 'continente_id' => 1, 'nombre' => 'FRANCIA', 'nacionalidad' => 'FRANCESA/O'],
            ['id_georef' => 111, 'continente_id' => 1, 'nombre' => 'GRECIA', 'nacionalidad' => 'GRIEGA/O'],
            ['id_georef' => 112, 'continente_id' => 1, 'nombre' => 'HUNGRÍA', 'nacionalidad' => 'HÚNGARA/O'],
            ['id_georef' => 113, 'continente_id' => 1, 'nombre' => 'IRLANDA', 'nacionalidad' => 'IRLANDÉS/A'],
            ['id_georef' => 114, 'continente_id' => 1, 'nombre' => 'ISLANDIA', 'nacionalidad' => 'ISLANDÉS/A'],
            ['id_georef' => 115, 'continente_id' => 1, 'nombre' => 'ITALIA', 'nacionalidad' => 'ITALIANA/O'],
            ['id_georef' => 136, 'continente_id' => 1, 'nombre' => 'LETONIA', 'nacionalidad' => 'LETONA/O'],
            ['id_georef' => 146, 'continente_id' => 1, 'nombre' => 'LIECHTENSTEIN', 'nacionalidad' => 'LIECHTENSTEINIANA/O'],
            ['id_georef' => 142, 'continente_id' => 1, 'nombre' => 'LITUANIA', 'nacionalidad' => 'LITUANA/O'],
            ['id_georef' => 117, 'continente_id' => 1, 'nombre' => 'LUXEMBURGO', 'nacionalidad' => 'LUXEMBURGUÉS/A'],
            ['id_georef' => 147, 'continente_id' => 1, 'nombre' => 'MACEDONIA DEL NORTE', 'nacionalidad' => 'MACEDONIA/O'],
            ['id_georef' => 118, 'continente_id' => 1, 'nombre' => 'MALTA', 'nacionalidad' => 'MALTÉS/A'],
            ['id_georef' => 148, 'continente_id' => 1, 'nombre' => 'MOLDAVIA', 'nacionalidad' => 'MOLDAVA/O'],
            ['id_georef' => 149, 'continente_id' => 1, 'nombre' => 'MÓNACO', 'nacionalidad' => 'MONEGASCA/O'],
            ['id_georef' => 150, 'continente_id' => 1, 'nombre' => 'MONTENEGRO', 'nacionalidad' => 'MONTENEGRINA/O'],
            ['id_georef' => 119, 'continente_id' => 1, 'nombre' => 'NORUEGA', 'nacionalidad' => 'NORUEGA/O'],
            ['id_georef' => 121, 'continente_id' => 1, 'nombre' => 'PAÍSES BAJOS', 'nacionalidad' => 'NEERLANDÉS/A'],
            ['id_georef' => 122, 'continente_id' => 1, 'nombre' => 'POLONIA', 'nacionalidad' => 'POLACA/O'],
            ['id_georef' => 123, 'continente_id' => 1, 'nombre' => 'PORTUGAL', 'nacionalidad' => 'PORTUGUÉS/A'],
            ['id_georef' => 125, 'continente_id' => 1, 'nombre' => 'REINO UNIDO', 'nacionalidad' => 'BRITÁNICA/O'],
            ['id_georef' => 127, 'continente_id' => 1, 'nombre' => 'REPÚBLICA CHECA', 'nacionalidad' => 'CHECA/O'],
            ['id_georef' => 128, 'continente_id' => 1, 'nombre' => 'RUMANIA', 'nacionalidad' => 'RUMANA/O'],
            ['id_georef' => 129, 'continente_id' => 1, 'nombre' => 'RUSIA', 'nacionalidad' => 'RUSA/O'],
            ['id_georef' => 130, 'continente_id' => 1, 'nombre' => 'SAN MARINO', 'nacionalidad' => 'SANMARINENSE'],
            ['id_georef' => 151, 'continente_id' => 1, 'nombre' => 'SERBIA', 'nacionalidad' => 'SERBIA/O'],
            ['id_georef' => 131, 'continente_id' => 1, 'nombre' => 'SUECIA', 'nacionalidad' => 'SUECA/O'],
            ['id_georef' => 132, 'continente_id' => 1, 'nombre' => 'SUIZA', 'nacionalidad' => 'SUIZA/O'],
            ['id_georef' => 133, 'continente_id' => 1, 'nombre' => 'UCRANIA', 'nacionalidad' => 'UCRANIANA/O'],

            // ÁFRICA (continente_id: 2)
            ['id_georef' => 206, 'continente_id' => 2, 'nombre' => 'ANGOLA', 'nacionalidad' => 'ANGOLEÑA/O'],
            ['id_georef' => 201, 'continente_id' => 2, 'nombre' => 'ARGELIA', 'nacionalidad' => 'ARGELINA/O'],
            ['id_georef' => 207, 'continente_id' => 2, 'nombre' => 'BENÍN', 'nacionalidad' => 'BENINÉS/A'],
            ['id_georef' => 208, 'continente_id' => 2, 'nombre' => 'BOTSUANA', 'nacionalidad' => 'BOTSUANA/O'],
            ['id_georef' => 209, 'continente_id' => 2, 'nombre' => 'BURKINA FASO', 'nacionalidad' => 'BURKINABÉ'],
            ['id_georef' => 210, 'continente_id' => 2, 'nombre' => 'BURUNDI', 'nacionalidad' => 'BURUNDÉS/A'],
            ['id_georef' => 211, 'continente_id' => 2, 'nombre' => 'CABO VERDE', 'nacionalidad' => 'CABOVERDIANA/O'],
            ['id_georef' => 212, 'continente_id' => 2, 'nombre' => 'CAMERÚN', 'nacionalidad' => 'CAMERUNÉS/A'],
            ['id_georef' => 213, 'continente_id' => 2, 'nombre' => 'CHAD', 'nacionalidad' => 'CHADIANA/O'],
            ['id_georef' => 214, 'continente_id' => 2, 'nombre' => 'COMORAS', 'nacionalidad' => 'COMORENSE'],
            ['id_georef' => 215, 'continente_id' => 2, 'nombre' => 'CONGO', 'nacionalidad' => 'CONGOLEÑA/O'],
            ['id_georef' => 216, 'continente_id' => 2, 'nombre' => 'COSTA DE MARFIL', 'nacionalidad' => 'MARFILEÑA/O'],
            ['id_georef' => 202, 'continente_id' => 2, 'nombre' => 'EGIPTO', 'nacionalidad' => 'EGIPCIA/O'],
            ['id_georef' => 217, 'continente_id' => 2, 'nombre' => 'ERITREA', 'nacionalidad' => 'ERITREA/O'],
            ['id_georef' => 218, 'continente_id' => 2, 'nombre' => 'ETIOPÍA', 'nacionalidad' => 'ETÍOPE'],
            ['id_georef' => 219, 'continente_id' => 2, 'nombre' => 'GABÓN', 'nacionalidad' => 'GABONÉS/A'],
            ['id_georef' => 220, 'continente_id' => 2, 'nombre' => 'GAMBIA', 'nacionalidad' => 'GAMBIANA/O'],
            ['id_georef' => 221, 'continente_id' => 2, 'nombre' => 'GHANA', 'nacionalidad' => 'GHANÉS/A'],
            ['id_georef' => 222, 'continente_id' => 2, 'nombre' => 'GUINEA', 'nacionalidad' => 'GUINEANA/O'],
            ['id_georef' => 223, 'continente_id' => 2, 'nombre' => 'GUINEA ECUATORIAL', 'nacionalidad' => 'ECUATOGUINEANA/O'],
            ['id_georef' => 224, 'continente_id' => 2, 'nombre' => 'GUINEA-BISÁU', 'nacionalidad' => 'GUINEANA/O'],
            ['id_georef' => 225, 'continente_id' => 2, 'nombre' => 'KENIA', 'nacionalidad' => 'KENIATA'],
            ['id_georef' => 226, 'continente_id' => 2, 'nombre' => 'LESOTO', 'nacionalidad' => 'LESOTENSE'],
            ['id_georef' => 227, 'continente_id' => 2, 'nombre' => 'LIBERIA', 'nacionalidad' => 'LIBERIANA/O'],
            ['id_georef' => 228, 'continente_id' => 2, 'nombre' => 'LIBIA', 'nacionalidad' => 'LIBIA/O'],
            ['id_georef' => 229, 'continente_id' => 2, 'nombre' => 'MADAGASCAR', 'nacionalidad' => 'MALGACHE'],
            ['id_georef' => 230, 'continente_id' => 2, 'nombre' => 'MALAWI', 'nacionalidad' => 'MALAWÍ'],
            ['id_georef' => 231, 'continente_id' => 2, 'nombre' => 'MALÍ', 'nacionalidad' => 'MALIENSE'],
            ['id_georef' => 203, 'continente_id' => 2, 'nombre' => 'MARRUECOS', 'nacionalidad' => 'MARROQUÍ'],
            ['id_georef' => 232, 'continente_id' => 2, 'nombre' => 'MAURICIO', 'nacionalidad' => 'MAURICIANA/O'],
            ['id_georef' => 233, 'continente_id' => 2, 'nombre' => 'MAURITANIA', 'nacionalidad' => 'MAURITANA/O'],
            ['id_georef' => 234, 'continente_id' => 2, 'nombre' => 'MOZAMBIQUE', 'nacionalidad' => 'MOZAMBIQUEÑA/O'],
            ['id_georef' => 235, 'continente_id' => 2, 'nombre' => 'NAMIBIA', 'nacionalidad' => 'NAMIBIA/O'],
            ['id_georef' => 236, 'continente_id' => 2, 'nombre' => 'NÍGER', 'nacionalidad' => 'NIGERINA/O'],
            ['id_georef' => 205, 'continente_id' => 2, 'nombre' => 'NIGERIA', 'nacionalidad' => 'NIGERIANA/O'],
            ['id_georef' => 237, 'continente_id' => 2, 'nombre' => 'REPÚBLICA CENTROAFRICANA', 'nacionalidad' => 'CENTROAFRICANA/O'],
            ['id_georef' => 238, 'continente_id' => 2, 'nombre' => 'REPÚBLICA DEMOCRÁTICA DEL CONGO', 'nacionalidad' => 'CONGOLEÑA/O'],
            ['id_georef' => 239, 'continente_id' => 2, 'nombre' => 'RUANDA', 'nacionalidad' => 'RUANDÉS/A'],
            ['id_georef' => 240, 'continente_id' => 2, 'nombre' => 'SANTOMÉ Y PRÍNCIPE', 'nacionalidad' => 'SANTOTOMENSE'],
            ['id_georef' => 241, 'continente_id' => 2, 'nombre' => 'SENEGAL', 'nacionalidad' => 'SENEGALÉS/A'],
            ['id_georef' => 242, 'continente_id' => 2, 'nombre' => 'SEYCHELLES', 'nacionalidad' => 'SEYCHELLENSE'],
            ['id_georef' => 243, 'continente_id' => 2, 'nombre' => 'SIERRA LEONA', 'nacionalidad' => 'SIERRALEONÉS/A'],
            ['id_georef' => 244, 'continente_id' => 2, 'nombre' => 'SOMALIA', 'nacionalidad' => 'SOMALÍ'],
            ['id_georef' => 245, 'continente_id' => 2, 'nombre' => 'SUAZILANDIA', 'nacionalidad' => 'SUAZI'],
            ['id_georef' => 204, 'continente_id' => 2, 'nombre' => 'SUDÁFRICA', 'nacionalidad' => 'SUDAFRICANA/O'],
            ['id_georef' => 246, 'continente_id' => 2, 'nombre' => 'SUDÁN', 'nacionalidad' => 'SUDANÉS/A'],
            ['id_georef' => 247, 'continente_id' => 2, 'nombre' => 'SUDÁN DEL SUR', 'nacionalidad' => 'SUDANÉS/A'],
            ['id_georef' => 248, 'continente_id' => 2, 'nombre' => 'TANZANIA', 'nacionalidad' => 'TANZANA/O'],
            ['id_georef' => 249, 'continente_id' => 2, 'nombre' => 'TOGO', 'nacionalidad' => 'TOGOLÉS/A'],
            ['id_georef' => 250, 'continente_id' => 2, 'nombre' => 'TÚNEZ', 'nacionalidad' => 'TUNECINA/O'],
            ['id_georef' => 251, 'continente_id' => 2, 'nombre' => 'UGANDA', 'nacionalidad' => 'UGANDÉS/A'],
            ['id_georef' => 252, 'continente_id' => 2, 'nombre' => 'YIBUTI', 'nacionalidad' => 'YIBUTIANA/O'],
            ['id_georef' => 253, 'continente_id' => 2, 'nombre' => 'ZAMBIA', 'nacionalidad' => 'ZAMBIANA/O'],
            ['id_georef' => 254, 'continente_id' => 2, 'nombre' => 'ZIMBABUE', 'nacionalidad' => 'ZIMBABUENSE'],

            // ASIA (continente_id: 6)
            ['id_georef' => 401, 'continente_id' => 6, 'nombre' => 'AFGANISTÁN', 'nacionalidad' => 'AFGANA/O'],
            ['id_georef' => 402, 'continente_id' => 6, 'nombre' => 'ARABIA SAUDITA', 'nacionalidad' => 'SAUDÍ'],
            ['id_georef' => 438, 'continente_id' => 6, 'nombre' => 'ARMENIA', 'nacionalidad' => 'ARMENIA/O'],
            ['id_georef' => 409, 'continente_id' => 6, 'nombre' => 'AZERBAIYÁN', 'nacionalidad' => 'AZERBAIYANA/O'],
            ['id_georef' => 403, 'continente_id' => 6, 'nombre' => 'BAHRÉIN', 'nacionalidad' => 'BAHREINÍ'],
            ['id_georef' => 404, 'continente_id' => 6, 'nombre' => 'BANGLADÉS', 'nacionalidad' => 'BANGLADESÍ'],
            ['id_georef' => 439, 'continente_id' => 6, 'nombre' => 'BRUNEI', 'nacionalidad' => 'BRUNEANA/O'],
            ['id_georef' => 440, 'continente_id' => 6, 'nombre' => 'BUTÁN', 'nacionalidad' => 'BUTANÉS/A'],
            ['id_georef' => 417, 'continente_id' => 6, 'nombre' => 'CAMBOYA', 'nacionalidad' => 'CAMBOYANA/O'],
            ['id_georef' => 407, 'continente_id' => 6, 'nombre' => 'CHINA', 'nacionalidad' => 'CHINA/O'],
            ['id_georef' => 430, 'continente_id' => 6, 'nombre' => 'COREA DEL SUR', 'nacionalidad' => 'COREANA/O'],
            ['id_georef' => 431, 'continente_id' => 6, 'nombre' => 'COREA DEL NORTE', 'nacionalidad' => 'COREANA/O'],
            ['id_georef' => 408, 'continente_id' => 6, 'nombre' => 'EMIRATOS ÁRABES UNIDOS', 'nacionalidad' => 'EMIRATÍ'],
            ['id_georef' => 447, 'continente_id' => 6, 'nombre' => 'FILIPINAS', 'nacionalidad' => 'FILIPINA/O'],
            ['id_georef' => 441, 'continente_id' => 6, 'nombre' => 'GEORGIA', 'nacionalidad' => 'GEORGIANA/O'],
            ['id_georef' => 410, 'continente_id' => 6, 'nombre' => 'INDIA', 'nacionalidad' => 'INDIO/A'],
            ['id_georef' => 411, 'continente_id' => 6, 'nombre' => 'INDONESIA', 'nacionalidad' => 'INDONESIA/O'],
            ['id_georef' => 412, 'continente_id' => 6, 'nombre' => 'IRAQ', 'nacionalidad' => 'IRAQUÍ'],
            ['id_georef' => 413, 'continente_id' => 6, 'nombre' => 'IRÁN', 'nacionalidad' => 'IRANÍ'],
            ['id_georef' => 414, 'continente_id' => 6, 'nombre' => 'ISRAEL', 'nacionalidad' => 'ISRAELÍ'],
            ['id_georef' => 415, 'continente_id' => 6, 'nombre' => 'JAPÓN', 'nacionalidad' => 'JAPONÉS/A'],
            ['id_georef' => 416, 'continente_id' => 6, 'nombre' => 'JORDANIA', 'nacionalidad' => 'JORDANIA/O'],
            ['id_georef' => 448, 'continente_id' => 6, 'nombre' => 'KAZAJISTÁN', 'nacionalidad' => 'KAZAJA/O'],
            ['id_georef' => 449, 'continente_id' => 6, 'nombre' => 'KIRGUISTÁN', 'nacionalidad' => 'KIRGUISA/O'],
            ['id_georef' => 418, 'continente_id' => 6, 'nombre' => 'KUWAIT', 'nacionalidad' => 'KUWAITÍ'],
            ['id_georef' => 419, 'continente_id' => 6, 'nombre' => 'LAOS', 'nacionalidad' => 'LAOSIANA/O'],
            ['id_georef' => 420, 'continente_id' => 6, 'nombre' => 'LÍBANO', 'nacionalidad' => 'LIBANÉS/A'],
            ['id_georef' => 421, 'continente_id' => 6, 'nombre' => 'MALASIA', 'nacionalidad' => 'MALASIA/O'],
            ['id_georef' => 422, 'continente_id' => 6, 'nombre' => 'MALDIVAS', 'nacionalidad' => 'MALDIVA/O'],
            ['id_georef' => 423, 'continente_id' => 6, 'nombre' => 'MONGOLIA', 'nacionalidad' => 'MONGOLA/O'],
            ['id_georef' => 405, 'continente_id' => 6, 'nombre' => 'MYANMAR', 'nacionalidad' => 'BIRMANA/O'],
            ['id_georef' => 424, 'continente_id' => 6, 'nombre' => 'NEPAL', 'nacionalidad' => 'NEPALÍ'],
            ['id_georef' => 425, 'continente_id' => 6, 'nombre' => 'OMÁN', 'nacionalidad' => 'OMANÍ'],
            ['id_georef' => 426, 'continente_id' => 6, 'nombre' => 'PAKISTÁN', 'nacionalidad' => 'PAKISTANÍ'],
            ['id_georef' => 442, 'continente_id' => 6, 'nombre' => 'PALESTINA', 'nacionalidad' => 'PALESTINA/O'],
            ['id_georef' => 427, 'continente_id' => 6, 'nombre' => 'QATAR', 'nacionalidad' => 'QATARÍ'],
            ['id_georef' => 432, 'continente_id' => 6, 'nombre' => 'SINGAPUR', 'nacionalidad' => 'SINGAPURENSE'],
            ['id_georef' => 433, 'continente_id' => 6, 'nombre' => 'SIRIA', 'nacionalidad' => 'SIRIA/O'],
            ['id_georef' => 434, 'continente_id' => 6, 'nombre' => 'SRI LANKA', 'nacionalidad' => 'ESRILANQUÉS/A'],
            ['id_georef' => 435, 'continente_id' => 6, 'nombre' => 'TAILANDIA', 'nacionalidad' => 'TAILANDÉS/A'],
            ['id_georef' => 443, 'continente_id' => 6, 'nombre' => 'TAYIKISTÁN', 'nacionalidad' => 'TAYIKA/O'],
            ['id_georef' => 450, 'continente_id' => 6, 'nombre' => 'TIMOR ORIENTAL', 'nacionalidad' => 'TIMORENSE'],
            ['id_georef' => 444, 'continente_id' => 6, 'nombre' => 'TURKMENISTÁN', 'nacionalidad' => 'TURCOMANA/O'],
            ['id_georef' => 445, 'continente_id' => 6, 'nombre' => 'TURQUÍA', 'nacionalidad' => 'TURCA/O'],
            ['id_georef' => 446, 'continente_id' => 6, 'nombre' => 'UZBEKISTÁN', 'nacionalidad' => 'UZBEKA/O'],
            ['id_georef' => 437, 'continente_id' => 6, 'nombre' => 'VIETNAM', 'nacionalidad' => 'VIETNAMITA'],
            ['id_georef' => 451, 'continente_id' => 6, 'nombre' => 'YEMEN', 'nacionalidad' => 'YEMENÍ'],

            // OCEANÍA (continente_id: 7)
            ['id_georef' => 501, 'continente_id' => 7, 'nombre' => 'AUSTRALIA', 'nacionalidad' => 'AUSTRALIANA/O'],
            ['id_georef' => 502, 'continente_id' => 7, 'nombre' => 'FIJI', 'nacionalidad' => 'FIYIANA/O'],
            ['id_georef' => 505, 'continente_id' => 7, 'nombre' => 'ISLAS MARSHALL', 'nacionalidad' => 'MARSHALÉS/A'],
            ['id_georef' => 506, 'continente_id' => 7, 'nombre' => 'ISLAS SALOMÓN', 'nacionalidad' => 'SALOMONENSE'],
            ['id_georef' => 522, 'continente_id' => 7, 'nombre' => 'KIRIBATI', 'nacionalidad' => 'KIRIBATIANA/O'],
            ['id_georef' => 511, 'continente_id' => 7, 'nombre' => 'MICRONESIA', 'nacionalidad' => 'MICRONESIA/O'],
            ['id_georef' => 515, 'continente_id' => 7, 'nombre' => 'NAURU', 'nacionalidad' => 'NAURUANA/O'],
            ['id_georef' => 504, 'continente_id' => 7, 'nombre' => 'NUEVA ZELANDA', 'nacionalidad' => 'NEOZELANDÉS/A'],
            ['id_georef' => 516, 'continente_id' => 7, 'nombre' => 'PALAOS', 'nacionalidad' => 'PALAUANA/O'],
            ['id_georef' => 523, 'continente_id' => 7, 'nombre' => 'PAPÚA NUEVA GUINEA', 'nacionalidad' => 'PAPÚ'],
            ['id_georef' => 507, 'continente_id' => 7, 'nombre' => 'SAMOA', 'nacionalidad' => 'SAMOANA/O'],
            ['id_georef' => 508, 'continente_id' => 7, 'nombre' => 'TONGA', 'nacionalidad' => 'TONGANA/O'],
            ['id_georef' => 512, 'continente_id' => 7, 'nombre' => 'TUVALU', 'nacionalidad' => 'TUVALUANA/O'],
            ['id_georef' => 509, 'continente_id' => 7, 'nombre' => 'VANUATU', 'nacionalidad' => 'VANUATUENSE'],
        ];

        $reserved = [
            'ARGENTINA' => 158,
            'BOLIVIA' => 159,
            'ESPAÑA' => 6,
            'UCRANIA' => 38,
            'CHILE' => 162,
            'PARAGUAY' => 165,
            'PERÚ' => 166,
            'URUGUAY' => 168,
            'VENEZUELA' => 169,
        ];

        $nextId = 1;
        $usedIds = array_values($reserved);
        $finalNaciones = [];
        $now = now();

        foreach ($nacionesRaw as $n) {
            $nombre = $n['nombre'];
            if (isset($reserved[$nombre])) {
                $n['id'] = $reserved[$nombre];
            } else {
                while (in_array($nextId, $usedIds)) {
                    $nextId++;
                }
                $n['id'] = $nextId;
                $usedIds[] = $nextId;
                $nextId++;
            }
            $n['created_at'] = $now;
            $n['updated_at'] = $now;
            $finalNaciones[] = $n;
        }

        // Ordenar por ID para facilitar la lectura en la DB si es necesario
        usort($finalNaciones, fn($a, $b) => $a['id'] <=> $b['id']);

        foreach (array_chunk($finalNaciones, 50) as $chunk) {
            DB::table('nacions')->insert($chunk);
        }
    }
}
