<?php include ("includes/templates/header.php"); ?>

<?php

// check if logged in
if( !$cookieValid ){
        header("Location: /stats");
        exit();
}

$numberResults = 15;
$last_no_blocks_found = 10;
$BTC_per_block = 50; // don't keep this hardcoded
$bitcoinController = new BitcoinClient($rpcType, $rpcUsername, $rpcPassword, $rpcHost);
$difficulty = $bitcoinController->query("getdifficulty");

if ($_GET["more"] == 'true') { $numberResults = 30; }

        // time = difficulty * 2**32 / hashrate
        // hashrate is in Khash/s
        function CalculateTimePerBlock( $btc_difficulty, $_hashrate ){
                if( $btc_difficulty > 0 && $_hashrate > 0 ) {
                        $find_time_hours = ((($btc_difficulty * bcpow(2,32)) / ($_hashrate * bcpow(10,3))) / 3600);
                } else {
                        $find_time_hours = 0;
                }

                return $find_time_hours;
        }

        function CoinsPerDay( $time_per_block, $btc_block ){
                if( $time_per_block > 0 && $btc_block > 0 ) {
                        $coins_per_day = (24 / $time_per_block) * $btc_block;
                } else {
                        $coins_per_day = 0;
                }

                return $coins_per_day;
        }
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

                <div class="sidebar_content" id="sb1">

                <div class="block" style="clear:none;">
                 <div class="block_head">
                  <div class="bheadl"></div>
                  <div class="bheadr"></div>
                  <h2>Pool Stats</h2>
                 </div>

                 <div class="block_content" style="padding:10px;">





			<div class="block small left">
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					<h2>Top <?php echo $numberResults;?> Hashrates</h2>
						<ul class="tabs">
							<li style="font-size:9px;"><a href="?more=true">More</a>&nbsp;&nbsp;</li>
							<li style="font-size:9px;"><a href="/statsAuth">Less</a></li>
						</ul>
				</div>		<!-- .block_head ends -->

				<div class="block_content">

<center>
<table width="100%" border="0" style="font-size:13px;">
<thead>
<tr style="background-color:#B6DAFF;"><th align="left">Rank</th><th align="left" scope="col">User Name</th><th align="left" scope="col">KH/s</th><th align="left">&#3647;/Day<font size="1"> (est)</font></th></tr>
</thead>
<tbody>
<?php

// TOP 20 CURRENT HASHRATES  *******************

$result = mysql_query("SELECT id, hashrate FROM webUsers WHERE hashrate != '0' ORDER BY hashrate DESC LIMIT " . $numberResults);
$rank = 1;
$user_found = false;

while ($resultrow = mysql_fetch_object($result)) {
	$resdss = mysql_query("SELECT username FROM webUsers WHERE id=$resultrow->id");
	$resdss = mysql_fetch_object($resdss);
	$username = $resdss->username;
	if( $cookieValid && $username == $userInfo->username )
	{
		echo "<tr class=\"\">";
		$user_found = true;
	}
	else
	{
		echo "<tr>";
	}
	echo "<td>" . $rank;

	$user_hash_rate = $resultrow->hashrate;

	echo "</td><td>" . $username . "</td><td>" . number_format($user_hash_rate) . "</td><td>&nbsp;";

	$time_per_block = CalculateTimePerBlock($difficulty, $user_hash_rate);

	$coins_day = CoinsPerDay($time_per_block, $BTC_per_block);

	echo number_format( $coins_day, 3 );

	echo "</td></tr>";

	$rank++;
}

if( $cookieValid && $user_found == false )
{
	$query_init       = "SET @rownum := 0";

	$query_getrank    =   "SELECT rank, hashrate FROM (
                        SELECT @rownum := @rownum + 1 AS rank, hashrate, id
                        FROM webUsers ORDER BY hashrate DESC
                        ) as result WHERE id=" . $userInfo->id;

	mysql_query( $query_init );
	$result = mysql_query( $query_getrank );
	$row = mysql_fetch_array( $result );

	$user_hashrate = $row['hashrate'];

	echo "<tr class=\"user_position\" style='background-color:#99EB99;'><td>" . $row['rank'] . "</td><td>" . $userInfo->username . "</td><td>" . number_format( $user_hashrate ) . "</td><td>";

	$time_per_block = CalculateTimePerBlock($difficulty, $user_hashrate);

	$coins_day = CoinsPerDay($time_per_block, $BTC_per_block);

	echo "&nbsp;" . number_format( $coins_day, 3 ) . "</td></tr>";
}
?>
</tbody>
</table>


				</div>		<!-- .block_content ends -->
				<div class="bendl"></div>

				<div class="bendr"></div>
			</div>		<!-- .block.small.left ends -->











			<div class="block small right">
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					<h2>Top <?php echo $numberResults;?> Contributers</h2>
						<ul class="tabs">
							<li style="font-size:9px;"><a href="?more=true">More</a>&nbsp;&nbsp;</li>
							<li style="font-size:9px;"><a href="/statsAuth">Less</a></li>
						</ul>
				</div>		<!-- .block_head ends -->

				<div class="block_content">

