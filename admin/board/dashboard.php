					<div id="content">
						<script type="text/javascript">
							displayVersionNotice();
						</script>
						<div id="versionnotice"></div>
						<div class="datagrid" style="width: 500px; display: block; margin: 0 auto;">
							<table>
								<thead>
									<tr>
										<th colspan="2">Important Information</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>Vote URL</td>
										<td><input type="text" style="display: block;width: 100%" value="<?php echo('http://'.$_SERVER["SERVER_NAME"].BASE_DIR.'/'); ?>" /></td>
									</tr>
									<tr class="alt">
										<td>Callback URL</td>
										<td><input type="text" style="display: block;width: 100%" value="<?php echo('http://'.$_SERVER["SERVER_NAME"].BASE_DIR.'/callback.php?i='); ?>" /></td>
									</tr>
								</tbody>
							</table>
						</div>
						<p style="text-align: center;">Please submit any bugs you may find and they will be resolved immediately. Thanks for using Motivote!</p>
					</div>