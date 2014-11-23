<?php
$rewardvotes = mv_reward_votes();
$rewardentry = mv_reward();
$incentives = mv_incentives();

if ($rewardentry === false || $rewardentry === null) {
	if (count($rewardvotes) >= mv_active_site_count()) {
		$_SESSION['step'] = 3;
		
		$mvdb->escapedQuery("INSERT INTO `".DBPRE."rewards`
								(`user`, `ip`, `submitted`, `ready`, `fulfilled`, `incentive`)
								VALUES ('%1:s', '%2:s', UTC_TIMESTAMP(), false, false, null)",
								$_SESSION['user'], $_SERVER['REMOTE_ADDR']);
		$insert = $mvdb->lastInsertID();
		
		if ($insert > 0) {
			foreach ($rewardvotes as $key => $value) {
				$mvdb->escapedQuery("UPDATE `".DBPRE."votes` SET `fulfilled` = 1 WHERE `id` = %1:d", $value['id']);
			}
		}
		else {
			die('ERROR');
		}
	}
	else {
		include('step2.php');
		die();
	}
}
else if (isset($_GET['reward'])) {
	foreach ($incentives as $k => $incentive) {
		if ($incentive['id'] == intval($_GET['reward'])) {
			unset($_GET['reward']);
			$mvdb->escapedQuery("UPDATE `".DBPRE."rewards` SET `ready` = 1, `incentive` = %1:d WHERE `id` = %2:d", $incentive['id'], $rewardentry['id']);
			echo('<div>You\'ve been rewarded with '.number_format($incentive['amount']).' '.$incentive['name'].'</div><br />');
			include('step2.php');
			die();
		}
	}
}
?>
<div id="input">
	<?php
	foreach ($incentives as $inc) {
		echo('<div class="incentive"><input type="radio" name="reward" value="'.$inc['id'].'"> <img src="images/'.$inc['image'].'" alt-"'.$inc['name'].'" /> x'.number_format($inc['amount']).'</div>');
	}
	?>
</div>
<?php //echo('<br /><br /><div style="font-size: 10px; text-align: center;">Auth ID: '.md5('test').'</div>'); ?>
<script type="text/javascript">
	workStill = false;
	$(function() {
		$('#continue').removeClass('disabled').text('Claim').off();
		$('#back').hide().off();
		warn('');
		step('3', 'Select reward');
		$('#loadimg').hide();
		
		$('#continue').click(function() {
			var reward = $('.incentive input[name="reward"]:checked').val();
			
			if (reward == null || reward == '') {
				warn('Please select your reward.');
				return false;
			}
			
			$('#loadimg').show();
			$.ajax({
				url: "index.php?ajax=step3&reward=" + reward,
				cache: false
			})
			.done(function(html) {
				$('#links').html(html);
			});
			return false;
		});
	});
</script>