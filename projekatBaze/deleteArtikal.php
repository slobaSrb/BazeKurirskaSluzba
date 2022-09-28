<?php 
  require 'config.inc.php';

  readfile('header.tmpl.html');

  if (isset($_GET['id']) && ctype_digit($_GET['id'])) {
    $id = $_GET['id'];
  } 

  if(isset($_COOKIE["mysqlmongo"]) && $_COOKIE["mysqlmongo"] === "mysql"){
      $db = new mysqli(
        MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE);
      $sql = "DELETE FROM artikal WHERE id_artikla=$id";
      $db->query($sql);
      echo '<p style="color:orange">Artikal obrisan.</p>';
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

      echo "Connection to database successfully";
      
      $collection = $db->selectCollection("artikal");
      $collection->deleteOne( array( "_id_artikla" => $id ) );
  }
  readfile('footer.tmpl.html');
?>