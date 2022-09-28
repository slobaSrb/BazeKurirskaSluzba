<?php 
  require 'config.inc.php';
  readfile('header.tmpl.html');
  if (isset($_GET['id']) && ctype_digit($_GET['id'])) {
    $id = $_GET['id'];
  }

  if(isset($_COOKIE["mysqlmongo"]) && $_COOKIE["mysqlmongo"] === "mysql"){
    $db = new mysqli(
        MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE);
    $sql = "SELECT * FROM porudzbina";
    $porudzbine = $db->query($sql);

    $db = new mysqli(
        MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE);
    $sql = "SELECT * FROM artikal";
    $artikli = $db->query($sql);
  } else {
    require_once 'vendor/autoload.php';
                    
    $url = 'mongodb://127.0.0.1:27017';
    $dbname = 'kurirska_sluzba';
    $db = null;
    
    $client = new MongoDB\Client($url);
    $db = $client->selectDatabase($dbname);
    
    $collectionPorudzbine = $db->selectCollection("porudzbina");

    $collectionArtikli= $db->selectCollection("artikal");
    $filter  = [];
    $options = ['sort' => ['id_artikla' => 1]];

    $artikli = $collectionArtikli->find($filter, $options);
    
    
  }
  $porudzbina = '';
  $artikal = '';
  $kolicina = '';

  $mysql = isset($_COOKIE["mysqlmongo"]) && $_COOKIE["mysqlmongo"] === "mysql";

  if(isset($_POST['submit'])){
    $ok = true;

    if(!isset($_POST['porudzbina']) || $_POST['porudzbina'] === ''){
        $ok = false;
    } else {
        $porudzbina = $_POST['porudzbina'];
    }

    if(!isset($_POST['artikal']) || $_POST['artikal'] === ''){
        $ok = false;
    } else {
        $artikal = $_POST['artikal'];
    }

    if(!isset($_POST['kolicina']) || $_POST['kolicina'] === ''){
        $ok = false;
    } else {
        $kolicina = $_POST['kolicina'];
    }

    if($ok){
        if(isset($_COOKIE["mysqlmongo"]) && $_COOKIE["mysqlmongo"] === "mysql"){
        $db = new mysqli(
            MYSQL_HOST,MYSQL_USER,MYSQL_PASSWORD,MYSQL_DATABASE);

        $sql = sprintf(
            "INSERT INTO artikli_porudzbine (id_artikli_porudzbine, id_porudzbine, id_artikla, kolicina) VALUES 
            (NULL, '%s', '%s', '%s')",
            $db->real_escape_string($porudzbina),
            $db->real_escape_string($artikal),
            $db->real_escape_string($kolicina)
        );


        $db->query($sql);
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

        $collection = $db->selectCollection("artikli_porudzbine");

        $filter  = [];
        $options = ['sort' => ['id_artikli_porudzbine' => -1]];

        $id_artikli_porudzbine = $collection->findOne($filter, $options);

        $collection->insertOne(
            array(
            'id_artikli_porudzbine' => ($id_artikli_porudzbine->id_artikli_porudzbine + 1),
            'id_porudzbine' => (int)$porudzbina,
            'id_artikla' => (int)$artikal,
            'kolicina' => (int)$kolicina
            )
        );

      }
        echo "<p style='color:green'>Artikli porudzbine dodati.</p>";
    } else {
        echo "<p style='color:red'>Artikli porudzbine nisu dodati.</p>";
    }
  } else {
      
      $porudzbina = $id;
  }

  

?>

<form 
    action=""
    method="post">
    <?php
    
    ?>
    <br>

    Porudzbina: <input type="text" name="porudzbina" value="<?php
    if($mysql){
        echo htmlspecialchars($porudzbina, ENT_QUOTES);
    } else {
        echo $porudzbina;
    }
    
    ?>"><br>
    
    Artikal: 
    <select name="artikal">
    <option value="">Please select</option>
    <?php 

        foreach ($artikli as $row) {
           
    ?>
    <option value="<?php echo $row['id_artikla'] ?>">
    <?php 
        if($mysql){
            $sql = sprintf("SELECT * FROM artikal WHERE id_artikla=%s",
            $db->real_escape_string($row['id_artikla']));
            $artikli1 = $db->query($sql);
            echo $artikli1->fetch_object()->naziv_artikla;
        } else {

            $filter  = ['id_artikla' => $row['id_artikla']];
            $options = [];

            $artikli = $collectionArtikli->findOne($filter, $options);
    
            echo $artikli->naziv_artikla;
        }
    ?>
    <?php
        }
    ?></select><br>

    Kolicina: <input type="text" name="kolicina" value="<?php
    echo htmlspecialchars($kolicina, ENT_QUOTES);
    ?>"><br>

    <input type="submit" name="submit" class="btn btn-primary" value="Ubaci artikal u porudzbinu">
</form>
<?php 
    readfile('footer.tmpl.html');
?>
