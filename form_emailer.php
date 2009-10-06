<?php 
/**
 * Form Emailer 
 * 
 * @author Dean Sofer (ProLoser)
 * @version 0.29
 * @date October 6, 2009
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
 
<input type="hidden" name="spam" value="" />
 // Use this to check for spam bots. The class will check to see if this field has been filled in and not send an email.
 // Surprisingly effective.
 */
 
 
 
/**
  * Feel free to change how you execute the FormMailer class however you wish. 
  */ 
 
if (!empty($_POST)){
    $formMailer = new FormMailer(true);
    if (isset($_GET['debug'])) {
        echo $formMailer->message;
        echo '<pre>';
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
    
	function __construct($autoSend = false, $formData = false) {
			
        $this->data = ($formData == false) ? $this->parseData($_POST) : $this->parseData($formData);
        
        if ($autoSend == true) {
            if ($this->validateData() && $this->generateHeaders() && $this->generateMessage() && $this->sendEmail()) {
				echo $this->displaySuccess($this->redirect, $this->autoRedirect);
            } else {
            
                echo $this->displayError();
            }
        }
	}
    
    // Reformats data array for standardized usage across multiple methods and validation initialization
    function parseData($data = null) {
    	if ($data == null) {
    		$data = $this->data;
    	}
    	
    	foreach ($data as $field => $value) {
    		$result[strtoupper($field)] = $value;
    	}
    	
    	return $result;
    }
    
    
    // Loops through every single field in the data, checking for validation rules and applying them to each field
    // based on field naming conventions
    function validateData($formData = null) {
        if ($formData == null) $formData = $this->data;
        $valid = true;
        
        /*foreach ($formData as $field => $value) {
        	$rule = ($field);
        	if (method_exists($this, 'validate'.$rule) && !call_user_func(array($this, 'validate'.$rule))) {
        		$valid = false;
        		break;
        	}
        }*/
        
    	if (!$this->validateEmail($this->data['EMAIL']) || !empty($this->data['SPAM'])) {
    	
            $valid = false;
			$this->errors['validation'] = 'Please ensure that the email field was correctly filled out.';
            
        }

        return $valid;
    }
    
    function validateEmail($data) {
    	$emails = explode(',', $data);
    	$valid = true;
    	
    	foreach ($emails as $email) {
    		if (!preg_match("/^[a-z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+(?:[a-z]{2,4}|museum|travel)$/i", trim($email))) {
    			$valid = false;
    		}
    	}
    	
    	return $valid;
    }
    
    function validateEmpty($data) {
		if (empty($data)) {
			return false;
		}
    	return true;
    }
    
    function generateHeaders($preserveData = false) {

        if (empty($this->data['RECIPIENTS']) || empty($this->data['SUBJECT']) || empty($this->data['EMAIL'])) {
                $this->errors['headers'] = 'Please ensure that all required form inputs are present and spelled correctly.';
                return false;
        } else {
        
        	if (!$this->validateEmail($this->data['RECIPIENTS'])){
        		$this->errors['recipients'] = 'Please ensure that a hidden "recipients" field is populated with valid, comma delimitated, email addresses.';
        		return false;
        	}
        	                                	         
			if (empty($this->data['EMAIL']) || !isset($this->data['EMAIL'])) $this->data['EMAIL'] = 'empty@email.com';
			$this->recipients = $this->data['RECIPIENTS'];
            $this->subject = $this->data['SUBJECT'];
            $this->headers  = 'MIME-Version: 1.0' . "\r\n"
                . 'Content-type: text/html; charset=iso-8859-1' . "\r\n"
                . 'From: '.$this->data['EMAIL']."\r\n"
                . 'Reply-To: '.$this->data['EMAIL'];
			$this->redirect = $this->data['REDIRECT'];
			$this->autoRedirect = $this->data['AUTOREDIRECT'];
            
            if (!$preserveData) {
                unset($this->data['EMAIL']);
                unset($this->data['RECIPIENTS']);
                unset($this->data['SUBJECT']);
                unset($this->data['REDIRECT']);
                unset($this->data['AUTOREDIRECT']);
                unset($this->data['SPAM']);
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
        
        $return = $this->styles();
        
        $return .= '<h1 class="success">The email was sent.</h1>';
        if (!empty($redirect)) {
            $return .= '<div class="redirect"><a href="'.$redirect.'">Please click here to return to the form</a>';
                    
            if ($autoRedirect > 0) {
                $return .= " <span>or wait $autoRedirect seconds...</span>";
                $return .= "<meta http-equiv='refresh' content='$autoRedirect;URL=".$redirect."' />";
            }
            
            $return .= '</div>';
        } 
        
        return $return;
    }
    
    function displayError($autoRedirect = null) {
        $return = $this->styles();

        $return .= '<h1 class="error">There was an Error</h1>';

        foreach ($this->errors as $error) {
            $return .= '<h2>'.$error.'</h2>';
        }
        if (!empty($_SERVER['HTTP_REFERER']))
            $return .= '<div class="redirect"><a href="'.$_SERVER['HTTP_REFERER'].'">Please click here to return to the form</a></div>';
            
        return $return;
    }
    
    function styles() {
        return '<style type="text/css">
        .success{color:green;text-align:center;} .error{color:red;text-align:center;} .redirect{text-align:center;} .redirect span{display:block;}
        </style>';
    }
 
} ?>