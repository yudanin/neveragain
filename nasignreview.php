<?php 

require 'includes/dbconn.php';

//Check password
session_start();
$sword = "XYZ";

$dispErr = "none";
$ifLoggedIn = false;
if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['fromForm'] == "YES") {
    $_SESSION['sword'] = $_POST['secret-word'];
    if(strtolower($_POST['secret-word']) != $sword)
        $dispErr = "flex";
}
if (isset($_SESSION['sword'])) {
    if(strtolower($_SESSION['sword']) == $sword){
        $ifLoggedIn = true;
    }
} 

//if from sign review form submission
if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['fromForm'] == "ReviewSignature") {
    
    //var_dump($_POST);
 
    //Insert signature record into tbl_signatures
    
    //Get occupations
    
    //Form occupations' values string for the query
    //sign-occupation-xx
    $q = "";
    foreach($_POST as $key => $value){
        if(preg_match('/^sign-occupation-/i', $key) == 1){
            $arr = explode("-",$key);
            $lc = $arr[count($arr)-1];
            $q .= "(" . $_POST['sign-id'] . ", '" . $lc . "', '" . $value . "'),";
        }
        //var_dump($q);
    }
    $q = rtrim($q, ',');
   
    $sqldelete = "DELETE FROM tbl_occupations WHERE SignID = " . $_POST['sign-id'] . ";";
    $sqlinsert = "INSERT INTO tbl_occupations (SignID, LangCode, Occupation)
                  VALUES " . $q . ";";
    $sqlupdate = "UPDATE tbl_signatures 
             SET Email='" . mysqli_escape_string($conn, $_POST['sign-email']) . "',
                 SignName='" . mysqli_escape_string($conn, $_POST['sign-name']) . "',
                 SignNameLatin='" . mysqli_escape_string($conn, $_POST['sign-name-latin']) . "',
                 CityCountry='" . mysqli_escape_string($conn, $_POST['sign-city-country']) . "',
                 IfApproved = 1  
                 WHERE ID = " . $_POST['sign-id'] . ";";

    $rc = false;
    try{
        $res = mysqli_query($conn, @$sqlupdate);
        $rc = true;
    } catch (mysqli_sql_exception $e) { 
        var_dump($e);
        echo "Failed to update signatory's info, will NOT proceed to update occupation.";
    } 

    if($rc){
        try{
            $res = mysqli_query($conn, @$sqldelete);
        } catch (mysqli_sql_exception $e) { 
            var_dump($e);
            echo "Failed to delete old version of the signatory occupation's translations, will NOT proceed to update new translation of the occupation.";
            $rc = false;
        }
    }

    if($rc){
        try{
            $res = mysqli_query($conn, @$sqlinsert);
        } catch (mysqli_sql_exception $e) { 
            var_dump($e);
            echo "Failed to save new translation of the signatory's occupation.";
        }
    }
    
} //(if from sign review form submision)



//Get languages to generate language boxes
$sql_langs = "SELECT LangCode, LangName FROM tbl_langs;";
$res_langs = mysqli_query($conn, @$sql_langs);
if($res_langs === false){
    echo mysqli_error($conn);
} else {
    $langs = mysqli_fetch_all($res_langs, MYSQLI_ASSOC);
}