<center>
<table class="" width="100%" style="font-size:13px;">
<thead>
<tr style="background-color:#B6DAFF;"><th scope="col" align="left">Rank</th><th scope="col" align="left">User Name</th><th scope="col" align="left">Shares</th></tr>
</thead>
<tbody>
<?php

// TOP 20 Round SHARES Count ***********************************
 //All-time share count
 //$result = mysql_query("SELECT id, share_count-stale_share_count AS shares FROM webUsers ORDER BY shares DESC LIMIT " . $numberResults);

 // Round share count
 $result = mysql_query("SELECT id, shares_this_round AS shares FROM webUsers WHERE shares_this_round > 0 ORDER BY shares DESC LIMIT " . $numberResults);
 $rank = 1;
 $user_found = false;

while ($resultrow = mysql_fetch_object($result)) {
	$resdss = mysql_query("SELECT username, shares_this_round AS shares FROM webUsers WHERE id=$resultrow->id");
	$resdss = mysql_fetch_object($resdss);
	$username = $resdss->username;
	if( $cookieValid && $username == $userInfo->username )
	{
		echo "<tr class=\"user_position\">";
		$user_found = true;
	}
	else
	{
		echo "<tr>";
	}

	echo "<td>" . $rank;

	echo "</td><td>" . $username . "</td><td>" . number_format($resultrow->shares) . "</td></tr>";
	$rank++;
}

if( $cookieValid && $user_found == false )
{
	$query_init       = "SET @rownum := 0";

	$query_getrank    =   "SELECT rank, shares FROM (
                        SELECT @rownum := @rownum + 1 AS rank, shares_this_round AS shares, id
                        FROM webUsers ORDER BY shares DESC
                        ) as result WHERE id=" . $userInfo->id;

	mysql_query( $query_init );
	$result = mysql_query( $query_getrank );
	$row = mysql_fetch_array( $result );

	echo "<tr class=\"user_position\" style='background-color:#99EB99;'><td>" . $row['rank'] . "</td><td>" . $userInfo->username . "</td><td>" . number_format( $row['shares'] ) . "</td></tr>";
}
?>
</tbody>
</table>


				</div>		<!-- .block_content ends -->
				<div class="bendl"></div>

				<div class="bendr"></div>
			</div>		<!-- .block.small.left ends -->











                <div class="block" style="">
                 <div class="block_head">
                  <div class="bheadl"></div>
                  <div class="bheadr"></div>
			<h2>Server Stats</h2>
                 </div>

                 <div class="block_content" style="padding-left:5px;padding-right:5px;">
<?php
// START SERVER STATS ************************************************

echo "<table class=\"\" width='100%' style='font-size:13px;'>";

$hashrate = $settings->getsetting('currenthashrate');
$show_hashrate = round($hashrate / 1000,3);

echo "<tr><td class=\"leftheader\">Pool Hash Rate</td><td>". number_format($show_hashrate, 3) . " Mhash/s</td></tr>";

// Enable this for pool efficiency stats to be shown (useless stat imo)
//$results = mysql_query("SELECT (1 - (SUM(stale_share_count)/SUM(share_count))) * 100 AS efficiency FROM webUsers") or sqlerr(__FILE__, __LINE__);
//$row = mysql_fetch_object($results);
//echo "<tr><td class=\"leftheader\">Pool Efficiency</td><td><span class=\"green\">". number_format($row->efficiency, 2) . "%</span></td></tr>";

$res = mysql_query("SELECT count(webUsers.id) FROM webUsers WHERE hashrate > 0") or sqlerr(__FILE__, __LINE__);
$row = mysql_fetch_array($res);
$users = $row[0];

echo "<tr><td class=\"leftheader\">Current Users Mining</td><td>" . number_format($users) . "</td></tr>";
echo "<tr><td class=\"leftheader\">Current Total Miners</td><td>" . number_format($settings->getsetting('currentworkers')) . "</td></tr>";

$current_block_no = $bitcoinController->query("getblockcount");

echo "<tr><td class=\"leftheader\">Next Network Block</td><td><a href=\"http://explorer.litecoin.net/block/" . $current_block_no . "\" target='_new'>";
echo number_format($current_block_no + 1) . "</a>";

echo " &nbsp;&nbsp;<font size='1'> (Current: <a href=\"http://explorer.litecoin.net/block/" . $current_block_no . "\" target='_new'>" .(number_format($current_block_no)). ")</font></a></td></tr>";

