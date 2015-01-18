					<div id="content">
						<form action="tester.php" method="post">
							<?php
							if (!$mvrewardtac) {
								// per vote
								?>
								<div class="datagrid" style="max-width: 500px; display: block; margin: 0 auto;">
									<table>
										<thead>
											<tr>
												<th colspan="2">Insert a test vote</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td width="50%">Name</td>
												<td width="50%"><input type="text" name="name" value="" /></td>
											</tr>
											<tr class="alt">
												<td width="50%">IP</td>
												<td width="50%"><input type="text" name="ip" value="<?php echo($_SERVER['REMOTE_ADDR']); ?>" /></td>
											</tr>
											<tr>
												<td width="50%">Site</td>
												<td width="50%">
													<select name="site">
													<?php
													$results = mv_sites();
													
													foreach ($results as $res) {
														echo('<option value="'.$res['id'].'">'.$res['name'].'</option>');
													}
													?>
													</select>
												</td>
											</tr>
										</tbody>
										<tfoot>
											<tr>
												<td class="rright" colspan="2">
													<a href="#" class="button savetable"><span>Submit</span></a>
												</td>
											</tr>
										</tfoot>
									</table>
								</div>
								<?php
							}
							else {
								// reward
								?>
								<div class="datagrid" style="max-width: 500px; display: block; margin: 0 auto;">
									<table>
										<thead>
											<tr>
												<th colspan="2">Insert a test reward</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td width="50%">Name</td>
												<td width="50%"><input type="text" name="name" value="" /></td>
											</tr>
											<tr class="alt">
												<td width="50%">IP</td>
												<td width="50%"><input type="text" name="ip" value="<?php echo($_SERVER['REMOTE_ADDR']); ?>" /></td>
											</tr>
											<tr>
												<td width="50%">Reward</td>
												<td width="50%">
													<select name="reward">
													<?php
													$results = mv_incentives_all();
													
													foreach ($results as $res) {
														echo('<option value="'.$res['id'].'">'.$res['name'].'</option>');
													}
													?>
													</select>
												</td>
											</tr>
										</tbody>
										<tfoot>
											<tr>
												<td class="rright" colspan="2">
													<a href="#" class="button savetable"><span>Submit</span></a>
												</td>
											</tr>
										</tfoot>
									</table>
								</div>
								<?php
							}
							?>
						</form>
					</div>
					<script type="text/javascript">
						$('form').submit(function() {
							var f = $(this);
							$.ajax({
								type: f.attr('method'),
								cache: false,
								url: f.attr('action'),
								data: f.serialize(),
								success: function(data)
								{
									alert(data);
									animateLoadPage(currentURL);
								}
							});
							return false;
						});
						$('.savetable').click(function() {
							var d = $(this).closest('.datagrid');
							var f = d.parent('form'); // form
							f.submit();
							return false;
						});
					</script>