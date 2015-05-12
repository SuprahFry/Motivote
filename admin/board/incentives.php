					<div id="content">
						<?php
						if (!$mvrewardtac) {
							echo('<div class="informationlight">Your incentive tactic is not set to "reward", so users will not be prompted for their selection.</div>');
						}
						
						$results = mv_incentives_all();
						$alt = false;
						$token = NoCSRF::generate('stoken');
						
						foreach ($results as $result) {
						?>
						<div class="datagrid" style="max-width: 500px; display: inline-block;">
							<form action="edit.php?action=update&target=incentives&stoken=<?php echo($token); ?>" method="post">
								<table>
									<thead>
										<tr>
											<th colspan="2"><?php echo($result['name']); ?></th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td width="50%">Name</td>
											<td width="50%"><input type="text" name="name" value="<?php echo($result['name']); ?>" /></td>
										</tr>
										<tr class="alt">
											<td width="50%">Amount</td>
											<td width="50%"><input type="text" name="amount" value="<?php echo($result['amount']); ?>" /></td>
										</tr>
										<tr>
											<td width="50%">Image</td>
											<td width="50%">
												<select name="image">
												<?php
												$files = glob("../images/*.{jpg,gif,png}", GLOB_BRACE);
												
												foreach ($files as $file) {
													$f = pathinfo($file, PATHINFO_BASENAME);
													echo('<option value="'.$f.'" '.($result['image'] == $f ? 'selected' : '').'>'.$f.'</option>');
												}
												?>
												</select>
											</td>
										</tr>
										<tr class="alt">
											<td width="50%">Active</td>
											<td width="50%"><input type="checkbox" name="active" <?php echo($result['active'] == '1' ? 'checked' : ''); ?> /></td>
										</tr>
									</tbody>
									<tfoot>
										<tr>
											<td class="rright" colspan="2">
												<input type="hidden" name="id" value="<?php echo($result['id']); ?>" />
												<a href="#" class="button deletetable"><span>Delete</span></a>
												<a href="#" class="button savetable"><span>Save</span></a>
											</td>
										</tr>
									</tfoot>
								</table>
							</form>
						</div>
						<?php
						}
						?>
						<div class="datagrid" style="max-width: 500px; display: inline-block;">
							<form action="edit.php?action=create&target=incentives&stoken=<?php echo($token); ?>" method="post">
								<table>
									<thead>
										<tr>
											<th colspan="2">Add new incentive</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td width="50%">Name</td>
											<td width="50%"><input type="text" name="name" /></td>
										</tr>
										<tr class="alt">
											<td width="50%">Amount</td>
											<td width="50%"><input type="text" name="amount" /></td>
										</tr>
										<tr>
											<td width="50%">Image</td>
											<td width="50%">
												<select name="image">
												<?php
												$files = glob("../images/*.{jpg,gif,png}", GLOB_BRACE);
												
												foreach ($files as $file) {
													$f = pathinfo($file, PATHINFO_BASENAME);
													echo('<option value="'.$f.'">'.$f.'</option>');
												}
												?>
												</select>
											</td>
										</tr>
										<tr class="alt">
											<td width="50%">Active</td>
											<td width="50%"><input type="checkbox" name="active" checked /></td>
										</tr>
									</tbody>
									<tfoot>
										<tr>
											<td class="rright" colspan="2">
												<input type="hidden" name="id" value="<?php echo($result['id']); ?>" />
												<a href="#" class="button savetable"><span>Add</span></a>
											</td>
										</tr>
									</tfoot>
								</table>
							</form>
						</div>
					</div>
					<script type="text/javascript">
						$('.datagrid form').submit(function() {
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
							var f = d.children('form'); // form
							f.submit();
							return false;
						});
						$('.deletetable').click(function() {
							if (confirm('Are you sure you want to delete this site?')) {
								var d = $(this).closest('.datagrid');
								var f = d.children('form'); // form
								$.ajax({
									type: f.attr('method'),
									cache: false,
									url: 'edit.php?action=delete&target=incentives',
									data: f.serialize(),
									success: function(data)
									{
										animateLoadPage(currentURL);
									}
								});
							}
							
							return false;
						});
					</script>