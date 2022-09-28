
<?php
  readfile('header.tmpl.html');
  require 'config.inc.php';

  if(isset($_POST['submit'])){
    if(isset($_COOKIE["mysqlmongo"]) && $_COOKIE["mysqlmongo"] === "mysql"){

        
     $conn = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
        // Create database
    $sql = "CREATE DATABASE kurirska_sluzba1";
    if ($conn->query($sql) === TRUE) {
    echo "Database created successfully";
    } else {
    echo "Error creating database: " . $conn->error;
    }
    $sql = "USE kurirska_sluzba1";
    if ($conn->query($sql) === TRUE) {
    echo "Database in use";
    } else {
    echo "Error using database: " . $conn->error;
    }

    $sql = "CREATE TABLE kurirska_sluzba1.`artikal` (
        `id_artikla` int(11) NOT NULL AUTO_INCREMENT,
        `naziv_artikla` varchar(50) DEFAULT NULL,
        `putanja_do_slika` varchar(100) DEFAULT NULL,
        `cena` int(11) DEFAULT NULL,
        `opis` varchar(255) DEFAULT NULL,
        PRIMARY KEY (`id_artikla`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
      if ($conn->query($sql) === TRUE) {
        echo "Tables created successfully";
        } else {
        echo "Error creating tables: " . $conn->error;
        }

    $sql = "CREATE TABLE kurirska_sluzba1.`status_porudzbine` (
        `id_statusa` int(10) unsigned NOT NULL AUTO_INCREMENT,
        `opis_statusa` varchar(50) DEFAULT NULL,
        PRIMARY KEY (`id_statusa`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
    if ($conn->query($sql) === TRUE) {
        echo "Tables created successfully";
        } else {
        echo "Error creating tables status_porudzbine: " . $conn->error;
        }
      $sql = "CREATE TABLE kurirska_sluzba1.`kupac` (
        `ime_prezime` varchar(100) DEFAULT NULL,
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `email` varchar(100) DEFAULT NULL,
        `adresa_dostave` varchar(100) DEFAULT NULL,
        `postanski_broj_dostave` int(5) unsigned DEFAULT NULL,
        `grad` varchar(50) DEFAULT NULL,
        `telefon` varchar(20) DEFAULT NULL,
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
      if ($conn->query($sql) === TRUE) {
        echo "Tables created successfully";
        } else {
        echo "Error creating tables: " . $conn->error;
        }
      $sql = "CREATE TABLE kurirska_sluzba1.`isporuka` (
        `id_isporuke` int(10) unsigned NOT NULL AUTO_INCREMENT,
        `datum_preuzimanja` datetime NOT NULL,
        `datum_prispeca_u_grad_destinacije` datetime DEFAULT NULL,
        PRIMARY KEY (`id_isporuke`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
      if ($conn->query($sql) === TRUE) {
        echo "Tables created successfully";
        } else {
        echo "Error creating tables: " . $conn->error;
        }
      $sql = "CREATE TABLE kurirska_sluzba1.`nacin_placanja` (
        `id_nacina_placanja` smallint(5) unsigned NOT NULL,
        `naziv_placanja` varchar(30) NOT NULL,
        PRIMARY KEY (`id_nacina_placanja`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
      if ($conn->query($sql) === TRUE) {
        echo "Tables created successfully";
        } else {
        echo "Error creating tables: " . $conn->error;
        }
      $sql = "CREATE TABLE kurirska_sluzba1.`placanje` (
        `id_placanja` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
        `id_nacina_placanja` smallint(5) unsigned DEFAULT NULL,
        `cena_dostave` int(11) DEFAULT NULL,
        `cena_artikla` int(11) DEFAULT NULL,
        `cena_za_placanje` int(11) DEFAULT NULL,
        PRIMARY KEY (`id_placanja`),
        KEY `placanje_ibfk_1` (`id_nacina_placanja`),
        CONSTRAINT `placanje_ibfk_1` FOREIGN KEY (`id_nacina_placanja`) REFERENCES `nacin_placanja` (`id_nacina_placanja`) ON UPDATE CASCADE
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
      if ($conn->query($sql) === TRUE) {
        echo "Tables created successfully";
        } else {
        echo "Error creating tables: " . $conn->error;
        }
      $sql = "CREATE TABLE kurirska_sluzba1.`porudzbina` (
        `id_porudzbine` int(11) NOT NULL AUTO_INCREMENT,
        `id_kupca` int(11) DEFAULT NULL,
        `id_statusa` int(10) unsigned DEFAULT NULL,
        `id_placanja` smallint(5) unsigned DEFAULT NULL,
        `id_isporuke` int(10) unsigned NOT NULL,
        `identifikator_porudzbine` varchar(30) DEFAULT NULL,
        PRIMARY KEY (`id_porudzbine`),
        KEY `fk_id_isporuke` (`id_isporuke`),
        KEY `fk_id_kupca` (`id_kupca`),
        KEY `fk_id_placanja` (`id_placanja`),
        KEY `fk_id_statusa` (`id_statusa`),
        CONSTRAINT `fk_id_isporuke` FOREIGN KEY (`id_isporuke`) REFERENCES `isporuka` (`id_isporuke`) ON UPDATE CASCADE,
        CONSTRAINT `fk_id_kupca` FOREIGN KEY (`id_kupca`) REFERENCES `kupac` (`id`) ON UPDATE CASCADE,
        CONSTRAINT `fk_id_placanja` FOREIGN KEY (`id_placanja`) REFERENCES `placanje` (`id_placanja`) ON UPDATE CASCADE,
        CONSTRAINT `fk_id_statusa` FOREIGN KEY (`id_statusa`) REFERENCES `status_porudzbine` (`id_statusa`) ON UPDATE CASCADE
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
      if ($conn->query($sql) === TRUE) {
        echo "Tables created successfully";
        } else {
        echo "Error creating tables: " . $conn->error;
        }
      $sql = "CREATE TABLE kurirska_sluzba1.`artikli_porudzbine` (
        `id_artikli_porudzbine` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `id_porudzbine` int(11) NOT NULL,
        `id_artikla` int(11) NOT NULL,
        `kolicina` int(11) unsigned NOT NULL,
        PRIMARY KEY (`id_artikli_porudzbine`),
        KEY `id_porudzbine` (`id_porudzbine`),
        KEY `id_artikla` (`id_artikla`),
        CONSTRAINT `fk_1_id_id_artikla` FOREIGN KEY (`id_artikla`) REFERENCES `artikal` (`id_artikla`) ON UPDATE CASCADE,
        CONSTRAINT `fk_1_id_porudzbine` FOREIGN KEY (`id_porudzbine`) REFERENCES `porudzbina` (`id_porudzbine`) ON UPDATE CASCADE
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
      if ($conn->query($sql) === TRUE) {
        echo "Tables created successfully";
        } else {
        echo "Error creating tables: " . $conn->error;
        }
       $sql = "CREATE TABLE kurirska_sluzba1.`artikli_zarada` (
        `naziv_artikla` varchar(50) NOT NULL,
        `cena` int(11) NOT NULL,
        PRIMARY KEY (`naziv_artikla`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
       if ($conn->query($sql) === TRUE) {
        echo "Tables created successfully";
        } else {
        echo "Error creating tables: " . $conn->error;
        }
       $sql = "CALL populate_nacin_placanja_table()";
       if($conn->query($sql) === TRUE) {
        echo "procedure nacin placanja called";
       } else {
        echo "didn't call nacin placanja procedure" . $conn->error;
       }
    $conn->close();
    } else {
                // This path should point to Composer's autoloader
        require_once 'vendor/autoload.php';
            
        $url = 'mongodb://127.0.0.1:27017';
        $dbname = 'kurirska_sluzba1';
        $db = null;
        
        $client = new MongoDB\Client($url);
        $db = $client->selectDatabase($dbname);

        echo "Connection to database successfully";


        // $collection->insertOne(
        //         array(
        //         'id_isporuke' => ($id_isporuke + 1),
        //         'datum_preuzimanja_kurira' => new \MongoDB\BSON\UTCDateTime(),
        //         'datum_prispeca_kupcu' => new \MongoDB\BSON\UTCDateTime()
        //         )
        // );
        }
    }

?>
<?php
    readfile('populateTablesForm.php');

    readfile('footer.tmpl.html');
?>