<?php 
     require 'config.inc.php';

     readfile('header.tmpl.html');
     
     if(isset($_COOKIE["mysqlmongo"]) && $_COOKIE["mysqlmongo"] === "mysql"){
         $db = new mysqli(
            MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE);

         $mesecna_zarada = 0;
         $mesecni_promet = 0;

         $godisnja_zarada = 0;
         $godisnji_promet = 0;


         $sql = "CALL p_ukupna_zarada_promet($mesecna_zarada,$mesecni_promet,$godisnja_zarada,$godisnji_promet)";
         // call the stored procedure
         $db->query($sql);
         
         $db->query("SET @mesecna_zarada = 0");
         $db->query("SET @mesecni_promet = 0");
         $db->query("SET @godisnja_zarada = 0");
         $db->query("SET @godisnji_promet = 0");

         $db->query("CALL p_ukupna_zarada_promet(@mesecna_zarada,@mesecni_promet,@godisnja_zarada,@godisnji_promet)");

         $result = $db->query("SELECT @mesecna_zarada AS mz");
         $mesecna_zarada = $result->fetch_object()->mz;

         $result = $db->query("SELECT @mesecni_promet AS mp");
         $mesecni_promet = $result->fetch_object()->mp;

         $result = $db->query("SELECT @godisnja_zarada AS gz");
         $godisnja_zarada = $result->fetch_object()->gz;

         $result = $db->query("SELECT @godisnji_promet AS gp");
         $godisnji_promet = $result->fetch_object()->gp;
         
         echo "<p>Mesecna zarada: $mesecna_zarada</p>";
         echo "<p>Mesecni promet: $mesecni_promet</p>";
         echo "<p>Godisnja zarada: $godisnja_zarada</p>";
         echo "<p>Godisnji promet: $godisnji_promet</p>";

         $db->close();
     } else {
         // mongo db code here
         // This path should point to Composer's autoloader
         require_once 'vendor/autoload.php';
               
         $url = 'mongodb://127.0.0.1:27017';
         $dbname = 'kurirska_sluzba';
         $db = null;
         
         $client = new MongoDB\Client($url);
         $db = $client->selectDatabase($dbname);
         
         $collection = $db->selectCollection("porudzbina");

         $result = $collection->aggregate([
            [
               '$lookup' => [
                  'from' => 'placanje',
                  'localField' => 'id_placanja',
                  'foreignField' => 'id_placanja',
                  'as' => 'placanje'
               ]
            ],
            [
               '$unwind' => '$placanje'     
            ],
            [
               '$lookup' => [
                  'from' => 'isporuka',
                  'localField' => 'id_isporuke',
                  'foreignField' => 'id_isporuke',
                  'as' => 'isporuka'
            ]
            ],
            [
               '$unwind' => '$isporuka'
            ],
            [
               '$group' => [
                  '_id' => [
                     'id_porudzbine' => [
                        '$id_porudzbine'
                     ]
                  ],
                  'cena_dostave' => 
                  ['$first' => '$placanje.cena_dostave'],
                  'cena_za_placanje' => 
                  ['$first' => '$placanje.cena_za_placanje'],
                  'datum_prispeca_kupcu' => 
                  ['$first' => '$isporuka.datum_prispeca_kupcu'],
                  'datum_preuzimanja_kurira' => 
                  ['$first' => '$isporuka.datum_preuzimanja_kurira']
               ]
            ]
         ]);

         $monthArray = array();
         $yearArray = array();
         $monthDelivery = array();
         $yearDelivery = array();
         // month = expense
         // year = expense

         foreach($result as $row){
            $monthYear = date('m/Y', (int)str_split($row->datum_preuzimanja_kurira,10)[0]);
            $year = date('Y', (int)str_split($row->datum_preuzimanja_kurira,10)[0]);
            
            // month in a year expenses
            if(array_key_exists($monthYear,$monthArray)){
               $monthArray[$monthYear] += $row->cena_za_placanje;
               $monthDelivery[$monthYear] += $row->cena_dostave;
            } else {
               $monthArray[$monthYear] = $row->cena_za_placanje;
               $monthDelivery[$monthYear] = $row->cena_dostave;
            }

            // yearly expenses
            if(array_key_exists($year,$yearArray)){
               $yearArray[$year] += $row->cena_za_placanje;
               $yearDelivery[$year] += $row->cena_dostave;
            } else {
               $yearArray[$year] = $row->cena_za_placanje;
               $yearDelivery[$year] = $row->cena_dostave;
            }
         }

         foreach($monthDelivery as $key => $row){
            ?>Mesec: <label><?php echo $key;?></label> 
            Zarada: <label><?php echo $row;?></label><br><?php
         }

         foreach($monthArray as $key => $row){
            ?>Mesec: <label><?php echo $key;?></label> 
            Promet: <label><?php echo $row;?></label><br><?php
         }

         foreach($yearDelivery as $key => $row){
            ?>Godina: <label><?php echo $key;?></label> 
            Zarada: <label><?php echo $row;?></label><br><?php
         }

         foreach($yearArray as $key => $row){
            ?>Godina: <label><?php echo $key;?></label> 
            Promet: <label><?php echo $row;?></label><br><?php
         }

     }
    readfile('footer.tmpl.html');
?>

<?php
   // This path should point to Composer's autoloader
   // require_once 'vendor/autoload.php';
	
	// $url = 'mongodb://127.0.0.1:27017';
	// $dbname = 'kurirska_sluzba';
	// $db = null;
	
	// $client = new MongoDB\Client($url);
	// $db = $client->selectDatabase($dbname);

   // echo "Connection to database successfully";
	
   // echo "Database mydb selected";

   // $collection = $db->selectCollection("porudzbina");
   // var_dump($collection->find()->toArray());
?>