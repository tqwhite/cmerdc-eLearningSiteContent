<?php
//----------------------------------------------------
// Google Form Sandwich
// Created by Jaz Witham (Jazzerup)
// 2011
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND
// Script allows you to write custom php code
// before submitting a form to google
//----------------------------------------------------
//Google Form Key
$formkey = "dGJCdUFNTkFWNkpoc1hsNmhLMzV4dmc6MQ";
//Email address of person who should get email notification of form submission
$toemail = "snorton@erdc.k12.mn.us";
$thankyou = "http://http://elearning.technology.<!rootDomainSegment!>/ThankYou/";
//Change this URL to your google form address
$googleformURL = "https://docs.google.com/a/gapps.erdc.k12.mn.us/spreadsheet/formResponse?formkey=$formkey";
//---------------------Recaptcha Plugin ------------------------
//This is where we call the Captcha plugin to verify
 require_once('recaptchalib.php');
  $privatekey = "6LeVsd0SAAAAAA8aU8ZY4o50-wrFVfVWPxDadtJ-";
  $resp = recaptcha_check_answer ($privatekey,
                                $_SERVER["REMOTE_ADDR"],
                                $_POST["recaptcha_challenge_field"],
                                $_POST["recaptcha_response_field"]);

  if (!$resp->is_valid) {
    // What happens when the CAPTCHA was entered incorrectly
    die ("The reCAPTCHA wasn't entered correctly. Go back and try it again." .
         "(reCAPTCHA said: " . $resp->error . ")");
  } else {
    // Your code here to handle a successful verification
  }

//-----------------Start send email script------------------------
//This is where you would put any custom scripting such as using php to send confirmation emails
$name = $_POST["entry_0_single"];  //Replace the periods in the field name with underscore.
$fromemail = $_POST["entry_3_single"];
$subject = $_POST["entry_2_single"];
$body = "Message:" . $_POST["entry_0_single"] . $_POST["entry_1_single"] . "From:" .$_POST["entry_2_single"]  . $_POST["entry_5_single"] . "Message has been sent\n";
$header = "From: " . $fromemail . "\r\n";
$header .= "Reply-To: " . $fromemail . "\r\n";
if (!(mail($toemail,$subject,$body,$header))) {
   echo("
<p>Message delivery failed...</p>
 
");
   echo("from email: $fromemail to email: $toemail");
  }
//-----------------End send email script------------------------
 
//----------------Send Form Fields to Google--------------------
//Loops through the form fields and creates a query string to submit to google
foreach ($_POST as $var => $value) {
 
    if ($var != "ignore") {
    $postdata=$postdata . urlencode(str_replace("_", "." , $var)) . "=" . $value . "&";
    }
}
//remove the extra comma
$postdata=substr($postdata,0,-1);
//Submit the form fields to google
$ch = curl_init();
curl_setopt ($ch, CURLOPT_URL,$googleformURL);
curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt ($ch, CURLOPT_POSTFIELDS, $postdata);
curl_setopt ($ch, CURLOPT_POST, 1);
$data = curl_exec ($ch);
curl_close($ch);
//echo $data;
//Redirect to your thank you page
header( "Location: $thankyou" ) ;
?>