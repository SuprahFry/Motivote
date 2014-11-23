					<div id="content">
						<div class="informationlight">When creating themes, or customizing the default theme, please clone the original theme into a new, uniquely named directory to avoid being overridden when you update Motivote.</div>
						<div class="datagrid" style="max-width: 500px; display: inline-block;">
							<form action="edit.php?action=update&target=settings" method="get">
								<table>
									<thead>
										<tr>
											<th colspan="2">Selected Theme</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td width="50%">Value</td>
											<td width="50%">
												<select name="value">
												<?php
												$files = glob("../templates/*", GLOB_BRACE);
												//print_r($files);
												
												foreach ($files as $file) {
													$f = pathinfo($file, PATHINFO_BASENAME);
													
													if ($f != 'index.html') {
														echo('<option value="'.$f.'" '.(mv_setting('selected_theme') == $f ? 'selected' : '').'>'.$f.'</option>');
													}
												}
												?>
												</select>
											</td>
										</tr>
									</tbody>
									<tfoot>
										<tr>
											<td class="rright" colspan="2">
												<input type="hidden" name="name" value="selected_theme" />
												<a href="#" class="button savetable"><span>Save</span></a>
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
					</script>