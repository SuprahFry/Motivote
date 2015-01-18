<?php
if (isset($_GET['username']) && $_SESSION['step'] == 1) {
	$_SESSION['user'] = ucwords(strtolower($_GET['username']));
}

$_SESSION['step'] = 2;
?>
<p>Voting as user "<strong><span class="user"><?php echo($_SESSION['user']); ?></span></strong>"</p>
<ul>
	<?php
	$sites = mv_vote_sites();
	//print_r($sites);
	
	foreach ($sites as $site) {
		echo('<li><a class="button site'.$site['id'].'" target="_blank" href="portal.php?site='.$site['id'].'"><span class="name">'.$site['name'].'</span><span class="time"></span></a></li>');
	}
	?>
</ul>
<script type="text/javascript">
	workStill = true;
	
	$(function() {
		<?php
		if (!$mvrewardtac) {
			echo("$('#continue').hide();");
		}
		?>
		$('#continue').addClass('disabled');
		$('#continue').off();
		$('#back').off().show();
		warn('');
		step('2', 'Vote on topsites');
		$('#loadimg').hide();
		if (!running) {
			running = true;
			(function countCheck() {
				$('#loadimg').show();
				$.getJSON('index.php?ajax=times', function(data) {
					if (data == 'Session invalid.') {
						window.location = 'index.php';
						return;
					}
					
					if (workStill)
					{
						for (var i = 0; i < data.length; i++) {
							//console.log(data[i]);
							updateCount(data[i], i);
						}
						
						if ($('li a.button').length == data.length) {
							warn('All sites have been voted for.');
						}
					}
				});
				$.ajax({
					url: 'index.php?ajax=step3continue', 
					success: function(data) {
						if (data == 'Session invalid.') {
							window.location = 'index.php';
							return;
						}
						if (workStill) {
							if (data == '0') {
								if ($('#continue').hasClass('disabled')) {
									$('#continue').removeClass('disabled');
								}
								
								$('#continue').text('Continue');
							}
							else
							{
								if (!$('#continue').hasClass('disabled')) {
									$('#continue').addClass('disabled');
								}
								
								$('#continue').text(parseInt(data) + ' votes left');
							}
						}
							
						$('#loadimg').hide();
					}
				});
				
				if (workStill) {
					setTimeout(countCheck, 5000);
				}
				else {
					running = false;
				}
			})();
			<?php if ($mvrewardtac): ?>
			(function worker() {
				if (workStill) {
					//$('#continue').text('Checking');
				}
				
			})();
			<?php endif; ?>
		}
		$('#back').click(function() {
			$('#loadimg').show();
			$.ajax({
				url: "index.php?ajax=step1",
				cache: false
			})
			.done(function(html) {
				$('#links').html(html);
			});
			return false;
		});
		$('#continue').click(function() {
			$('#loadimg').show();
			$.ajax({
				url: "index.php?ajax=step3",
				cache: false
			})
			.done(function(html) {
				$('#links').html(html);
			});
			return false;
		});
		
		function updateCount(data, index) {
			var d = data['callbackdate'].split(/[- :]/);
			d = Date.UTC(d[0], d[1]-1, d[2], d[3], d[4], d[5]);
			d = new Date(d);
			d.addHours(parseInt(data['waittime']));
			var link = $('.site' + data['site']);
			$(this).off();
			var step3 = false;
			
			<?php if ($mvrewardtac): ?>
			step3 = true;
			<?php endif; ?>
			
			if (data['fulfilled'] == '0' && data['outoftime'] == '0' && step3) {
				link.off('click hover');
				link.addClass('voted');
				link.click(function() {
					alert('You\'ve already voted previously on this list and your vote will be counted for a reward.');
					return false;
				});
			}
			else if (data['outoftime'] == '1') {
				link.children('.name').hide();
				
				if (!link.hasClass('disabled')) {
					link.click(function() {
						alert('You can\'t vote on this site right now. Try again later.');
						return false;
					});
					link.hover(function() {
						$(this).children('.name').show();
						$(this).children('.time').hide();
					}, function() {
						$(this).children('.name').hide();
						$(this).children('.time').show();
					});
					link.addClass('disabled');
				}
				
				link.countdown(d, function(event) {
					if (event.type == 'update') {
						$(this).children('.time').text(event.strftime('%H:%M:%S'));
					}
					else {
						$(this).off();
						$(this).children('.time').text('');
						$(this).children('.name').show();
						link.removeClass('disabled');
					}
				});
			}
		}
	});
</script>