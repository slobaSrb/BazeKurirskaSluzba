<?php 

require 'config.inc.php';

readfile('header.tmpl.html');
echo $_COOKIE["mysqlmongo"];

    $kupci = '';
    $statusi = '';
    $nacini_placanja = '';

    $mysql = isset($_COOKIE["mysqlmongo"]) && $_COOKIE["mysqlmongo"] === "mysql";
    if(isset($_COOKIE["mysqlmongo"]) && $_COOKIE["mysqlmongo"] === "mysql"){
        $db = new mysqli(
            MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE);
        
        
        
        $sql = "SELECT * FROM kupac";
        $result = $db->query($sql);

        $sql = "SELECT * FROM status_porudzbine";
        $statuses = $db->query($sql);

        $sql = "SELECT * FROM nacin_placanja";
        $ways_to_pay = $db->query($sql);
    } else {
        require_once 'vendor/autoload.php';
                    
        $url = 'mongodb://127.0.0.1:27017';
        $dbname = 'kurirska_sluzba';
        $db = null;
        
        $client = new MongoDB\Client($url);
        $db = $client->selectDatabase($dbname);

        $collection = $db->selectCollection("porudzbina");

        $filter  = [];
        $options = ['sort' => ['id_porudzbine' => -1]];

        $result = $collection->findOne($filter, $options);

        $id_porudzbine = $result->id_porudzbine;

        $collection = $db->selectCollection("kupac");

        $filter = [];
        $options = ['sort' => ['id' => 1]];

        $result = $collection->find($filter, $options);

        $collection = $db->selectCollection("status_porudzbine");

        $filter = [];
        $options = ['sort' => ['id_statusa' => 1]];

        $statuses = $collection->find($filter, $options);

        $collection = $db->selectCollection("nacin_placanja");

        $filter = [];
        $options = ['sort' => ['id_nacina_placanja' => 1]];

        $ways_to_pay = $collection->find($filter, $options);
    }

    
    if(isset($_POST['submit'])){
        $ok = true;
        
        if(!isset($_POST['kupci']) || $_POST['kupci'] === ''){
            $ok = false;
        } else {
            $kupci = $_POST['kupci'];
        };

        if(!isset($_POST['statusi']) || $_POST['statusi'] === ''){
            $ok = false;
        } else {
            $statusi = $_POST['statusi'];
        };

        if(!isset($_POST['nacini_placanja']) || $_POST['nacini_placanja'] === ''){
            $ok = false;
        } else {
            $nacini_placanja = $_POST['nacini_placanja'];
        }
    

        if($ok){
            
            if(isset($_COOKIE["mysqlmongo"]) && $_COOKIE["mysqlmongo"] === "mysql"){
                
                // $db = new mysqli(
                //     MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE);
                $db = new mysqli(
                    MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE
                );

                $sql = sprintf("INSERT INTO isporuka (datum_preuzimanja, datum_prispeca_u_grad_destinacije)
                 VALUES (NOW(), date_add(NOW(),interval 5 day))");

                $db->query($sql);

                echo "<p>Isporuka dates added.</p>";

                $db->query("COMMIT");

                $sql = sprintf("INSERT INTO placanje 
                        VALUES (NULL, $nacini_placanja, 500, 0, 0)");

                $db->query($sql);

                $db->query("COMMIT");

                $sql = "SELECT id_isporuke FROM isporuka ORDER BY id_isporuke DESC LIMIT 1";

                $id_isporuke = $db->query($sql);

                $db->query("COMMIT");

                $sql = "SELECT id_placanja FROM placanje ORDER BY id_placanja DESC LIMIT 1";

                $id_placanja = $db->query($sql);

                $db->query("COMMIT");

                $id_isporuke = $id_isporuke->fetch_object()->id_isporuke;
                $id_placanja = $id_placanja->fetch_object()->id_placanja;

                $sql = sprintf(
                "INSERT INTO porudzbina (id_kupca, id_statusa, id_placanja, id_isporuke) VALUES (
                        '%s', '%s', '%s', '%s');",
                        $db->real_escape_string($kupci),
                        $db->real_escape_string($statusi),
                        $db->real_escape_string($id_placanja),
                        $db->real_escape_string($id_isporuke));
                $db->query($sql);
                echo '<p style="color:green">Porudzbina dodata.</p>';
                $db->close();
        
            } else {

               
                
                // $cursor = $collection->aggregate(
                //     array(
                //         array('$group' => array(
                //             '_id' => array(
                //                 'id_isporuke' => array('$id_isporuke')
                //             ),
                //             'id_isporuke' => 
                //             array('$first' => '$id_isporuke'),
                //         )),
                //         array('$sort' => array('id_isporuke' => -1)),
                //         array('$limit' => 1)
                //     )
                // );
                // foreach ($cursor as $row) {
                //     var_dump($row->id_isporuke);
                // };

                $collection = $db->selectCollection("isporuka");

                $filter  = [];
                $options = ['sort' => ['id_isporuke' => -1]];

                $result1 = $collection->findOne($filter, $options);

                $id_isporuke = $result1->id_isporuke;


                $date = (new DateTime())->modify('+5 day');

                $mongoDate = new \MongoDB\BSON\UTCDateTime($date);


                $collection->insertOne(
                    array(
                        'id_isporuke' => ($id_isporuke + 1),
                        'datum_preuzimanja_kurira' => new \MongoDB\BSON\UTCDateTime(),
                        'datum_prispeca_kupcu' => $mongoDate
                    )
                );


                $collection =  $db->selectCollection("porudzbina");
                $insertOneResult = $collection->insertOne(
                array(
                    
                    'id_porudzbine' => ($id_porudzbine + 1),

                    'id_kupca' => (int)$kupci,
                    
                    'id_statusa' => (int)$statusi,
                    
                    'id_placanja' => (int)$nacini_placanja,

                    'id_isporuke' => ($id_isporuke + 1)
                    
                ));
        }
        
    
        
        
    } else {
        echo '<p style="color:red">Porudzbina nije dodata.';
    }
  }
?>

<form 
    action=""
    method="post">

    Kupac: 
    <select name="kupci">
    <option value="">Please select</option>
    <?php 
      
       foreach ($result as $row) {
    
    ?>
        <option value="<?php echo $row['id'] ?>">
        <?php echo $row['ime_prezime']; ?>
        </option>
    <?php } ?>
    </select><br>

    Status: 
    <select name="statusi">
    <option value="">Please select</option>
    <?php 
      
       foreach ($statuses as $row) {
    
    ?>
        <option value="<?php echo $row['id_statusa'] ?>">
        <?php echo $row['opis_statusa']; ?>
        </option>
    <?php } ?>
    </select><br>
    
    Nacin Placanja: 
    <select name="nacini_placanja">
    <option value="">Please select</option>
    <?php 
      
       foreach ($ways_to_pay as $row) {
    
    ?>
        <option value="<?php echo $row['id_nacina_placanja'] ?>">
        <?php echo $row['naziv_placanja']; ?>
        </option>
    <?php } ?>


<?php   
    readfile('insertPorudzbinaForm.php');
    readfile('footer.tmpl.html');
?>