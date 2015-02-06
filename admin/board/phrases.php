					<div id="content">
						<div class="datagrid">
							<table>
								<thead>
									<tr>
										<th>Key</th>
										<th>
											Value
											<span style="float: right; font-size: 11px; font-style: italic;">Double click a value to edit!</span>
										</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$settings = mv_phrases();
									$alt = false;
									
									foreach ($settings as $setting) {
									?>
									<tr class="<?php echo($alt ? 'alt' : ''); ?>">
										<td class="editablename"><?php echo($setting['name']); ?></td>
										<td class="editable"><?php echo($setting['value']); ?></td>
									</tr>
									<?php
										$alt = !$alt;
									}
									?>
								</tbody>
								<!--<tfoot>
									<tr>
										<td class="rright" colspan="3">
											Key: <input type="text" />
											<a href="#" class="button"><span>Create</span></a>
										</td>
									</tr>
								</tfoot>-->
							</table>
						</div>
					</div>
					<script type="text/javascript">
						<?php
						$token = NoCSRF::generate('stoken');
						?>
						var token = '<?php echo($token); ?>';
						var dbcl = function() {
							$(this).off();
							$(this).html('<input type="text" value="' + $(this).text() + '" />');
							$(this).children('input').focus().blur(function() {
								var v = $(this).val();
								var n = $(this).parent().parent().children('.editablename').text();
								$(this).parent().html(v).dblclick(dbcl);
								$(this).off();
								
								$.ajax({
									url: "edit.php?action=update&target=phrases&stoken=" + token + "&name=" + n + "&value=" + v,
									cache: false
								})
								.done(function(html) {
									//alert(html);
									token = html;
								});
							});
						}
						$('.editable').dblclick(dbcl);
					</script>