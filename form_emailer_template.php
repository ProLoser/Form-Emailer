<table width="100%">
<tr><th width="25%">Field</th><th>Value</th></tr>
<?php foreach ($this->data as $field=>$value) { ?>
		
	<tr><td><?php echo $field; ?>:</td><td><?php echo $value; ?>&nbsp;</td></tr>
		
<?php } ?>
</table>