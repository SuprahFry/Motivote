<?php
$_SESSION['step'] = 1;
?>
<div id="input">
	<input type="text" id="userfield" name="username" value="Username" />
</div>
<script type="text/javascript">
	$('#userfield').focus(function() {
		if ($(this).val() == 'Username') {
			$(this).val('');
		}
	}).focusout(function() {
		if ($(this).val() == '') {
			$(this).val('Username');
		}
	});
	
	workStill = false;
	$(function() {
		$('#continue').text('Continue').off().removeClass('disabled');
		$('#back').hide().off();
		warn('');
		step('1', 'Enter username');
		$('#loadimg').hide();
		
		$('#userfield').keypress(function (e) {
			if (e.which == 13) {
				$('#continue').click();
				return false;
			}
		});
		$('#continue').click(function() {
			var user = $('#userfield').val();
			if (user == '' || user == 'Username') {
				warn('Please enter your username.');
				return false;
			}
			
			$('#loadimg').show();
			$.ajax({
				url: "index.php?ajax=step2&username=" + user,
				cache: false
			})
			.done(function(html) {
				$('#userfield').off();
				$('#links').html(html);
			});
			return false;
		});
	});
</script>