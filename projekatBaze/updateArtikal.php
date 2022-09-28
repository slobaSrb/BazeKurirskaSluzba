<?php
    require 'config.inc.php';

    if (isset($_GET['id']) && ctype_digit($_GET['id'])) {
        $id = $_GET['id'];
    }

    readfile('header.tmpl.html');

    $naziv_artikla = '';
    $cena = '';
    $opis = '';

    if (isset($_POST['submit'])) {
        $ok = true;

        if (!isset($_POST['naziv_artikla']) || $_POST['naziv_artikla'] === '') {
        $ok = false;
        } else {
        $naziv_artikla = $_POST['naziv_artikla'];
        };

        if (!isset($_POST['cena']) || $_POST['cena'] === '') {
        $ok = false;
        } else {
        $cena = $_POST['cena'];
        };

        if (!isset($_POST['opis']) || $_POST['opis'] === '') {
        $ok = false;
        } else {
        $opis = $_POST['opis'];
        };

        if ($ok) {
            // add database code here
          if(isset($_COOKIE["mysqlmongo"]) && $_COOKIE["mysqlmongo"] === "mysql"){
                $db = new mysqli(
                MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE);
                $sql = sprintf(
                "UPDATE artikal SET naziv_artikla='%s', cena='%s', opis='%s'
                  WHERE id_artikla=%s",
                $db->real_escape_string($naziv_artikla),
                $db->real_escape_string($cena),
                $db->real_escape_string($opis),
                $id);
              $db->query($sql);
              $db->close();
            } else {
              require_once 'vendor/autoload.php';
                    
              $url = 'mongodb://127.0.0.1:27017';
              $dbname = 'kurirska_sluzba';
              $db = null;
              
              $client = new MongoDB\Client($url);
              $db = $client->selectDatabase($dbname);

              $collection = $db->selectCollection("artikal");

              $collection->updateOne(
                [ 'id_artikla' => $id ],
                [ '$set' => [ 'naziv_artikla' => $naziv_artikla ]],
                []
             );
             $collection->updateOne(
                [ 'id_artikla' => $id ],
                [ '$set' => [ 'cena' => (int)$cena]]
              );
              $collection->updateOne(
                [ 'id_artikla' => $id ],
                [ '$set' => [ 'opis' => $opis]]
              );
            }
            echo '<p style="color:green">Artikal updejtovan.</p>';
            
          } else {
            echo '<p style="color:red">Artikal nije updejtovan.</p>';
          }
    } else {
      if(isset($_COOKIE["mysqlmongo"]) && $_COOKIE["mysqlmongo"] === "mysql"){
        $db = new mysqli(
          MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE);
        $sql = "SELECT * FROM artikal WHERE id_artikla=$id";
        $result = $db->query($sql);
        $object = $result->fetch_object();
        $naziv_artikla = $object->naziv_artikla;
        $cena = $object->cena;
        $opis = $object->opis;
    
        $db->close();
      } else {
        require_once 'vendor/autoload.php';
                    
        $url = 'mongodb://127.0.0.1:27017';
        $dbname = 'kurirska_sluzba';
        $db = null;
        
        $client = new MongoDB\Client($url);
        $db = $client->selectDatabase($dbname);

        $collectionArtikli= $db->selectCollection("artikal");

        $filter  = ['id_artikla' => (int)$id];
        $options = [];
        
        $artikli = $collectionArtikli->findOne($filter, $options);
        

        $naziv_artikla = $artikli->naziv_artikla;
        $cena = $artikli->cena;
        $opis = $artikli->opis;
      }
      }
?>

<form
      action=""
      method="post">
        Naziv artikla: <input type="text" name="naziv_artikla" value="<?php
        echo htmlspecialchars($naziv_artikla, ENT_QUOTES);
        ?>"><br>

        Cena: <input type="text" name="cena" value="<?php
        echo htmlspecialchars($cena, ENT_QUOTES);
        ?>"><br>

        Opis: <input type="text" name="opis" value="<?php
        echo htmlspecialchars($opis, ENT_QUOTES);
        ?>"><br>

<input type="submit" name="submit" class="btn btn-primary" value="Updejtuj artikal">
</form>