$show_difficulty = round($difficulty, 8);

echo "<tr><td class=\"leftheader\">Current Difficulty</th><td><a href=\"http://allchains.info\" target='_new'><font size='2'>" . $show_difficulty . "</font></a></td></tr>";

$result = mysql_query("SELECT n.blockNumber, n.confirms, n.timestamp FROM winning_shares w, networkBlocks n WHERE w.blockNumber = n.blockNumber ORDER BY w.blockNumber DESC LIMIT 1");

$show_time_since_found = false;
$time_last_found;

	if ($resultrow = mysql_fetch_object($result)) {

		$found_block_no = $resultrow->blockcount;
		$confirm_no = $resultrow->confirms;

echo "<tr><td class=\"leftheader\">Last Block Found</td><td><a href=\"http://explorer.litecoin.net/block/" . $found_block_no . "\" target='_new'>" . number_format($found_block_no) . "</a></td></tr>";

		$time_last_found = $resultrow->timestamp;

		$show_time_since_found = true;
	}

	$time_to_find = CalculateTimePerBlock($difficulty, $hashrate);
	// change 25.75 hours to 25:45 hours
	$intpart = floor( $time_to_find );
	$fraction = $time_to_find - $intpart; // results in 0.75
	$minutes = number_format(($fraction * 60 ),0);

echo "<tr><td class=\"leftheader\">Est. Avg. Time per Round</td><td>" . number_format($time_to_find,0) . " Hours " . $minutes . " Minutes</td></tr>";

	$now = new DateTime( "now" );
	if (isset($time_last_found)) {
		$hours_diff = ($now->getTimestamp() - $time_last_found) / 3600;
	} else {
		$hours_diff = 0;
	}

	if( $hours_diff < $time_to_find ) {
		$time_last_found_out = "<span class=\"green\">";
	} elseif( ( $hours_diff * 2 ) > $time_to_find ) {
		$time_last_found_out = "<span class=\"red\">";
	} else {
		$time_last_found_out = "<span class=\"orange\">";
	}

	$time_last_found_out = $time_last_found_out . floor( $hours_diff ). " Hours " . $hours_diff*60%60 . " Minutes</span>";
	if (empty($time_last_found)) { $time_last_found_out = "N/A"; }

echo "<tr><td class=\"leftheader\">Time Since Last Block</td><td>" . $time_last_found_out . "</td></tr>";

echo "</table>";

echo '<ul><li><font color="orange">Server stats are also available in JSON format <a href="/api" target="_api">HERE</a></font></li></ul>';
?>


                </div>          <!-- nested block ends -->
                <div class="bendl"></div>
                <div class="bendr"></div>
                </div>











                <div class="block" style="clear:none;">
                 <div class="block_head">
                  <div class="bheadl"></div>
                  <div class="bheadr"></div>
			<h2>Last <?php echo $last_no_blocks_found. " Blocks Found"; ?></h2>

			<ul class="tabs">
				<li><a href="/blocksAuth">More</a></li>
			</ul>
                 </div>

                 <div class="block_content" style="padding-left:5px;padding-right:5px;">
		<p>
<?php
// SHOW LAST (=$last_no_blocks_found) BLOCKS
echo "<center><table class=\"stats_lastblocks\" width='100%' style='font-size:13px;'>";
echo "<tr style='background-color:#B6DAFF;'><th scope=\"col\" align='left'>Block</th><th scope=\"col\" align='left'>Validity</th>".
     "<th scope=\"col\" align='left'>Finder</th><th scope=\"col\" align='left'>Date / Time</th> <th scope=\"col\" align='left'>Shares</th></tr>";

$result = mysql_query("SELECT DISTINCT n.blockNumber, n.confirms, n.timestamp FROM winning_shares w, networkBlocks n WHERE w.blockNumber = n.blockNumber ORDER BY w.blockNumber DESC LIMIT " . $last_no_blocks_found);

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

	echo "<td><a href=\"http://explorer.litecoin.net/block/$block_no\">" . number_format($block_no) . "</a></td>";
	echo "<td>" . $confirms . "</td>";
	echo "<td>$realUsername</td>";
	echo "<td>".strftime("%F %r",$resultrow->timestamp)."</td>";

	if ($shareCount <= 0) { $shareCount = "Updating..."; }
	echo "<td>$shareCount</td>";
	echo "</tr>";
}

echo "</table>";

echo "</center><ul><li>Note: <font color='orange'>Round Earnings are not credited until 120 confirms.</font></li></ul>";
?>

		</p>
                </div>          <!-- nested block ends -->
                <div class="bendl"></div>
                <div class="bendr"></div>
                </div>












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
