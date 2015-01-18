<!DOCTYPE html>
<html>
	<head>
		<title><?php echo(mv_phrase('title', mv_setting('server_name'), 'Admin Control Panel')); ?></title>
		<meta http-equiv="content-type" content="text/html;charset=utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />  
		<link href="css/minicolors.css" rel="stylesheet" type="text/css" />
		<link href="css/screen.css" media="screen, projection" rel="stylesheet" type="text/css" />
		<link href="css/icons.css" rel="stylesheet" type="text/css" />
		<link href="css/print.css" media="print" rel="stylesheet" type="text/css" />
		<!--[if IE]>
			<link href="css/ie.css" media="screen, projection" rel="stylesheet" type="text/css" />
		<![endif]-->
		<script type="text/javascript">
		  WebFontConfig = {
			google: { families: [ 'Droid+Serif:700:latin', 'Droid+Serif:900:latin' ] }
		  };
		  (function() {
			var wf = document.createElement('script');
			wf.src = ('https:' == document.location.protocol ? 'https' : 'http') +
			  '://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
			wf.type = 'text/javascript';
			wf.async = 'true';
			var s = document.getElementsByTagName('script')[0];
			s.parentNode.insertBefore(wf, s);
		  })();
		</script>
		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		<script type="text/javascript" src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
		<?php if (!isset($nointeract)): ?>
		<script type="text/javascript" src="js/minicolors.js"></script>
		<script type="text/javascript" src="js/cookie.js"></script>
		<script type="text/javascript" src="js/jquery.ba-bbq.min.js"></script>
		<script type="text/javascript">
			var currentURL = 'dashboard';
			var flowcook = $.cookie("loginflow", { raw: false });
			var init = true;
			var version = 0;
			var currentVersion = <?php echo(MVERNUM); ?>;
			var updateColors = function(hex, rgb) {
				$('#sidebar-outer').css('background-color', hex);
				$('#header').css('background-color', hex);
				$('.datagrid table thead th').css('background-color', hex);
				$('.datagrid .button').css('background-color', hex);
			}
			function updateColors() {
				updateColors(getColorCookie(), 0);
			}
			function getColorCookie() {
				var value = $.cookie("color", { raw: false });
				
				if (value == null) {
					value = rgb2hex($('#header').css('background-color'));
					setColorCookie(value);
				}
				
				return value;
			}
			
			function setColorCookie(value) {
				$.cookie("color", value, { expires: 365 });
			}
			
			function rgb2hex(rgb) {
				if (/^#[0-9A-F]{6}$/i.test(rgb)) return rgb;

				rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
				function hex(x) {
					return ("0" + parseInt(x).toString(16)).slice(-2);
				}
				return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
			}
			
			function animateLoadPage(url) {
				$('#content-container').animate({
					height: '0%',
					opacity: 0
				}, 250, function() {
					loadPage(url);
				});
			}
			
			function displayVersionNotice() {
				if (version == 0) {
					return;
				}
				
				var insert = '';
				
				if (version == -1) {
					insert = '<div class="information">Unable to check current version. Visit <a href="http://rspserver.com/motivote">http://rspserver.com/motivote</a> to make sure you\'re up to date.';
				}
				else if (version > currentVersion) {
					insert = '<div class="failure">New version available! Latest version: ' + version + '. <a href="http://rspserver.com/motivote">Click here to get the latest version</a></div>';
				}
				else {
					//insert = '<div class="informationlight">You have the latest version: ' + version + '</div>';
				}
				
				if (insert != '') {
					$('#versionnotice').html(insert);
				}
				$('.vers').text(version);
			}
			
			function getCurrentVersion() {
				$.ajax({
					url: 'http://rspserver.com/ext/mvwebversion.txt',
					cache: false
				}).error(function() {
					version = -1;
					displayVersionNotice();
				}).done(function(data) {
					version = parseFloat(data);
					displayVersionNotice();
				});
			}
			
			function loadPage(url) {
				$.ajax({
					url: 'index.php?board=' + url,
					cache: false
				}).error(function(jqxhr, status, error) {
					$('#ajaxcontent').html('<div id="content"><div class="failure">Error: ' + url + ' ' + error + '</div></div>');
					setCurrentSidebarSelection(url);
				
					if (flowcook && !init) {
						$('#content-container').animate({
							height: '100%',
							opacity: 1
						}, 250, function() { });
					}
					
					init = false;
				}).done(function(html) {
					$('#ajaxcontent').html(html);
					updateColors(getColorCookie(), 0);
					setCurrentSidebarSelection(url);
					
					if (flowcook == null) {
						flowcook = true;
						$('#content-container').animate({
							opacity: 1
						}, 500, function() { });
					}
					else if (!init) {
						$('#content-container').animate({
							height: '100%',
							opacity: 1
						}, 250, function() { });
					}
					
					init = false;
				});
			}
			
			function setCurrentSidebarSelection(url) {
				$('#sidebar-container li').removeClass('current');
				var v = $('#sidebar-container a[href$="' + url + '"]').parent().addClass('current');
			}
			
			$(document).ready(function() {
				updateColors(rgb2hex($('#header').css('background-color')), 0);
				$('.color-picker').miniColors({
					letterCase: 'uppercase',
					defaultValue: rgb2hex($('#header').css('background-color')),
					change: function(hex, rgb) {
						updateColors(hex, rgb);
						setColorCookie(hex);
					}
				});
				$('.color-picker').miniColors('value', getColorCookie());
				
				if (flowcook == null) {
					$.cookie("loginflow", true, { expires: 365 });
					
					$('#header').animate({
						top: '0px'
					}, 500, function() {
						$('#sidebar-outer').animate({
							left: '0px'
						}, 1000, function() { });
					});
				}
				else {
					$('#header').css('top', '0px');
					$('#sidebar-outer').css('left', '0px');
					$('#content-container').css('opacity', '1');
				}

				$(window).bind('hashchange', function(e) {
					currentURL = $.param.fragment();
					
					if (currentURL == null || currentURL == '') {
						currentURL = 'dashboard';
					}
					
					if (flowcook && !init) {
						animateLoadPage(currentURL);
					}
					else {
						loadPage(currentURL);
					}
				});
				
				$(window).trigger('hashchange');
				getCurrentVersion();
			});
		</script>
		<?php else: ?>
		<script type="text/javascript">
			$(document).ready(function() {
				$('#header').css('top', '0px');
				$('#sidebar-outer').css('left', '0px');
				$('#content-container').css('opacity', '1');
			});
		</script>
		<?php endif; ?>
	</head>
	<body>
		<div id="wrapper">
			<div id="header">
				<div id="header-background">
					<div id="logo">
						<a href="#dashboard">
							<img src="images/logo.png" alt="Electra" />
						</a>
						<div class="version">v<?php echo(MVERNUM); ?></div>
					</div>
					<?php if (!isset($nointeract)): ?>
					<div id="notifications">
						<div id="account">
							<div id="tools">
								<!--<form id="search" action="#" method="get">
									<input type="text" name="query" id="search-box" />
									<input type="submit" value="" id="search-submit" />
								</form>-->
								<input type="hidden" name="color1" class="color-picker" size="6" />
								<a id="logout" href="index.php?do=logout"></a>
							</div>
						</div>
					</div>
					<?php endif; ?>
				</div>
			</div>
			<div id="main">
				<?php
				if (!isset($nosidebar)) {
					include('sidebar.php');
				}
				?>
				<div id="content-container">