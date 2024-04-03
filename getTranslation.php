<?php

$source_lang = $_GET['s'];
$target_lang = $_GET['t'];
$text = $_GET['text'];

//if ((!isset($_GET['s'])) || (!isset($_GET['t'])) || (strlen($source_lang) > 3) || (strlen($target_lang) > 3) || (strlen($text) < 1) ) {
if ((!isset($_GET['t'])) || (strlen($target_lang) > 3) || (strlen($text) < 1) ) {
    echo "lang or text parameters are not specified correctly";
    exit;
}

$apiKey = 'XYZ';

$source_lang = "";

//get targen langs
//require 'includes/dbconn.php';

//Form q 
//$q = '&q=' . rawurlencode($text) . '&source=' . $source_lang . '&target=' . $target_lang;
$q = '&q=' . rawurlencode($text) . '&source=' . '' . '&target=' . $target_lang;

//Get translation
$url = 'https://www.googleapis.com/language/translate/v2?key=' . $apiKey . $q;
$handle = curl_init($url);
curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($handle);                 
$responseDecoded = json_decode($response, true);
$responseCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);      //Here we fetch the HTTP response code
curl_close($handle);

if($responseCode != 200) {
    echo 'Fetching translation failed! Server response code:' . $responseCode . '<br>';
    echo 'Error description: ' . $responseDecoded['error']['errors'][0]['message'];
}
else {
    echo json_encode($responseDecoded['data']['translations'][0]["translatedText"], JSON_UNESCAPED_UNICODE);
}


?>