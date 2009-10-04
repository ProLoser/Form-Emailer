<?php 
/**
 * Form Emailer 
 * 
 * @author Dean Sofer (ProLoser)
 * @version 0.28
 * @date June 14, 2009
 * 
 */
 
 
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
 
 **
 * Required:
 *
<input type="hidden" name="recipients" value="" />
 // Comma-deliminated set of emails (ie: "bobsaget@email.com, joseph@smith.com")
 
<input type="hidden" name="subject" value="" />
 // User or programmer entered subject line for the email (ie: "Contact Us Email")
 
<input type="text" name="email" value="" />
 // Reply to address of the email (ie: "charles@schwabb.net")
 
 **
 * Optional:
 *
<input type="hidden" name="redirect" value="" />
 // Continuation path after form is successfully sent (ie: "/index.php" or "http://success.com")
 
<input type="hidden" name="autoRedirect" value="" />
 // Integer value for # of seconds to wait before automatically redirecting (ie: "0" or "5")
 // If this value is set to 0, the page will be immediately redirected upon success
 
 
 */
 
 
 
/**
  * Feel free to change how you execute the FormMailer class however you wish. 
  */ 
 
if (!empty($_POST)){
        $formMailer = new FormMailer($_POST, true);
        if (isset($_GET['debug'])) {
                echo $formMailer->message;
                echo '<pre>';
                print_r($formMailer->data);
        }
} else {
        echo 'Please pass a form to this page';
}
 
 
/**
 * 
 * !!! DO NOT EDIT BELOW THIS LINE !!!
 * (unless you know what you're doing)
 * 
 */
 
 
Class FormMailer {
 
        var $data;
        var $recipients;
        var $subject;
        var $message;
        var $headers;
		var $redirect;
		var $autoRedirect;
        var $errors = array();
        
        function __construct($formData = null, $autoSend = false) {
                $this->data = $formData;
                
                if ($autoSend = true) {
                        if ($this->validateData() && $this->generateHeaders() && $this->generateMessage() && $this->sendEmail())
								$this->displaySuccess($this->redirect, $this->autoRedirect);
                        else
                                $this->displayError();
                }
        }
        
        function validateData($formData = null) {
                $valid = false;
                if ($formData == null) $formData = $this->data;
                
            	if ($this->validateEmail($this->data['email']))
                        $valid = true;
                else 
                        $this->errors['validation'] = 'Please ensure that the email field was properly filled out correctly.';
                
                return $valid;
        }
        
        function validateEmail($data) {
        	$valid = true;
        	$emails = explode(',', $data);
        	foreach ($emails as $email) {
        		if (!preg_match("/^[a-z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+(?:[a-z]{2,4}|museum|travel)$/i", trim($email)))
        			$valid = false;
        	}
        	return $valid;
        }
        
        function generateheaders($preserveData = false) {

                if (empty($this->data['recipients']) || empty($this->data['subject']) || empty($this->data['email'])) {
                        $this->errors['headers'] = 'Please ensure that all required form inputs are present and spelled correctly.';
                        return false;
                } else {
                
                	if (!$this->validateEmail($this->data['recipients'])){
                		$this->errors['recipients'] = 'Please ensure that a hidden "recipients" field is populated with valid, comma delimitated, email addresses';
                		return false;
                	}
                	                                	         
					if (empty($this->data['email']) || !isset($this->data['email'])) $this->data['email'] = 'empty@email.com';
					$this->recipients = $this->data['recipients'];
	                $this->subject = $this->data['subject'];
	                $this->headers  = 'MIME-Version: 1.0' . "\r\n"
	                        . 'Content-type: text/html; charset=iso-8859-1' . "\r\n"
	                        . 'From: '.$this->data['email']."\r\n"
	                        . 'Reply-To: '.$this->data['email'];
					$this->redirect = $this->data['redirect'];
					$this->autoRedirect = $this->data['autoRedirect'];
	                
	                if (!$preserveData) {
	                        unset($this->data['email']);
	                        unset($this->data['recipients']);
	                        unset($this->data['subject']);
	                        unset($this->data['redirect']);
	                        unset($this->data['autoRedirect']);
	                }
	                
	                return true;
                
                }
        }
        
        function generateMessage($template = 'form_emailer_template.php') {
                try {
                        ob_start();
                        include($template);
                        $this->message = ob_get_clean();
                        return true;    
                } catch (Exception $e) {
                        $this->errors['message'] = 'There was an issue generating the message from the template file.';
                        return false;
                }
        }
        
        function sendEmail() {
                if (mail($this->recipients, $this->subject, $this->message, $this->headers))
                        return true;
                else {
                        $this->errors['email'] = 'The email failed to send';
                        return false;
                }
                
        }
        
        function displaySuccess($redirect = null, $autoRedirect = null) {
                if ($autoRedirect == 0 && !empty($redirect)) {
                        header('Location: '.$redirect);
                        exit();
                }
                
                echo $this->styles();
                
                echo '<h1 class="success">The email was sent.</h1>';
                if (!empty($redirect)) {
                        echo '<div class="redirect"><a href="'.$redirect.'">Please click here to return to the form</a>';
                                
                        if ($autoRedirect > 0)
                                echo " <span>or wait $autoRedirect seconds...</span>";
                                echo "<meta http-equiv='refresh' content='$autoRedirect;URL=".$redirect."' />";
                        
                        echo '</div>';
                }
                
        }
        
        function displayError($autoRedirect = null) {
                echo $this->styles();
        
                echo '<h1 class="error">There was an Error</h1>';
 
                foreach ($this->errors as $error) {
                        echo '<h2>'.$error.'</h2>';
                }
                if (!empty($_SERVER['HTTP_REFERER']))
                        echo '<div class="redirect"><a href="'.$_SERVER['HTTP_REFERER'].'">Please click here to return to the form</a></div>';
        }
        
        function styles() {
                return '<style type="text/css">
                .success{color:green;text-align:center;} .error{color:red;text-align:center;} .redirect{text-align:center;} .redirect span{display:block;}
                </style>';
        }
 
} ?>