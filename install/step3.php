<?php
$failedstep = true;
?>
<p>You're almost there! Just a few more questions, and you'll be ready to go with Motivote!</p>
<table class="testres">
	<tr>
		<td>Server Name</td>
		<td><input type="text" value="SuperScape" name="server_name" />
	</tr>
	<tr>
		<td>Incentive Tactic</td>
		<td>
			<select name="incentive_tactic">
				<option value="reward" selected>Reward for all votes</option>
				<option value="vote">Reward for each vote</option>
			</select>
		</td>
	</tr>
	<tr>
		<td>Admin Control Password (write this down!)</td>
		<td><input type="text" value="<?php echo(substr(md5(rand()), 0, 6)); ?>" name="admin_pass" /></td>
	</tr>
</table>