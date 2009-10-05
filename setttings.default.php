<?php 
/*
 * 
 * CONFIGURATION:
 * 
 Simply passing a form to this file is enough to properly send an email, 
 however certain fields must be present in the form in order for it to 
 work. The fields must be spelled and have the same exact casing as
 shown below. Optional fields add further configuration options. Currently
 the form only uses the $_POST method, however a simple tweak when calling
 new FormMailer() can fix that if you wish to use $_GET.
 
/**
 * Required:
 */
 
// A list of recipients you would like to recieve the email
$recipients = array('email@example.com', '"Bob Saget" <bob@saget.com>');
 
 
//<input type="text" name="email" value="" />
 // Reply to address of the email (ie: "charles@schwabb.net")
 
/**
 * Optional:
 */
 
// Subject of email. You can also use a passed input instead.
$subject = 'New Form Emailer Message'; // $_POST['subject'];
 
// Continuation path after form is successfully sent (ie: "/index.php" or "http://success.com")
$redirect = false;

// Integer value for # of seconds to wait before automatically redirecting (ie: "0" or "5")
// If this value is set to 0, the page will be immediately redirected upon success
$autoRedirect = false; 

$template = 'form_emailer_template.php'; // path to template file
 
 
 
 
 /**
 * 
 * !!! DO NOT EDIT BELOW THIS LINE !!!
 * (unless you know what you're doing)
 * 
 */
 
include('form_emailer.php');
 
if (!empty($_POST)){
        $formMailer = new FormMailer($_POST, true, $template);
        if (isset($_GET['debug'])) {
                echo $formMailer->message;
                echo '<pre>';
                print_r($formMailer->data);
        }
} else {
        echo 'Please pass a form to this page';
}