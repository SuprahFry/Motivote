						<p>Before proceeding with the installation, the installer will check your website for possible problems you may encounter if you try to install Motivote.
						If all the tests pass, you know that you will not have any compatability issues with Motivote!
						If any tests fail, you will need to fix the issue before proceeding.
						You will know if a test fails to pass when it is highlighted red.</p>
						<h2>Required Versions</h2>
						<table class="testres">
							<?php
							$verpass = version_compare(PHP_VERSION, $cureq['version']['PHP'], '>=');
							$failedstep = $failedstep || $verpass == false;
							?>
							<tr class="<?php echo($verpass ? 'pass' : 'fail'); ?>">
								<td class="sucicon"><img src="<?php echo($verpass ? 'success' : 'error'); ?>.png" /></td>
								<td class="testitle">PHP</td>
								<td class="tesvalue"><?php echo(PHP_VERSION); ?></td>
							</tr>
						</table>
						<h2>Required PHP Features</h2>
						<table class="testres">
							<?php
							foreach ($cureq['feature'] as $check => $needed) {
								$valcheck = ini_get($check);
								$verpass = $valcheck == $needed;
								
								if ($needed == false) {
									$verpass = true;
								}
								
								$failedstep = $failedstep || $verpass == false;
							?>
							<tr class="<?php echo($verpass ? 'pass' : 'fail'); ?>">
								<td class="sucicon"><img src="<?php echo($verpass ? 'success' : 'error'); ?>.png" /></td>
								<td class="testitle"><?php echo($check); ?></td>
								<td class="tesvalue"><?php echo($verpass ? 'On' : 'Off'); ?></td>
							</tr>
							<?php } ?>
						</table>
						<h2>Required PHP Modules</h2>
						<table class="testres">
							<?php
							foreach ($cureq['modules'] as $check) {
								$verpass = extension_loaded($check);
								$failedstep = $failedstep || $verpass == false;
							?>
							<tr class="<?php echo($verpass ? 'pass' : 'fail'); ?>">
								<td class="sucicon"><img src="<?php echo($verpass ? 'success' : 'error'); ?>.png" /></td>
								<td class="testitle"><?php echo($check); ?></td>
								<td class="tesvalue"><?php echo($verpass ? 'On' : 'Off'); ?></td>
							</tr>
							<?php } ?>
						</table>
						<h2>Files and Folders with Write Access</h2>
						<table class="testres">
							<?php
							foreach ($cureq['writable'] as $check) {
								$verpass = is_writable('../'.$check);
								$failedstep = $failedstep || $verpass == false;
							?>
							<tr class="<?php echo($verpass ? 'pass' : 'fail'); ?>">
								<td class="sucicon"><img src="<?php echo($verpass ? 'success' : 'error'); ?>.png" /></td>
								<td class="testitle"><?php echo($check); ?></td>
								<td class="tesvalue"><?php echo($verpass ? 'Writable' : 'Unwritable'); ?></td>
							</tr>
							<?php } ?>
						</table>
						<input type="hidden" name="failedstep" value="<?php echo($failedstep); ?>" />