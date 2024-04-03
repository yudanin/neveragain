let selectedLanguage = 'en'; // Default language is English
const formElement = document.getElementById('signup-form');
const linkElement = document.getElementById('mobile-link');
const linkExternalElement = document.getElementById('mobile-link-external');
const creditsBtn = document.getElementById('credits-btn');
const creditsOverlay = document.getElementById('credits-overlay');


// Function to update the website content based on the selected language
function updateContent() {
    // Update the link text with the selected language
    formElement.src = formLanguages[selectedLanguage].link;

    linkExternalElement.href = formLanguages[selectedLanguage].externalLink
} //(updateContent)



function openCredits() {

    creditsOverlay.style.display = 'flex'

}



function closeCredits() {
    creditsOverlay.style.display = 'none'
}



function loadManifest() {

    const yearElement = document.getElementById('current-year')
    yearElement.innerHTML = new Date().getFullYear();
    
    //If from sign form submission
    HdnIfDispLangSelect = document.getElementById('hdn-if-display-lang-select');
    IfLangSelect = HdnIfDispLangSelect.value;

    if(IfLangSelect == 0) {
        //get language code from hdn-sign-lang-code if coming from sign form submission
        languageSelect = document.getElementById('hdn-sign-lang-code');
    } else {
        //get language code from lang select popup
        languageSelect = document.getElementById('language-select');
    }
    selectedLanguage = languageSelect.value;

    //Load manifest
    var ajax = new XMLHttpRequest();
    var method = "GET";
    var url = "getManifest.php?lang=" + selectedLanguage;
    var asynchronous=true;
    ajax.open(method,url,asynchronous);
    ajax.send();
    ajax.onreadystatechange = function(){
    if(this.readyState == 4 && this.status == 200){
        var data = JSON.parse( this.responseText);
        document.getElementById("title").innerHTML = data[0].Title;
        document.getElementById("subtitle").innerHTML = data[0].Subtitle;
        document.getElementById("manifest").innerHTML = data[0].ManifestText;
        }

    } 

    //Get signatures
    var ajax = new XMLHttpRequest();
    var method = "GET";
    var url = "getSignatures.php?lang=" + selectedLanguage;
    var asynchronous=true;
    ajax.open(method,url,asynchronous);
    ajax.send();
    ajax.onreadystatechange = function(){
    if(this.readyState == 4 && this.status == 200){
        var data = JSON.parse( this.responseText);
        let obj = {};
        data.forEach((v) => {
            //let key = v.MsgName;
            document.getElementById("signatures").innerHTML += "<br>" + v.SignName;
            
            if(v.SignNameLatin.length > 0 && v.SignNameLatin != v.SignName){
                if(v.SignName != "")
                    document.getElementById("signatures").innerHTML += " / ";
                document.getElementById("signatures").innerHTML += v.SignNameLatin;
            }
                
            if(v.Occupation.length > 0)
                document.getElementById("signatures").innerHTML += ", " + v.Occupation;
            if(v.CityCountry.length > 0)
                document.getElementById("signatures").innerHTML += ", " + v.CityCountry;
            });
        }
    }

    //Get messages - labels for Sign form input firlds, etc. - in the target language
    var ajax = new XMLHttpRequest();
    var method = "GET";
    var url = "getMsgs.php?lang=" + selectedLanguage;
    var asynchronous=true;
    ajax.open(method,url,asynchronous);
    ajax.send();
    ajax.onreadystatechange = function(){
    if(this.readyState == 4 && this.status == 200){
        var data = JSON.parse( this.responseText);
        let obj = {};
        data.forEach((v) => {
            let key = v.MsgName;
            let value = v.MsgText;
            obj[key] = value;
        });

        document.getElementById("sign-btn-top").innerHTML = obj["sign-manifest"];
        document.getElementById("sign-btn-bottom").innerHTML = obj["sign-manifest"];

        document.getElementById("sign-popup-title").innerHTML = obj["sign-prompt"];
        document.getElementById("lbl-sign-email").innerHTML = obj["lbl-sign-email"];
        document.getElementById("lbl-sign-name").innerHTML = obj["lbl-sign-name"];
        document.getElementById("lbl-sign-name-latin").innerHTML = obj["lbl-sign-name-latin"];
        document.getElementById("lbl-sign-occupation").innerHTML = obj["lbl-sign-occupation"];
        document.getElementById("lbl-sign-city-country").innerHTML = obj["lbl-sign-city-country"];

        document.getElementById("thank-you-for-signing").innerHTML = obj["thank-you-for-signing"];
        
        
        document.getElementById("submit-sign-btn").innerHTML = obj["submit-sign-btn"];
        document.getElementById("cancel-sign-btn").innerHTML = obj["cancel-sign-btn"];
 
        }
     }

    //Hide language selection popup
    const langPopup = document.getElementById('language-popup');
    langPopup.style.display = 'none';

    //Show main content
    const mainContent = document.getElementById('main-content');
    mainContent.style.display = 'flex'; 

    //Show [SIGN] button if not coming form the sign form
    if(IfLangSelect == 0){
        DisplayThankYou = "flex";
        DisplaySignBtns = "none";
    } else {
        DisplayThankYou = "none";
        DisplaySignBtns = "flex";
    }
    signBtn = document.getElementById('sign-btn-top');
    signBtn.style.display = DisplaySignBtns; 
    signBtn = document.getElementById('sign-btn-bottom');
    signBtn.style.display = DisplaySignBtns; 
    msgThankYou = document.getElementById('thank-you-for-signing');
    msgThankYou.style.display = DisplayThankYou;

    //update lang code on the manifest sign form
    document.getElementById("hdn-sign-lang-code").value = selectedLanguage;

    // Update the website content based on the selected language
    //updateContent();

} //(loadManifest)



