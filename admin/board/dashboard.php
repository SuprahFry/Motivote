					<div id="content">
						<script type="text/javascript">
							displayVersionNotice();
						</script>
						<div id="versionnotice"></div>
						<div class="datagrid" style="width: 500px; display: block; margin: 0 auto;">
							<table>
								<thead>
									<tr>
										<th colspan="4">Common Callback IPs</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$uni = mv_unique_callbacks();
									$alt = false;
									
									foreach ($uni as $row) {
										?>
										<tr <?php echo($alt ? 'class="alt"' : ''); ?>>
											<td colspan="2"><?php echo($row['callbackip']); ?></td>
											<td colspan="2"><?php echo($row['COUNT(*)']); ?></td>
										</tr>
										<?php
										$alt = !$alt;
									}
									?>
								</tbody>
							</table>
						</div>
						<br />
						<div class="datagrid" style="width: 500px; display: block; margin: 0 auto;">
							<table>
								<thead>
									<tr>
										<th colspan="4">Important Information</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td colspan="2">Vote URL</td>
										<td colspan="2"><input type="text" style="display: block;width: 100%" value="<?php echo('http://'.$_SERVER["SERVER_NAME"].BASE_DIR.'/'); ?>" /></td>
									</tr>
									<tr class="alt">
										<td colspan="2">Callback URL</td>
										<td colspan="2"><input type="text" style="display: block;width: 100%" value="<?php echo('http://'.$_SERVER["SERVER_NAME"].BASE_DIR.'/callback.php?i='); ?>" /></td>
									</tr>
									<tr>
										<td>Curent Version</td>
										<td style="text-align: center;" class="vers"></td>
										<td>Installed Version</td>
										<td style="text-align: center;"><?php echo(MVERNUM); ?></td>
									</tr>
								</tbody>
							</table>
						</div>
						<p style="text-align: center;">Please submit any bugs you may find and they will be resolved immediately. Thanks for using Motivote!</p>
					</div>