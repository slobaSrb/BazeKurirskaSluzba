<?php 

 require 'config.inc.php';

 readfile('header.tmpl.html');

 $ime_prezime = '';
 $email = '';
 $adresa_dostave = '';
 $postanski_broj_dostave = '';
 $grad = '';
 $telefon = '';

 
 $kupci = '';
 $statusi = '';
 $nacini_placanja = '';

 if(isset($_POST['submit'])){
    $ok = true;

    if(!isset($_POST['ime_prezime']) || $_POST['ime_prezime'] === ''){
        $ok = false;
    } else {
        $ime_prezime = $_POST['ime_prezime'];
    }
    if(!isset($_POST['email']) || $_POST['email'] === ''){
        $ok = false;
    } else {
        $email = $_POST['email'];
    }
    if(!isset($_POST['adresa_dostave']) || $_POST['adresa_dostave'] === ''){
        $ok = false;
    } else {
        $adresa_dostave = $_POST['adresa_dostave'];
    }
    if(!isset($_POST['postanski_broj_dostave']) || $_POST['postanski_broj_dostave'] === ''){
        $ok = false;
    } else {
        $postanski_broj_dostave = $_POST['postanski_broj_dostave'];
    }
    if(!isset($_POST['grad']) || $_POST['grad'] === ''){
        $ok = false;
    } else {
        $grad = $_POST['grad'];
    }
    if(!isset($_POST['telefon']) || $_POST['telefon'] === ''){
        $ok = false;
    } else {
        $telefon = $_POST['telefon'];
    }

    if($ok){
        $db = new mysqli(
            MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE
        );
        // $sql = 'call populate_random_artikal_data()';
        $sql = sprintf(
            "INSERT INTO kupac (ime_prezime, email, adresa_dostave, postanski_broj_dostave, grad, telefon) VALUES 
            ('%s', '%s', '%s', '%s', '%s', '%s')", 
            $db->real_escape_string($ime_prezime),
            $db->real_escape_string($email),
            $db->real_escape_string($adresa_dostave),
            $db->real_escape_string($postanski_broj_dostave),
            $db->real_escape_string($grad),
            $db->real_escape_string($telefon));
        $db->query($sql);
        echo '<p style="color:green">Kupac dodat.</p>';

        $db->close();
    } else {
        echo '<p style="color:red">Kupac nije dodat.</p>';
    }
 }
?>

<form 
 action = ""
 method = "post">
<div class="form-group">
<label for="ime_prezime">Ime prezime kupca</label>
<input type="text" class="form-control" name="ime_prezime" id="ime_prezime" value="<?php 
    echo htmlspecialchars($ime_prezime, ENT_QUOTES);
?>">
</div>

<div class="form-group">
<label for="email">Email kupca</label>
<input type="text" class="form-control" name="email" id="email" value="<?php 
    echo htmlspecialchars($email, ENT_QUOTES);
?>">
</div>

<div class="form-group">
<label for="adresa_dostave">Adresa kupca</label>
<input type="text" class="form-control" name="adresa_dostave" id="adresa_dostave" value="<?php 
    echo htmlspecialchars($adresa_dostave, ENT_QUOTES);
?>">
</div>

<div class="form-group">
<label for="postanski_broj_dostave">Postanski broj kupca</label>
<input type="text" class="form-control" name="postanski_broj_dostave" id="postanski_broj_dostave" value="<?php 
    echo htmlspecialchars($postanski_broj_dostave, ENT_QUOTES);
?>">
</div>

<div class="form-group">
<label for="grad">Grad kupca</label>
<input type="text" class="form-control" name="grad" id="grad" value="<?php 
    echo htmlspecialchars($grad, ENT_QUOTES);
?>">
</div>

<div class="form-group">
<label for="telefon">Telefon kupca</label>
<input type="text" class="form-control" name="telefon" id="telefon" value="<?php 
    echo htmlspecialchars($telefon, ENT_QUOTES);
?>">
</div>

<input type="submit" name="submit" class="btn btn-primary" value="Insert artikal">

</form>

<?php 
    readline('footer.tmpl.html');
?>