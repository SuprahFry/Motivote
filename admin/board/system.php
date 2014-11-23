					<div id="content">
						<!--<canvas id="cpugraph" width="91px" height="172px" style="background: #000000;"></canvas>-->
					</div><!-- #008000 #00ff00 -->
					<script type="text/javascript">
						var bw = 91;
						var bh = 172;
						var p = 0;
						var cw = bw + (p*2) + 1;
						var ch = bh + (p*2) + 1;

						var canvas = $('<canvas/>').attr({class: 'cpugraph', width: cw, height: ch}).appendTo('#content');
						var context = canvas.get(0).getContext("2d");
						
						function drawBoard(){
							for (var x = 0; x <= bw; x += 10) {
								context.moveTo(0.5 + x + p, p);
								context.lineTo(0.5 + x + p, bh + p);
							}


							for (var x = 0; x <= bh; x += 10) {
								context.moveTo(p, 0.5 + x + p);
								context.lineTo(bw + p, 0.5 + x + p);
							}

							context.strokeStyle = "#008000";
							context.stroke();
						}

						drawBoard();
					</script>
					<style>
						.cpugraph {
							background-color: rgba(0, 0, 0, 1);
						}
					</style>