<h1>Course Edit</h1>

<select multiple size='3'>
<option>Test</option>
<option>Test</option>
<option>Test</option>
<option>Test</option>
<option>Test</option>
<?php /*
foreach($query->result_array() as $row)
	echo "<option> $row </option>";
*/ ?>
</select>
<br \>
<button onclick="location.href='<?php echo site_url('Mainpage/index'); ?>'">Save</button>
<button onclick="location.href='<?php echo site_url('Mainpage/index'); ?>'">Cancel</button>