//Get unapproved signatures
$sql_signs = "SELECT ID, Email, SignName, SignNameLatin, CityCountry, LangCode, DateTimeStamp FROM tbl_signatures WHERE IfApproved = 0 ORDER BY DateTimeStamp ASC;";
$res_signs = mysqli_query($conn, @$sql_signs);
if($res_signs === false){
    echo mysqli_error($conn);
} else {
    $signs = mysqli_fetch_all($res_signs, MYSQLI_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="icon" href="favicon.svg">
    <title>REVIEW SIGNATURES</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">

    <link rel="icon" href="favicon.ico">
    
    <script defer src="js/script.js"></script>
    
    <link href="css/app.css" rel="stylesheet">

</head>

<body>

<div class="body-wrapper">

    <div id="transl_loader" class="loader" style="display:none">
        <span>T</span>
        <span>R</span>
        <span>R</span>
        <span>N</span>
        <span>S</span>
        <span>L</span>
        <span>A</span>
        <span>T</span>
        <span>I</span>
        <span>N</span>
        <span>G</span>
    </div>

    <!----------------------- LOGIN FORM ---------------------------------->

<?php if($ifLoggedIn == false): ?>
    <form method="post">
        <div id="login-popup" class="popup" style="display:flex;background-color:#CDDDE9">
            <div class="popup-content">
                <p style="color:red;display:<?= $dispErr ?>">Questa non è una parola magica...</p>
                
                <label id="lbl-secret-word" for="secret-word" class="sign-label" style="font-size:1.5em">La parola magica:&nbsp;</label>
                <input type="password" name="secret-word" id="secret-word" maxlength="250" autofocus><br>
                <input type="hidden" name="fromForm" id="fromLogin" value="YES" />
                <br>
                <button id="cancel-login-btn" type="button" class="cancel-btn" onclick="">INVIA</button>
                &nbsp;
                <button id="login-btn" class="ok-btn">APPROVE</button>
            </div>
        </div>
    </form>
<?php endif ?>
    <!---------------------- (LOGIN FORM) --------------------------------->

    <!------------------------ SIGNATURE REVIEW FORM ----------------------->
    <form method="post">
        <div id="review-sign-popup" class="sign-popup" style="display:none">
            <div class="sign-popup-content" style="width:80%">
                <p id="review-sign-popup-title" class="popup-title" style="font-size: 18px"></p>
                
                <input type="hidden" id="sign-id" name="sign-id" value=""/>
                <p id="signed-on-datetime"></p>

                <hr>

                <!-- <label id="lbl-sign-datetime" for="sign-datetime" class="sign-label" autofocus></label> -->
                <!-- <input type="text" name="sign-datetime" id="sign-datetime" maxlength="100"><br> -->
                <table>
                    <tr>
                        <td>
                <label id="lbl-sign-email" for="sign-email" class="sign-label" autofocus>E-mail:&nbsp;</label>
                <input type="email" name="sign-email" id="sign-email" maxlength="250"><br>
                        </td>
                        <td>&nbsp;&nbsp;</td>
                        <td>
                <label id="lbl-sign-name" for="sign-name" class="sign-label">First and laste name:&nbsp;</label>
                <input type="text" name="sign-name" id="sign-name" maxlength="250" required><br>
                        </td>
                    </tr>
                    <tr>
                        <td>
                <label id="lbl-sign-name-latin" for="sign-name-latin" class="sign-label">First and last name in Latin characters:&nbsp;</label>
                <input type="text" name="sign-name-latin" id="sign-name-latin" maxlength="250" required><br>
                        </td>
                        <td>&nbsp;&nbsp;</td>
                        <td>
                <label id="lbl-sign-city-country" for="sign-city-country" class="sign-label"  maxlength="250">City, State/Province, Country:&nbsp;</label>
                <input type="text" name="sign-city-country" id="sign-city-country" maxlength="250" required><br>
                        </td>
                    </tr>
                </table>
                <fieldset class="sign-fieldset">
                    <table width="100%">
                        <tr>
                            <td style="font-size:1.2rem">
                                Occupation
                            </td>
                            <td style="text-align:right">
                                <button type="button" id="translate-occupation-btn" class="ok-btn" onclick="translateOccupartion();">TRANSLATE</button>
                            </td>
                        <tr>
                    </table>
                    <br>
                    <div class="columns-holder">
                        <?php foreach($langs as $l): ?>
                            <div class="box">
                                <label id="lbl-sign-occupation-<?= $l["LangCode"] ?>" for="sign-occupation-<?= $l["LangCode"] ?>" class="sign-label"  maxlength="250"><?= $l["LangName"] ?>:&nbsp;</label>
                                <input type="text" name="sign-occupation-<?= $l["LangCode"] ?>" id="sign-occupation-<?= $l["LangCode"] ?>" class="input-sign-review-form" maxlength="250">
                            </div>
                        <?php endforeach; ?> 
                    </div>
                </fieldset>

                <input type="hidden" name="fromForm" id="fromForm" value="ReviewSignature" /> <!-- needed for identifying submitting form -->

                <br>
                <button id="close-sign-approve-btn" type="button" class="cancel-btn" onclick="hideSignReviewForm()">CLOSE</button>
                &nbsp;
                <button id="sign-approve-btn" class="ok-btn">APPROVE</button>
            </div>
        </div>
    </form>
    <!----------------------- (SIGNATURE REVIEW FORM) ---------------------->

    <div id="main-content" class="d-flex flex-column align-items-center" style="display:none;overflow-y:auto;font-family:sans-serif">

        <div style="width:80%;text-align:center">
            <h1 id="title" style="padding-top:15px">Signatures awaiting approval:</h1>
            <div id="signatures-awaiting-approval" style="width:100%;text-align:justify;font-size:large;padding-top:15px">
                <table class="sign-review-table">
                    <tr>
                        <th></th>
                        <th>Signed On</th>
                        <th>Email</th>
                        <th>Name</th>
                        <th>Name - Latin characters</th>
                        <th>City, State/Province, Country</th>
                    <tr>
                <?php $i = 0;
                      foreach($signs as $s): 
                        $sn = str_replace("'", "&apos;", $s);
                      ?>
                    <tr>
                        <td><button id="sign-review-btn" class="ok-btn" onclick='reviewSignature(<?= $sn["ID"] ?>, "<?= $sn["SignName"] ?>", <?php echo json_encode($sn); ?>);'>REVIEW</button></td>
                        <td><?= $s['DateTimeStamp'] ?></td>
                        <td><?= $s['Email'] ?></td>
                        <td><?= $s['SignName'] ?></td>
                        <td><?= $s['SignNameLatin'] ?></td>
                        <td><?= $s['CityCountry'] ?></td>
                    <tr>
                <?php $i++;
                      endforeach; ?> 
                </table>
            </div>
        </div>
        
        <div style="width:80%;display:flex;justify-content:center;align-items:center;margin-bottom:10px">
            <button id="sign-btn-bottom" class="ok-btn" style="display:none;" onclick="showSignForm();">Sign our Manifest</button>
        </div>
    </div>
      <!----------------------- Footer ----------------------->
      <footer>
        <div class="container-fluid">
          <div class="row">
            <div class="col-6 text-start align-items-center">
              © <span id="current-year"></span> Copyright Stop RUscism
            </div>
            <div class="col-6 text-end align-items-center">
              <span id="credits-btn" class="credits-btn footer-secondary-element"><a href="mailto:info@stop-ruscism.com">Contact Us</a></span>
            </div>
          </div>
        </div>
      </footer>
    </div>
  </div>
</body>

</html>