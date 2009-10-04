<style type="text/css">
input {display:block;}
.required{color:red}
</style>
<form method="post" action="form_emailer.php?debug">	
	Name:
	<input name="Name" id="Name" type="text" maxlength="30" size="30" />
	Address:
	<input name="Address" id="Address" type="text" maxlength="50" size="50" />
	City:
	<input name="City" id="City" type="text" maxlength="30" size="30" />
	Zip/Postal:
	<input name="PostalCode" id="PostalCode" type="text" maxlength="10" size="10" />
	<SPAN class="required">*</SPAN>Email:
	<input name="email" id="EmailAddress" type="text" maxlength="60" size="50" />
	<SPAN class="required">*</SPAN>Recipients:
	<input type="text" name="recipients" value="deansofer@gmail.com" />
	<SPAN class="required">*</SPAN>Subject:
	<input type="text" name="subject" value="Contact Us" />
	Redirect:
	<input type="text" name="redirect" value="." />
	AutoRedirect:
	<input type="text" name="autoRedirect" value="30" />
	<input id="Submit" type="submit" value="   Submit Form   " />
</form>