function showSignForm(){
    signPopup = document.getElementById('sign-popup');
    signPopup.style.display = 'flex';
}



function hideSignForm(){
    signPopup = document.getElementById('sign-popup');
    signPopup.style.display = 'none';
}



function hideSignReviewForm(){
    signPopup = document.getElementById('review-sign-popup');
    signPopup.style.display = 'none';
}



function showSignReviewForm(){
    signPopup = document.getElementById('review-sign-popup');
    signPopup.style.display = 'flex';
}



function reviewSignature(ID, Name, arrSign){

    document.getElementById('review-sign-popup-title').innerHTML = Name;

    document.getElementById('signed-on-datetime').innerHTML = "Signed on " + arrSign.DateTimeStamp;

    document.getElementById('sign-id').value = arrSign.ID; //assign Sign ID

    //document.getElementById('sign-datetime').value = arrSign.DateTimeStamp;
    document.getElementById('sign-email').value = arrSign.Email;
    document.getElementById('sign-name').value = arrSign.SignName;
    document.getElementById('sign-name-latin').value = arrSign.SignNameLatin;
    document.getElementById('sign-city-country').value = arrSign.CityCountry;

    //Occupations
    
    //Get and assign 
    var ajax = new XMLHttpRequest();
    var method = "GET";
    var url = "getOccupation.php?id=" + arrSign.ID;
    var asynchronous=true;
    ajax.open(method,url,asynchronous);
    ajax.send();

    ajax.onreadystatechange = function(){
        if(this.readyState == 4 && this.status == 200){
            var data = JSON.parse( this.responseText);
            data.forEach((v) => {
                e = document.getElementById("sign-occupation-" + v.LangCode);
                if(e) {
                    e.value = v.Occupation;
                    var l = document.getElementById("lbl-sign-occupation-" + v.LangCode);
                    if(l){
                        l.innerHTML = v.LangName;
                        //alert(v.LangCode + "|" + arrSign.LangCode);
                        if(v.LangCode == arrSign.LangCode){
                            l.innerHTML += " (source language)";
                        }
                    }
                }
            });
        }
    }

    showSignReviewForm();

} //(reviewSignature)



//Obtain translations for Occupation from Google Translate API
//assign to input boxes on Signature Review pop-up
function translateOccupartion(){

    ldr = document.getElementById('transl_loader');
    //ldr.style.display = 'flex';

    var arrTargetLangs = [];

    //get all non-source-language fields
    var tags = document.querySelectorAll('*[id^="sign-occupation-"]');
    tags.forEach((e) => {
        //check if not course
        if(document.getElementById("lbl-" + e.id).innerHTML.toLowerCase().includes("source")){
            sourceLang = e.id.split("-").pop();
            sourceOcc = e.value;
        } else {
            arrTargetLangs.push(e.id.split("-").pop());
        }
    });

    //For each target language, get translations and assign to Occulation boxes
    numLangs = arrTargetLangs.length;
    numLangsFilled = 0;
    arrTargetLangs.forEach((t) => {

        var ajax = new XMLHttpRequest();
        var method = "GET";
        //var url = "getTranslation.php?s=" + sourceLang + "&t=" + t + "&text=" + sourceOcc;
        var url = "getTranslation.php?s=" + sourceLang + "&t=" + t + "&text=" + sourceOcc;
        var asynchronous=true;
        ajax.open(method,url,asynchronous);
        ajax.send();

        ajax.onreadystatechange = function(){

    //alert(url + "\n\n" + "this.readyState="+ this.readyState + "\n\n" + "this.readyState="+ this.status);

    //alert(this.readyState + "||" + this.status);

            if(this.readyState == 4 && this.status == 200){

                ldr.style.display = 'flex';

                //var data = JSON.parse( this.responseText);
                var lang_val = this.responseText.replace(/['"]+/g, '');
                document.getElementById("sign-occupation-" + t).value = lang_val;
                document.getElementById("sign-occupation-" + t).style.backgroundColor = "#99DBF7#";

                numLangsFilled++;
                if(numLangs == numLangsFilled)
                    ldr.style.display = 'none';
                else
                    ldr.style.display = 'flex';
            }
        }

    });

    //Check if needs ot close wait loader
    if(numLangs == numLangsFilled)
        ldr.style.display = 'none';
    else
        ldr.style.display = 'flex';


} //(translateOccupartion)



//for index.php, Attach event listener for the "Accept" button when the document is ready
if(document.getElementById('manifest-body')){

document.addEventListener('DOMContentLoaded', function () {

    const yearElement = document.getElementById('current-year')
    yearElement.innerHTML = new Date().getFullYear();
    
    //Hide sign popup
    const signPopup = document.getElementById('sign-popup');
    signPopup.style.display = 'none';

    //const acceptButton = document.getElementById('accept-btn');
    //acceptButton.addEventListener('click', acceptLanguage); // Add event listener to the "Accept" button

    const acceptButton = document.getElementById('accept-btn');
    acceptButton.addEventListener('click', loadManifest); // Add event listener to the "Accept" button

    //creditsBtn.addEventListener('click', openCredits)
    //creditsOverlay.addEventListener('click', closeCredits)

    //Show language selection popup
    //if not after sign form submission
    IfDisplayLangSelect = document.getElementById('hdn-if-display-lang-select').value;
    if(IfDisplayLangSelect == 1) {
        LangPopupDisplay = 'flex';
    } else {
        LangPopupDisplay = 'none';
        loadManifest();
    }

    const languagePopup = document.getElementById('language-popup');
    languagePopup.style.display = LangPopupDisplay;

});

} //if index.php