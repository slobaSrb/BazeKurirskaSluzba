<?php 

 require 'config.inc.php';

 readfile('header.tmpl.html');

 $naziv_artikla = '';
 $cena = '';
 $opis = '';

 if(isset($_POST['submit'])){
    $ok = true;

    if(!isset($_POST['naziv_artikla']) || $_POST['naziv_artikla'] === ''){
        $ok = false;
    } else {
        $naziv_artikla = $_POST['naziv_artikla'];
    }
    if(!isset($_POST['cena']) || $_POST['cena'] === ''){
        $ok = false;
    } else {
        $cena = $_POST['cena'];
    }
    if(!isset($_POST['opis']) || $_POST['opis'] === ''){
        $ok = false;
    } else {
        $opis = $_POST['opis'];
    }

    if($ok){
        if(isset($_COOKIE["mysqlmongo"]) && $_COOKIE["mysqlmongo"] === "mysql"){
        $db = new mysqli(
            MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE
        );
        // $sql = 'call populate_random_artikal_data()';
        $sql = sprintf(
            "INSERT INTO artikal (naziv_artikla, cena, opis) VALUES 
            ('%s', '%s', '%s')", 
            $db->real_escape_string($naziv_artikla),
            $db->real_escape_string($cena),
            $db->real_escape_string($opis));
        $db->query($sql);
        $db->close();
        } else {
            require_once 'vendor/autoload.php';
                
            $url = 'mongodb://127.0.0.1:27017';
            $dbname = 'kurirska_sluzba';
            $db = null;
            
            $client = new MongoDB\Client($url);
            $db = $client->selectDatabase($dbname);

            echo "Connection to database successfully";
            
            $collection = $db->selectCollection("artikal");

            $filter  = [];
            $options = ['sort' => ['id_artikla' => -1]];

            $result = $collection->findOne($filter, $options);

            $id_artikla = $result->id_artikla;

            var_dump($id_artikla);

            $collection->insertOne(
                array(
                'id_artikla' => ($id_artikla + 1),
                'naziv_artikla' => $naziv_artikla,
                'cena' => (int)$cena,
                'opis' => $opis
                )
            );
        }
        echo '<p style="color:green">Artikal dodat.</p>';
        
    } else {
        echo '<p style="color:red">Artikal nije dodat.</p>';
    }
 }
?>

<form 
 action = ""
 method = "post">
 <div class="form-group">
<label for="naziv_artikla">Naziv artikla</label>
<input type="text" class="form-control" name="naziv_artikla" id="naziv_artikla" value="<?php 
    echo htmlspecialchars($naziv_artikla, ENT_QUOTES);
?>">
</div>
<div class="form-group">
<label for="cena">Cena</label>
<input type="text" class="form-control" name="cena" id="cena" value="<?php 
    echo htmlspecialchars($cena, ENT_QUOTES);
?>">
</div>
<div class="form-group">
<label for="opis">Opis</label>
<input type="text" class="form-control" name="opis" id="opis" value="<?php 
    echo htmlspecialchars($opis, ENT_QUOTES);
?>">
</div>

<input type="submit" name="submit" class="btn btn-primary" value="Insert artikal">

</form>

<?php 
    readline('footer.tmpl.html');
?>