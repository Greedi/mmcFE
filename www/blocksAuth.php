<?php

	include("includes/templates/header.php");
	include("includes/blocksAuth.inc.php");

	// number of graph items to show
	$count = 75;

	// check if logged in
	if( !$cookieValid ){
	        header("Location: /stats");
	        exit();
	}

$last_no_blocks_found = 50;
?>

<div class="block withsidebar">

        <div class="block_head">
                <div class="bheadl"></div>
                <div class="bheadr"></div>

		<h2>Welcome,
		<?php
		if($cookieValid) {

			echo $userInfo->username . " ";

                        $account_type = 0;
                        $account_type = account_type($userInfo->id);

                        if ($account_type == 9) {
                                $account_type = "<b>Early-Adopter</b>: <b>0%</b> Pool Fee";
                        } else {
                                $account_type = "<b>Active Account</b>: <b>" .$settings->getsetting("sitepercent"). "%</b> Pool Fee";
                        }

			echo "<font size='1px'>" .$account_type."</font> ";
			echo "<font size='1px'><i>(You are <a href='/osList'>donating</a> <b></i>" .antiXss($donatePercent)."%</b> <i>of your earnings)</i></font>";
		} else {
			echo "Guest";
		}
		?>
		</h2>
        </div>          <!-- .block_head ends -->

        <div class="block_content">

                <div class="sidebar">

                        <?php include ("includes/leftsidebar.php"); ?>

                </div>          <!-- .sidebar ends -->

                <div class="sidebar_content">

	                <div class="block" style="clear:none;">

	<!-- POOL LUCK BLOCK -->
	                <div class="block_head">
	                	<div class="bheadl"></div>
	                	<div class="bheadr"></div>
	                	<h2>Pool Luck <font size="1">over last <?php print $count; ?> blocks</font></h2>

	                </div>	<!-- .block_head ends -->

		                <div id="" class="block_content" style="">

					<div id="chart" style="">
						<?php shares_per_block_new($count); ?>
					</div>

					<center>
					<p style="padding-left:30px; padding-right:30px; font-size:10px;">
					The graph above illustrates N shares to find a block vs. N Shares expected to find a block based on
					difficulty assuming a zero variance scenario. Additionally our average number of shares per block is displayed (effective difficulty/pool luck).
					</p></center>

		                </div>          <!-- nested block content ends -->

	                <div class="bendl"></div>
	                <div class="bendr"></div>
	                </div>

	<!-- LAST N BLOCKS FOUND STATS BLOCK -->

	                <div class="block" style="clear:none;">
	                <div class="block_head">
	                <div class="bheadl"></div>
	                <div class="bheadr"></div>
				<h2>Last <?php echo $last_no_blocks_found. " Blocks Found"; ?></h2>
	                </div>

	              	<div class="block_content" style="padding-left:5px;padding-right:5px;">
			<p>
			<?php
			// SHOW LAST (=$last_no_blocks_found) BLOCKS
			echo "<center><table class='sortable' width='100%' style='font-size:13px;'>";
			echo "<thead>";
			echo "<tr style='background-color:#B6DAFF;'><th scope=\"col\" align='left'>Block</th><th scope=\"col\" align='left'>Validity</th>".
			     "<th scope=\"col\" align='left'>Finder</th><th scope=\"col\" align='left'>Time</th> <th scope=\"col\" align='left'>Shares</th></tr>";
			echo "</thead>";

			$result = mysql_query("SELECT DISTINCT n.blockNumber, n.confirms, n.timestamp FROM winning_shares w, networkBlocks n WHERE w.blockNumber = n.blockNumber ORDER BY w.blockNumber DESC LIMIT " . $last_no_blocks_found);

			echo "<tbody>";

			while($resultrow = mysql_fetch_object($result)) {
				echo "<tr>";
				$resdss = mysql_query("SELECT username, shareCount FROM winning_shares WHERE blockNumber = $resultrow->blockNumber");
				$resdss = mysql_fetch_object($resdss);

				$splitUsername = explode(".", $resdss->username);
				$realUsername = $splitUsername[0];
				$shareCount = number_format($resdss->shareCount);
				$confirms = $resultrow->confirms;

				if ((is_numeric($confirms)) && ($confirms !== "0")) {
					if ($confirms > 119) {
						$confirms = "<font color='green'>Confirmed!</font>";
					} else {
						$confirms = "<font color='grey'>".(120 - $confirms)." left</font>";
					}
				} else {
					continue(0);
				}

				$block_no = $resultrow->blockNumber;

				echo "<td><a href=\"http://blockexplorer.com/b/$block_no\">" . number_format($block_no) . "</a></td>";
				echo "<td>" . $confirms . "</td>";
				echo "<td>$realUsername</td>";
				echo "<td>".strftime("%F %r",$resultrow->timestamp)."</td>";

				if ($shareCount <= 0) { $shareCount = "Updating..."; }
				echo "<td>$shareCount</td>";
				echo "</tr>";
			}

			echo "</tbody>";
			echo "</table>";

			echo "</center><ul><li>Note: <font color='orange'>Round Earnings are not credited until 120 confirms.</font></li></ul>";
			?>

			</p>
	                </div>          <!-- nested block ends -->
	                <div class="bendl"></div>
	                <div class="bendr"></div>
	                </div>

                </div>          <!-- .sidebar_content ends -->

        </div>          <!-- .block_content ends -->

        <div class="bendl"></div>
        <div class="bendr"></div>

</div>          <!-- .block ends -->


<?php include("includes/templates/footer.php"); ?>
