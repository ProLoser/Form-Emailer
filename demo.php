<style type="text/css">
label {float: left; text-align: right; width: 100px;}
input, textarea {margin-left: 10px;}
input {width: 400px;}
textarea {width: 400px; height: 100px;}
form div {clear: left; margin: 10px; overflow: auto;}
form div div {float: left; clear: none; margin: 0;}
form div div input {width: 145px;}
.required{color:red}
.submit input{width: auto; margin-left: 110px;}
</style>
<form method="post" action="settings.php">	
	<input name="spam" type="hidden" />
	<div><div><label>Name:</label> <input name="Name" type="text" /></div>
	<div><label>Email:</label> <input name="Email" type="text" /></div></div>
	<div><label>Subject:</label> <input type="text" name="subject" /></div>
	<div><label>Message:</label> <textarea name="message"></textarea></div>
	<div class="submit"><input id="Submit" type="submit" value="        Submit       " /></div>
</form>