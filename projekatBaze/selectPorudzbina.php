<?php 
    // select porudzbina 
    readfile('header.tmpl.html');
    require 'config.inc.php';
?> 
<ul>
    <?php 
    echo $_COOKIE["mysqlmongo"];
if($_COOKIE["mysqlmongo"] === "mysql"){
    $db = new mysqli(
    MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE);
    $sql = 'SELECT * FROM porudzbina';
    $result = $db->query($sql);

  

  foreach ($result as $row) {
  
    $sql = sprintf('SELECT ime_prezime FROM kupac WHERE id=%s',
    $db->real_escape_string($row['id_kupca']));

    $ime_prezime = $db->query($sql);

    $sql = sprintf('SELECT opis_statusa FROM status_porudzbine WHERE id_statusa=%s',
    $db->real_escape_string($row['id_statusa']));

    $opis_statusa = $db->query($sql);

    $sql = sprintf('SELECT np.naziv_placanja FROM nacin_placanja AS np INNER JOIN placanje AS p ON p.id_nacina_placanja = np.id_nacina_placanja WHERE id_placanja=%s',
    $db->real_escape_string($row['id_placanja']));

    $naziv_placanja = $db->query($sql);


    $sql = sprintf('SELECT datum_preuzimanja FROM isporuka WHERE id_isporuke=%s',
    $db->real_escape_string($row['id_isporuke']));
    $datum_preuzimanja = $db->query($sql);
    
    $sql = sprintf('SELECT datum_prispeca_u_grad_destinacije FROM isporuka WHERE id_isporuke=%s',
    $db->real_escape_string($row['id_isporuke']));
    $datum_prispeca_u_grad_destinacije = $db->query($sql);

    $sql = sprintf('SELECT * FROM porudzbina WHERE 
    id_kupca=%s AND id_statusa=%s AND id_placanja=%s AND id_isporuke=%s',
    $db->real_escape_string($row['id_kupca']),
    $db->real_escape_string($row['id_statusa']),
    $db->real_escape_string($row['id_placanja']),
    $db->real_escape_string($row['id_isporuke']));

    $id_porudzbine = $db->query($sql);

    printf(
      '<li>
      <span>Ime prezime:(%s) Opis statusa:(%s) Naziv placanja:(%s) 
      Datum preuzimanja (%s) DPUGD (%s)</span>
      <a href="insertArtikliPorudzbine.php?id=%s">INSERT ARTIKLI PORUDZBINE</a>
      </li>',
      htmlspecialchars($ime_prezime->fetch_object()->ime_prezime, ENT_QUOTES),
      htmlspecialchars($opis_statusa->fetch_object()->opis_statusa, ENT_QUOTES),
      htmlspecialchars($naziv_placanja->fetch_object()->naziv_placanja, ENT_QUOTES),
      htmlspecialchars($datum_preuzimanja->fetch_object()->datum_preuzimanja, ENT_QUOTES),
      htmlspecialchars($datum_prispeca_u_grad_destinacije->fetch_object()->datum_prispeca_u_grad_destinacije, ENT_QUOTES),
      htmlspecialchars($id_porudzbine->fetch_object()->id_porudzbine, ENT_QUOTES)
    );
}

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
    
    $collection = $db->selectCollection("kupac");
    $cursor = $collection->aggregate(
       array(
            array('$lookup' => array(
                'from' => "porudzbina",
                'localField' => "id",
                'foreignField' => "id_kupca",
                'as' => 'porudzbina'
            )),
       array('$unwind' => '$porudzbina'),
       array('$group' => array(
                '_id' => array(
                    'id_porudzbine' => array('$porudzbina.id_porudzbine')
                ),
                'ime_prezime' => 
                array('$first' => '$ime_prezime'),
                'id_porudzbine' => 
                array('$first' => 
                    '$porudzbina.id_porudzbine'),
                'id_statusa' =>
                array('$first' =>
                    '$porudzbina.id_statusa'),
                'id_placanja' =>
                array('$first' =>
                    '$porudzbina.id_placanja'),
                'id_isporuke' =>
                array('$first' =>
                    '$porudzbina.id_isporuke'),
        )),
        array('$project' => array(
            'ime_prezime' => 1,
            '_id' => 0,
            'id_porudzbine' => 1,
            'id_statusa' => 1,
            'id_placanja' => 1,
            'id_isporuke' => 1
        )),
        array('$lookup' => array(
            'from' => "status_porudzbine",
            'localField' => "id_statusa",
            'foreignField' => "id_statusa",
            'as' => 'status'
        )),
        array('$lookup' => array(
            'from' => "placanje",
            'localField' => "id_placanja",
            'foreignField' => "id_placanja",
            'as' => 'placanje'
        )),
        array('$lookup' => array(
            'from' => "isporuka",
            'localField' => "id_isporuke",
            'foreignField' => "id_isporuke",
            'as' => 'isporuka'
        )),
        array('$unwind' => '$status'),
        array('$unwind' => '$placanje'),
        array('$unwind' => '$isporuka'),
        array('$group' => array(
            '_id' => array(
                'id_porudzbine' => array('$id_porudzbine')
            ),
            'id_porudzbine' => 
            array('$first' => '$id_porudzbine'),
            'ime_prezime' => 
            array('$first' => '$ime_prezime'),
            'opis_statusa' => 
            array('$first' => 
                '$status.opis_statusa'),
            'id_nacina_placanja' => 
            array('$first' => 
                '$placanje.id_nacina_placanja'),
            'datum_preuzimanja' => 
            array('$first' => 
                '$isporuka.datum_preuzimanja_kurira'),
            'DPUGD' => 
            array('$first' => 
                '$isporuka.datum_prispeca_kupcu'),
        )),
        array('$lookup' => array(
            'from' => "nacin_placanja",
            'localField' => "id_nacina_placanja",
            'foreignField' => "id_nacina_placanja",
            'as' => 'nacin_placanja'
        )),
        array('$unwind' => '$nacin_placanja'),
        array('$group' => array(
            '_id' => array(
                'id_porudzbine' => array('$id_porudzbine')
            ),
            'id_porudzbine' => 
            array('$first' => '$id_porudzbine'),
            'ime_prezime' => 
            array('$first' => '$ime_prezime'),
            'opis_statusa' => 
            array('$first' => 
                '$opis_statusa'),
            'datum_preuzimanja' => 
            array('$first' => 
                '$datum_preuzimanja'),
            'DPUGD' => 
            array('$first' => 
                '$DPUGD'),
            'naziv_placanja' => 
            array('$first' => 
                '$nacin_placanja.naziv_placanja'),
        )),
        ),
    );

    foreach ($cursor as $row) {

        printf(
            '<li>
            <span>Ime prezime:(%s) Opis statusa:(%s) Naziv placanja:(%s) 
            Datum preuzimanja (%s) DPUGD (%s)</span>
            <a href="insertArtikliPorudzbine.php?id=%s">INSERT ARTIKLI PORUDZBINE</a>
            </li>',
            htmlspecialchars($row->ime_prezime, ENT_QUOTES),
            htmlspecialchars($row->opis_statusa, ENT_QUOTES),
            htmlspecialchars($row->naziv_placanja, ENT_QUOTES),
            htmlspecialchars(date('d-m-Y H:i:s', (int)str_split($row->datum_preuzimanja,10)[0]), ENT_QUOTES),
            htmlspecialchars(date('d-m-Y H:i:s', (int)str_split($row->DPUGD,10)[0]), ENT_QUOTES),
            htmlspecialchars($row->id_porudzbine, ENT_QUOTES)
          );

        
    };

    $ops = array(
        array(
            '$project' => array(
                "author" => 1,
                "tags"   => 1,
            )
        ),
        array('$unwind' => '$tags'),
        array(
            '$group' => array(
                "_id" => array("tags" => '$tags'),
                "authors" => array('$addToSet' => '$author'),
            ),
        ),
    );
    //$results = $c->aggregate($ops);
    //var_dump($results);


}