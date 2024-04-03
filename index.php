<?php 

header('Content-type: text/html; charset=UTF-8');

require 'includes/dbconn.php';

//define value into hdn-if-display-lang-select to trigger language select form
$if_display_lang_select = 1; //display lang select form - not after form submission

//Check if opening after the submission of the signature form
if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['fromForm'] == "SignManifest") {
    
    //var_dump($_POST);
 
    //Insert signature record into tbl_signatures
    $sql = "INSERT INTO tbl_signatures
                        (Email, SignName, SignNameLatin, CityCountry, LangCode, DateTimeStamp, IfApproved) 
                 VALUES ('" . mysqli_escape_string($conn, $_POST['sign-email']) . "','"
                            . mysqli_escape_string($conn, $_POST['sign-name']) . "','"
                            . mysqli_escape_string($conn, $_POST['sign-name-latin']) . "','"
                            . mysqli_escape_string($conn, $_POST['sign-city-country']) . "','"
                            . mysqli_escape_string($conn, $_POST['hdn-sign-lang-code']) . "','"
                            . date('Y-m-d H:i:s') . "','" 
                            . 0 . "');";

    $res = mysqli_query($conn, @$sql);
    if($res === false){
        echo mysqli_error($conn);
    } else {
        //get ID of the record inserted into tbl_signatures
        $sig_id = mysqli_insert_id($conn);

        //insert Occupation into tbl_occupations
        $sql = "INSERT INTO tbl_occupations 
                            (SignID, LangCode, Occupation) 
                     VALUES ('" . $sig_id . "', '"
                                . $_POST['hdn-sign-lang-code'] . "','"
                                . $_POST['sign-occupation'] . "');";
        $res = mysqli_query($conn, @$sql);
        if($res === false){
            echo mysqli_error($conn);                        
        }
    }

    //define value into hdn-if-display-lang-select to prevent displaying the language select form
    $if_display_lang_select = 0; //do not display lang select form
    $lang = $_POST['hdn-sign-lang-code'];

} //(if from sign form submision)

//Get languages for the Select Language pop-up
$sql_langs = "SELECT LangCode, LangName FROM tbl_langs;";
$res_langs = mysqli_query($conn, @$sql_langs);
if($res_langs === false){
    echo mysqli_error($conn);
} else {
    $langs = mysqli_fetch_all($res_langs, MYSQLI_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="icon" href="favicon.svg">
    <title>Stop RUscism Manifesto</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">

    <link rel="icon" href="favicon.ico">
    
    <script defer src="js/script.js"></script>
    
    <link href="css/app.css" rel="stylesheet">

</head>

<body id="manifest-body">
  <div class="body-wrapper">
    
    <input type="hidden" name="hdn-if-display-lang-select" id="hdn-if-display-lang-select" value="<?= $if_display_lang_select ?>" />
    
    <!----------------------- LANGUAGE POP-UP ----------------------->
    <div id="language-popup" class="popup">
      <div class="popup-content">
        <p id="popup-title" class="popup-title">Select your preferred language to get started</p>
        <select id="language-select">
            <?php foreach($langs as $l): ?>
            <option value="<?= $l["LangCode"] ?>"><?= $l["LangName"] ?></option>
            <?php endforeach; ?>  
        </select>
        <button id="accept-btn" class="ok-btn">Continue</button>
      </div>
    </div>
    <!---------------------- (LANGUAGE POP-UP) ---------------------->

    <!------------------------ SIGNATURE FORM ----------------------->
    <form method="post">
        <div id="sign-popup" class="popup" style="display:none">
            <div class="popup-content">
                <p id="sign-popup-title" class="popup-title" style="font-size: 18px">Want to sign the Manifest? Provide the info below and click [SIGN]</p>
                
                <label id="lbl-sign-email" for="sign-email" class="sign-label" autofocus>E-mail:&nbsp;</label>
                <input type="email" name="sign-email" id="sign-email" maxlength="250"><br>
                
                <label id="lbl-sign-name" for="sign-name" class="sign-label">First and laste name:&nbsp;</label>
                <input type="text" name="sign-name" id="sign-name" maxlength="250" required><br>
                
                <label id="lbl-sign-name-latin" for="sign-name-latin" class="sign-label">First and last name in Latin characters:&nbsp;</label>
                <input type="text" name="sign-name-latin" id="sign-name-latin" maxlength="250" required><br>

                <label id="lbl-sign-occupation" for="lbl-sign-occupation" class="sign-label">First and last name in Latin characters:&nbsp;</label>
                <input type="text" name="sign-occupation" id="sign-occupation" maxlength="500"><br>
                
                <label id="lbl-sign-city-country" for="sign-city-country" class="sign-label"  maxlength="250">City, State/Province, Country:&nbsp;</label>
                <input type="text" name="sign-city-country" id="sign-city-country" maxlength="250" required><br>
                
                <input type="hidden" name="hdn-sign-lang-code" id="hdn-sign-lang-code" value="<?= $lang ?>" />
                <input type="hidden" name="fromForm" id="fromForm" value="SignManifest" /> <!-- needed for identifying submitting form -->

                <button id="cancel-sign-btn" type="button" class="cancel-btn" onclick="hideSignForm()">Cancel</button>
                &nbsp;
                <button id="submit-sign-btn" class="ok-btn">SIGN</button>
            </div>
        </div>
    </form>
    <!----------------------- (SIGNATURE FORM) ---------------------->

    <div id="main-content" class="d-flex flex-column align-items-center" 
         style="display:none;overflow-y:auto;font-family:sans-serif">

        <!-- <h1 id="main-title" class="main-title">Stop RUscism Manifesto</h1> -->
    
        <div class="main-title" style="width:100%;text-align:right">
            <button id="sign-btn-top" class="ok-btn" style="display:none;float:right;border:white solid 1px" onclick="showSignForm();">Sign our Manifest</button>
            <h1 id="thank-you-for-signing" class="main-title" style="display:none;font-size:24px;justify-content:center">Thank you for signing Stop RUscism Manifesto!</h1>
        </div>

        <div style="width:80%;text-align:center">
            <h1 id="title" style="padding-top:15px">
            </h1>
            <h2 id="subtitle"></h2>
            <div id="manifest" style="width:100%;text-align:justify;font-size:large;padding-top:15px"></div>
            <div id="signatures" style="width:100%;text-align:justify;font-size:large;font-style: italic;padding-top:15px;padding-bottom:20px"></div>
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