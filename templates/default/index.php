<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo(mv_phrase('title', mv_setting('server_name'), 'Voting')); ?></title>
		<meta http-equiv="content-type" content="text/html;charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />  
		<link rel="stylesheet" type="text/css" href="<?php echo($mvthemedir); ?>/style.css" />
		<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
		<script src="<?php echo(mv_base_url().'/js/date.js'); ?>"></script>
		<script src="<?php echo(mv_base_url().'/js/jquery.countdown.min.js'); ?>"></script>
		<script type="text/javascript">
			$(window).load(function() {
				$('#loadimg').hide();
			});
			var running = false;
			var workStill = true;
			
			function step(number, description) {
				$('#stepnum').text('Step ' + number);
				$('#stepname').text(description);
			}
			function warn(message) {
				if (message == '') {
					$('.warning').html('');
					return;
				}
				
				$('.warning').html(message + '<br />');
				setTimeout(function() {
					$('.warning').html('');
				}, 5000);
			}
		</script>
	</head>
	<body>
		<div id="wrapper">
			<div id="content">
				<h1><?php echo(mv_phrase('header', mv_setting('server_name'))); ?></h1>
				<div id="step"><span id="stepnum">Step 1</span><span id="stepname">Enter Username</span></div>
				<div id="loadimg"><img src="<?php echo($mvthemedir); ?>/images/loading.gif" alt="Loading" /></div>
				<div id="main">
					<p>
						<strong><?php echo(mv_phrase('section_header')); ?></strong><br />
						<?php echo(mv_phrase('section_body')); ?>
					</p>
					<div id="links">
						<?php
						if (isset($_SESSION['step'])) {
							include($mvthemedir.'/step'.$_SESSION['step'].'.php');
						}
						else {
							include($mvthemedir.'/step1.php');
						}
						?>
					</div>
					<div id="foot">
						<div class="warning"></div>
						<a href="#" class="button" id="back">Restart</a> <a href="#" class="button" id="continue">Continue</a><br style="clear: both;" />
					</div>
				</div>
				<div id="rights"><?php echo(mv_phrase('powered_by', MVERNUM)); ?></div>
			</div>
		</div>
	</body>
</html>