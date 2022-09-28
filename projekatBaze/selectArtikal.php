<?php
  readfile('header.tmpl.html');
?>
<ul>
<?php
  require 'config.inc.php';

  echo $_COOKIE["mysqlmongo"];
if($_COOKIE["mysqlmongo"] === "mysql"){
  $db = new mysqli(
    MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE);
  $sql = 'SELECT * FROM artikal';
  $result = $db->query($sql);

  foreach ($result as $row) {
      printf(
        '<li><span>Naziv:(%s) Cena:(%s) Opis:(%s)</span>
        <a href="updateArtikal.php?id=%s">Update artikal</a>
        <a href="deleteArtikal.php?id=%s">Delete artikal</a>
        </li>',
        htmlspecialchars($row['naziv_artikla'], ENT_QUOTES),
        htmlspecialchars($row['cena'], ENT_QUOTES),
        htmlspecialchars($row['opis'], ENT_QUOTES),
        htmlspecialchars($row['id_artikla'], ENT_QUOTES),
        htmlspecialchars($row['id_artikla'], ENT_QUOTES)
      );
  }

  $db->close();
} else {
    // naziv

    // cena 

    // opis 
    require_once 'vendor/autoload.php';
        
    $url = 'mongodb://127.0.0.1:27017';
    $dbname = 'kurirska_sluzba';
    $db = null;
    
    $client = new MongoDB\Client($url);
    $db = $client->selectDatabase($dbname);

    echo "Connection to database successfully";
    
    $collection = $db->selectCollection("artikal");

    $cursor = $collection->aggregate(
        array(
            array('$group' => array(
                '_id' => array(
                    'id_artikla' => array('$id_artikla')
                ),
                'id_artikla' => 
                array('$first' => '$id_artikla'),
                'naziv_artikla' => 
                array('$first' => '$naziv_artikla'),
                'cena' => 
                array('$first' => '$cena'),
                'opis' => 
                array('$first' => '$opis'),
              )),
              array(
                '$sort' => array('id_artikla' => 1)
              )
        )
    );

    foreach ($cursor as $row) {
      printf(
        '<li><span>Naziv:(%s) Cena:(%s) Opis:(%s)</span>
        <a href="updateArtikal.php?id=%s">Update artikal</a>
        <a href="deleteArtikal.php?id=%s">Delete artikal</a>
        </li>',
        htmlspecialchars($row->naziv_artikla, ENT_QUOTES),
        htmlspecialchars($row->cena, ENT_QUOTES),
        htmlspecialchars($row->opis, ENT_QUOTES),
        htmlspecialchars($row->id_artikla, ENT_QUOTES),
        htmlspecialchars($row->id_artikla, ENT_QUOTES)
      );
  }

    

    };


?>
</ul>
<?php
  readfile('footer.tmpl.html');
?>