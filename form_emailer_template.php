<table width="100%">
<tr><th width="25%">Field</th><th width="75%">Value</th></tr>
<?php foreach ($this->data as $field=>$value) { ?>
	<?php if (!empty($value)) { ?>
		
		<tr><td><?php echo $field; ?>:</td> 		<td><?php echo $value; ?></td></tr>
	<?php } ?>
<?php } ?>
</table>