<?php 

 require 'config.inc.php';

 readfile('header.tmpl.html');

 $ime_prezime = '';
 $email = '';
 $adresa_dostave = '';
 $postanski_broj_dostave = '';
 $grad = '';
 $telefon = '';
 
 $ime_prezime_platioca = '';
 $email_platioca = '';
 $adresa_platioca = '';
 $postanski_broj_platioca = '';
 $grad_platioca = '';
 $telefon_platioca = '';

 $id_kupca = '';

 $platioc_postoji = true;


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

    

    if($platioc_postoji){
        if(!isset($_POST['ime_prezime_platioca']) || $_POST['ime_prezime_platioca'] === ''){
            $platioc_postoji = false;
        } else {
            $ime_prezime_platioca = $_POST['ime_prezime_platioca'];
        }
        if(!isset($_POST['email_platioca']) || $_POST['email_platioca'] === ''){
            $platioc_postoji = false;
        } else {
            $email_platioca = $_POST['email_platioca'];
        }
        if(!isset($_POST['adresa_platioca']) || $_POST['adresa_platioca'] === ''){
            $platioc_postoji = false;
        } else {
            $adresa_platioca = $_POST['adresa_platioca'];
        }
        if(!isset($_POST['postanski_broj_platioca']) || $_POST['postanski_broj_platioca'] === ''){
            $platioc_postoji = false;
        } else {
            $postanski_broj_platioca = $_POST['postanski_broj_platioca'];
        }
        if(!isset($_POST['grad_platioca']) || $_POST['grad_platioca'] === ''){
            $platioc_postoji = false;
        } else {
            $grad_platioca = $_POST['grad_platioca'];
        }
        if(!isset($_POST['telefon_platioca']) || $_POST['telefon_platioca'] === ''){
            $platioc_postoji = false;
        } else {
            $telefon_platioca = $_POST['telefon_platioca'];
        }
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

        if($platioc_postoji){

            $sql = sprintf("SELECT * FROM kupac ORDER BY id DESC LIMIT 1",
            $db->real_escape_string($_POST['ime_prezime']));

            $result = $db->query($sql);
            $row = $result->fetch_object();
            $id_kupca = $row->id;

            $sql = sprintf(
                "INSERT INTO platioc(ime_prezime_platioca, email_platioca, adresa_platioca, postanski_broj_platioca, grad_platioca, telefon_platioca, id_kupca) VALUES
                 ('%s', '%s', '%s', '%s', '%s', '%s', '%s')",
                $db->real_escape_string($ime_prezime_platioca),
                $db->real_escape_string($email_platioca),
                $db->real_escape_string($adresa_platioca),
                $db->real_escape_string($postanski_broj_platioca),
                $db->real_escape_string($grad_platioca),
                $db->real_escape_string($telefon_platioca),
                $db->real_escape_string($id_kupca)
            );
        $db->query($sql);
        echo '<p style="color:green">Platioc dodat.</p>';
        } else {
            echo '<p style="color:red">Platioc nije dodat.</p>';
        }

        $db->close();
    } else {
        echo '<p style="color:red">Kupac i platioc nisu dodati.</p>';
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



<div class="form-group">
<label for="ime_prezime_platioca">Ime prezime platioca</label>
<input type="text" class="form-control" name="ime_prezime_platioca" id="ime_prezime_platioca" value="<?php 
    echo htmlspecialchars($ime_prezime_platioca, ENT_QUOTES);
?>">
</div>

<div class="form-group">
<label for="email_platioca">Email platioca</label>
<input type="text" class="form-control" name="email_platioca" id="email_platioca" value="<?php 
    echo htmlspecialchars($email_platioca, ENT_QUOTES);
?>">
</div>

<div class="form-group">
<label for="adresa_platioca">Adresa platioca</label>
<input type="text" class="form-control" name="adresa_platioca" id="adresa_platioca" value="<?php 
    echo htmlspecialchars($adresa_platioca, ENT_QUOTES);
?>">
</div>

<div class="form-group">
<label for="postanski_broj_platioca">Postanski broj platioca</label>
<input type="text" class="form-control" name="postanski_broj_platioca" id="postanski_broj_platioca" value="<?php 
    echo htmlspecialchars($postanski_broj_platioca, ENT_QUOTES);
?>">
</div>

<div class="form-group">
<label for="grad_platioca">Grad platioca</label>
<input type="text" class="form-control" name="grad_platioca" id="grad_platioca" value="<?php 
    echo htmlspecialchars($grad_platioca, ENT_QUOTES);
?>">
</div>

<div class="form-group">
<label for="telefon_platioca">Telefon platioca</label>
<input type="text" class="form-control" name="telefon_platioca" id="telefon_platioca" value="<?php 
    echo htmlspecialchars($telefon_platioca, ENT_QUOTES);
?>">
</div>

<input type="submit" name="submit" class="btn btn-primary" value="Insert artikal">

</form>

<?php 
    readline('footer.tmpl.html');
?>