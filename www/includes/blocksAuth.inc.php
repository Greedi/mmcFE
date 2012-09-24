<script type="text/javascript" src="js/highcharts.src.js"></script>
<script type="text/javascript" src="js/themes/mmcfe.js"></script>
<?php

$bitcoinController = new BitcoinClient($rpcType, $rpcUsername, $rpcPassword, $rpcHost);
$difficulty = round($bitcoinController->query("getdifficulty"));

function shares_per_block_new($count = 10) {
	global $difficulty, $settings;

        $numwonblocksQ = mysql_query("SELECT count(id) as count FROM winning_shares");
        $numwonblocksObj = mysql_fetch_object($numwonblocksQ);
        $avgsharesQ = mysql_query("SELECT sum(shareCount) / ".$numwonblocksObj->count." as avg FROM `winning_shares`");
        $avgsharesObj = mysql_fetch_object($avgsharesQ);
        $avgshares = $avgsharesObj->avg;

        $wonblocksQ = mysql_query("SELECT * FROM (SELECT blockNumber FROM `winning_shares` ORDER BY blockNumber DESC LIMIT ".$count.")b ORDER BY blockNumber ASC");
        $wonsharecountQ = mysql_query("SELECT * FROM (SELECT blockNumber, shareCount FROM `winning_shares` ORDER BY blockNumber DESC LIMIT ".$count.")s ORDER BY blockNumber ASC");
	$difficultyQ = mysql_query("SELECT * FROM (SELECT blockNumber, round(difficulty) AS difficulty FROM `networkBlocks` WHERE accountAddress != '' ORDER BY blockNumber DESC LIMIT ".$count.")nb ORDER BY blockNumber ASC");

	?>
		<script type="text/javascript">

			var chart;
			$(document).ready(function() {
				chart = new Highcharts.Chart({
					chart: {
						renderTo: 'chart',
						//defaultSeriesType: 'areaspline'
						defaultSeriesType: 'line',
						zoomType: 'y',
						height: 400,
						spacingRight: 35
					},
					title: {
						x: 32.5,
						text: 'Shares Per Block (actual, expected, average)'
					},
					subtitle: {
						x: 32.5,
						text: 'A visual representation of Mainframe variance'
					},
					xAxis: {
						categories: [

						<?php
					        while ($row = mysql_fetch_array($wonblocksQ, MYSQL_ASSOC)) {

							echo "'" .$row["blockNumber"]. "', ";

					        }
						?>

						],
						tickmarkPlacement: 'on',
						title: {
							enabled: false,
							text: 'Found Blocks'
						},
						labels: {
							enabled: 'false',
							rotation: -45,
							align: 'right',
							style: {
								 font: 'normal 8px Verdana, sans-serif',
								 display: 'none'
							}
						}
					},
					yAxis: {
						min: 0,
						title: {
							text: 'Number of Shares'
						},
						labels: {
							formatter: function() {
								return this.value / 1000000 + 'M';
							}
						}
					},
					tooltip: {
						formatter: function() {
							return 'Block '+
								 this.x +': '+ Highcharts.numberFormat(this.y, 0, ',') +' shares';
						}
					},
					legend: {
						x: 32.5,
						enabled: true
					},
					plotOptions: {
						area: {
							stacking: 'normal',
							lineColor: '#666666',
							lineWidth: 1,
							marker: {
								lineWidth: 1,
								lineColor: '#666666'
							}
						}
					},
					series: [{
						type: 'spline',
						name: 'Actual Shares per Block',
						data: [

						<?php
						$blk_count = 1;
						$avg_arr = array();
					        while ($row = mysql_fetch_array($wonsharecountQ, MYSQL_ASSOC)) {
							echo "" .$row["shareCount"]. ", ";
							$total_shares += $row["shareCount"];
							array_push($avg_arr, ($total_shares / $blk_count));
							$blk_count++;
					        }
						?>

						]
					}, {
						type: 'line',
						name: 'Actual Difficulty',
						data: [

						<?php
					        while ($row = mysql_fetch_array($difficultyQ, MYSQL_ASSOC)) {
							echo "" .$row["difficulty"]. ", ";
					        }
						?>

						]
					}, {
						type: 'spline',
						name: 'Effective Difficulty',
						data: [

						<?php
						foreach ($avg_arr as $avg_spb) {
							echo "" .$avg_spb. ", ";
						}
						?>

						]
					}]
				});

			});
	</script>

	<?php
}

?>

