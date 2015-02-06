					<div id="content">
						<div class="datagrid">
							<form action="edit.php?action=update&target=settings&stoken=<?php echo(NoCSRF::generate('stoken')); ?>" id="settings" name="settings" method="post">
							<table>
								<thead>
									<tr>
										<th>Key</th>
										<th>
											Value
										</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$settings = mv_settings_visible();
									$alt = false;
									
									foreach ($settings as $setting) {
									?>
									<tr class="<?php echo($alt ? 'alt' : ''); ?>">
										<td class="editablename" style="width: 35%"><span style="font-weight: bold;"><?php echo($setting['nicename']); ?></span><br />
										<span style="font-size: 9px; font-style: italic;"><?php echo($setting['name']); ?></span><br />
										<?php echo(nl2br($setting['description'])); ?>
										</td>
										<td>
											<?php
											if ($setting['values'] == 's') {
												echo('<input type="text" name="'.$setting['name'].'" value="'.$setting['value'].'" />');
											}
											else if ($setting['values'] == 'md5') {
												echo('<input type="text" placeholder="Leave blank unless changing" name="'.$setting['name'].'" />');
											}
											else if (strpos($setting['values'], ',') !== false) {
												echo('<select name="'.$setting['name'].'">');
												$values = explode(',', $setting['values']);
												
												foreach ($values as $v) {
													$s = explode('|', $v);
													echo('<option value="'.$s[0].'" '.($setting['value'] == $s[0] ? 'selected' : '').'>'.$s[1].'</option>');
												}
												
												echo('</select');
											}
											else if ($setting['values'] == 'b') {
												echo('<select name="'.$setting['name'].'">');
												echo('<option value="0" '.($setting['value'] == 0 ? 'selected' : '').'>Off</option>');
												echo('<option value="1" '.($setting['value'] == 1 ? 'selected' : '').'>On</option>');
												echo('</select');
											}
											?>
										</td>
									</tr>
									<?php
										$alt = !$alt;
									}
									?>
								</tbody>
								<tfoot>
									<tr>
										<td class="center" colspan="2">
											<span class="subcontainer button"><input type="submit" value="Save" /></span>
										</td>
									</tr>
								</tfoot>
							</table>
							</form>
						</div>
					</div>
					<script type="text/javascript">
						$('#settings').submit(function(e) {
							$.ajax({
								type: $(this).attr('method'),
								url: $(this).attr('action'),
								data: $(this).serialize(),
								cache: false
							})
							.done(function(html) {
								animateLoadPage(currentURL);
							});
							e.preventDefault();
						});
					</script>