					<div id="content">
						<?php
						$results = mv_sites();
						$alt = false;
						$token = NoCSRF::generate('stoken');
						
						foreach ($results as $result) {
						?>
						<div class="datagrid" style="max-width: 500px; display: inline-block;">
							<form action="edit.php?action=update&target=sites&stoken=<?php echo($token); ?>" method="post">
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
											<td width="50%">Vote URL</td>
											<td width="50%"><input type="text" name="voteurl" value="<?php echo($result['voteurl']); ?>" /></td>
										</tr>
										<tr>
											<td width="50%">Vote URL ID</td>
											<td width="50%"><input type="text" name="voteurlid" value="<?php echo($result['voteurlid']); ?>" /></td>
										</tr>
										<tr class="alt">
											<td width="50%">Wait Time</td>
											<td width="50%"><input type="text" name="waittime" value="<?php echo($result['waittime']); ?>" /></td>
										</tr>
										<tr>
											<td width="50%">Active</td>
											<td width="50%">
												<?php if($result['id'] != 1): ?>
												<input type="checkbox" name="active" <?php echo($result['active'] == '1' ? 'checked' : ''); ?> />
												<?php else: ?>
												Can not disable.
												<?php endif; ?>
											</td>
										</tr>
									</tbody>
									<tfoot>
										<tr>
											<td class="rright" colspan="2">
												<input type="hidden" name="id" value="<?php echo($result['id']); ?>" />
												<?php if($result['id'] != 1): ?>
												<a href="#" class="button deletetable"><span>Delete</span></a>
												<?php endif; ?>
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
							<form action="edit.php?action=create&target=sites&stoken=<?php echo($token); ?>" method="post">
								<table>
									<thead>
										<tr>
											<th colspan="2">Add new site</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td width="50%">Name</td>
											<td width="50%"><input type="text" name="name" /></td>
										</tr>
										<tr class="alt">
											<td width="50%">Vote URL</td>
											<td width="50%"><input type="text" name="voteurl" /></td>
										</tr>
										<tr>
											<td width="50%">Vote URL ID</td>
											<td width="50%"><input type="text" name="voteurlid" /></td>
										</tr>
										<tr class="alt">
											<td width="50%">Wait Time</td>
											<td width="50%"><input type="text" name="waittime" /></td>
										</tr>
										<tr>
											<td width="50%">Active</td>
											<td width="50%"><input type="checkbox" name="active" checked /></td>
										</tr>
									</tbody>
									<tfoot>
										<tr>
											<td class="rright" colspan="2">
												<a href="#" class="button savetable"><span>Add</span></a>
											</td>
										</tr>
									</tfoot>
								</table>
							</form>
						</div>
						<div style="text-align: center; font-size: 11px;">{id} corresponds to the "Vote URL ID" field and {incentive} is automatically generated and inserted into the url. All sites require both of these to work.</div>
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
									//alert(data);
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
									url: 'edit.php?action=delete&target=sites',
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