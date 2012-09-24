<?php

include ("includes/templates/header.php");

$goodMessage = "";
$returnError = "";
$userTable = "";

//Since this is the Admin panel we'll make sure the user is logged in and "isAdmin" enabled boolean; If this is not a logged in
// user that is enabled as admin, redirect to a 404 error page
if(!$cookieValid || $isAdmin != 1) {
	header('Location: /');
	exit;
}

if (isset($_POST["act"]))
{
	$act = mysql_real_escape_string($_POST["act"]);
	if (isset($_POST["authPin"])) { $inputAuthPin = mysql_real_escape_string(hash("sha256", $_POST["authPin"].$salt)); } else { $inputAuthPin = ""; }
	//Make sure an authPin is set and valid when $act is active
	if($act) {
		// Site Admin General Settings (pin needed)
		if($act == "UpdateMainPageSettings" && $authPin == $inputAuthPin) {
			try {
				$settings->setsetting("sitepayoutaddress", mysql_real_escape_string($_POST["paymentAddress"]));
				$settings->setsetting("sitepercent", mysql_real_escape_string($_POST["percentageFee"]));
				$settings->setsetting("websitename", mysql_real_escape_string($_POST["headerTitle"]));
				$settings->setsetting("pagetitle", mysql_real_escape_string($_POST["pageTitle"]));
				$settings->setsetting("slogan", mysql_real_escape_string($_POST["headerSlogan"]));
				$settings->setsetting("siterewardtype", mysql_real_escape_string($_POST["rewardType"]));
				$settings->loadsettings(); //refresh settings
				$goodMessage = "Successfully updated general settings";
			} catch (Exception $e) {
				$returnError = "Database Failed - General settings was not updated";
			}
		} else {
			if($act == "UpdateMainPageSettings" && $authPin != $inputAuthPin) {
				$returnError = "Authorization Pin Invalid or not entered.";
			}
		}

		// User Control (no pin needed)
		if(($act == "userControl") && (empty($_POST["searchStr"]))) {

			$returnError = "No search string specified.";
			$num_results = "0";

		} else if(($act == "userControl") && (!empty($_POST["searchStr"]))) {

			if (isset($_POST["searchStr"])) { $searchStr = mysql_real_escape_string($_POST["searchStr"]); }

			if (is_numeric($searchStr)) {
				$search_resultsQ = mysql_query("SELECT * FROM webUsers WHERE id = " .$searchStr. " ORDER BY `round_estimate` DESC LIMIT 1");
			} else {
				$search_resultsQ = mysql_query("SELECT * FROM webUsers WHERE `username` LIKE '%" .$searchStr. "%' OR `loggedIp` LIKE '%" .$searchStr. "%'".
							       " ORDER BY `round_estimate` DESC");
			}

			$search_results = "";

			$count = 1;
			$search_id = NULL;
			while ($row = mysql_fetch_array($search_resultsQ, MYSQL_ASSOC)) {

				$search_results .= "<tr style='background-color:#fff;'><td>" .$row['id']. "</td><td>" .$row["username"]. "</td><td>" .round($row["round_estimate"], 4).
						   "</td><td class='row_email' style='display:none;'>" .$row["loggedIp"]."</td><td class='row_email' style='display:none;'>" .$row["email"]. "</td><td>" .$row["share_count"].
						   "</td><td>" .$row["stale_share_count"]. "</td><td>" .$row["shares_this_round"].
						   "</td><td>" .$row["donate_percent"]. "</td><td>" .round(($row["hashrate"] / 1024), 2). "</td>".
						   "<td>" .@round(($row['stale_share_count'] / $row['share_count'] * 100), 2). "</td></tr>";
				$count++;
				$search_id = $row["id"];
			}

			$num_results = ($count - 1);
		}
	}
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


<?php
//Display Error and Good Messages(If Any)
if ($goodMessage) { echo "<div class=\"message success\"><p>".antiXss($goodMessage)."</p></div>"; }
if ($returnError) { echo "<div class=\"message errormsg\"><p>".antiXss($returnError)."</p></div>"; }
?>

<div id="AdminContainer">
                <div class="block" style="clear:none;">
                 <div class="block_head">
                  <div class="bheadl"></div>
                  <div class="bheadr"></div>
			<h1>Site Admin General Settings</h1>
                </div>

                <div class="block_content" style="padding:10px;">

		<!--Begin main page edits-->
		<form action="/adminPanel" method="post">
			<input type="hidden" name="act" value="UpdateMainPageSettings">
			Page Title <input type="text" name="pageTitle" size="50" value="<?php echo antiXss($settings->getsetting("pagetitle"));?>"><br/>
			Header Title <input type="text" name="headerTitle" size="45" value="<?php echo antiXss($settings->getsetting("websitename"));?>"><br/>
			Header Slogan <input type="text" name="headerSlogan" size="40" value="<?php echo antiXss($settings->getsetting("slogan"));?>"><br/>
			Percentage Fee <input type="text" name="percentageFee" size="5" maxlength="10" value="<?php echo antiXss($settings->getsetting("sitepercent")); ?>">%<br/>
			Fee Address <input type="text" name="paymentAddress" size="50" value="<?php echo antiXss($settings->getsetting("sitepayoutaddress")); ?>"><br/>
			Default Reward Type <select name="rewardType" size="1">
			<option value="1" <?php if ($settings->getsetting("siterewardtype") == 1) echo "selected"; ?>>PPLNS</option>
			</select>
			<br/><br/>
			Authorization Pin <input type="password" size="4" maxlength="4" name="authPin"><br/><br>
			<input type="submit" class="" value=" Update Site Settings ">
		</form>
                </div>          <!-- nested block ends -->
                <div class="bendl"></div>
                <div class="bendr"></div>
                </div>




                <div class="block" style="clear:none;">
                 <div class="block_head">
                  <div class="bheadl"></div>
                  <div class="bheadr"></div>
			<h1>Financial Statistics</h1>
                </div>

                <div class="block_content" style="padding:10px;">
	<?php

	$bitcoinController = new BitcoinClient($rpcType, $rpcUsername, $rpcPassword, $rpcHost);

	//$sitewallet = mysql_query("SELECT sum(balance) FROM `accountBalance` WHERE `balance` > 0") or sqlerr(__FILE__, __LINE__);
	$sitewallet = mysql_query("SELECT sum(balance) FROM `accountBalance`") or sqlerr(__FILE__, __LINE__);
	$sitewalletq = mysql_fetch_row($sitewallet);

	$unconf_blocksQ = mysql_query("SELECT DISTINCT confirms from `networkBlocks` WHERE `confirms` < 120 AND `confirms` > 0");
	$sitePercentQ = mysql_query("SELECT value FROM settings WHERE setting='sitepercent'");

	if ($sitePercentR = mysql_fetch_object($sitePercentQ)) {
	$sitePercent = $sitePercentR->value;
	}

	$unconf_blocks = mysql_num_rows($unconf_blocksQ);
	$unconf_income = ($unconf_blocks * (50 * ($sitePercent / 100)));
	$usersbalance = $sitewalletq[0] / 1;
	$user_reserve = ($unconf_blocks);
	$donation_reserve = $settings->getsetting("tobedonated");
	$balance = $bitcoinController->query("getbalance");
	$subtotal = $balance - $usersbalance - $donation_reserve;


	echo "Current Block Number: ".$bitcoinController->getblockcount()."<br>";
	echo "Current Difficulty: ".$bitcoinController->query("getdifficulty")."<br>";

	// Enable this for pool efficiency stats to be shown (useless stat imo)
	$results = mysql_query("SELECT (1 - (SUM(stale_share_count)/SUM(share_count))) * 100 AS efficiency FROM webUsers") or sqlerr(__FILE__, __LINE__);
	$row = mysql_fetch_object($results);
	echo "Pool Efficiency: ". number_format($row->efficiency, 2) . "%";
	echo "<br><br>";

	echo "Wallet Balance: ".$balance."<br>";
	echo "Held for Users: ".$usersbalance."<br>";
	echo "Held for Donation: ".$donation_reserve."<br>";
	echo "Immature Blocks: " .$user_reserve. "<br>";
	//echo "Pool Immature: " .$unconf_income. "<br>";
	echo "<br>";
	echo "Actual Liquid Assets: $subtotal<br>";
	//echo "Forecasted Assets: " .($subtotal + $unconf_income). "<br>";

	?>
        </div>          <!-- nested block ends -->

        <div class="bendl"></div>
        <div class="bendr"></div>
       </div>






                 <div class="block" style="clear:none;">
                 <div class="block_head">
                  <div class="bheadl"></div>
                  <div class="bheadr"></div>
			<h1>User Control</h1>
                 </div>

                 <div class="block_content" style="padding:10px;">

	<ul><li><font color="orange">Search by IP Address, Userame, or UserId</font></li></ul>
	<form action="/adminPanel" method="post">
		<input type="hidden" name="act" value="userControl">
		Search String &nbsp;
		<input type="text" name="searchStr" value="%">
		<input type="submit" value=" Search ">
	</form>
	<br>

	<?php if($num_results) { echo "<font size='1'>" .$num_results. " result(s)</font>"; }?>
	<?php //if($num_results) { echo $num_results. " result(s)"; }?>

	<div id="search_infobox">
		<div class="search_results">
			<table width="100%" border="0" style="font-size:13px;" class="sortable">
			<thead>
			<tr style='background-color:#B6DAFF; font-size:10px;'>
				<th onclick="$('.row_email').toggle();"><font color="blue">Show/Hide</font></th>
				<th>User</th>
				<th>Est.</th>
				<th class="row_email" style="display:none;">IP</th>
				<th class="row_email" style="display:none;" onclick="$('.row_email').toggle();">Email</th>
				<th>Tot.</th>
				<th>Stale</th>
				<th>Round</th>
				<th>Don. %</th>
				<th>MH/s</th>
				<th>Stale %</th>
			</tr>
			</thead>

			<tbody>
			<?php
			if (!empty($search_results)) {
				print "<script>$('#search_infobox').show();</script>";
				print $search_results;
			}
			?>
			</tbody>
			</table>
		</div>

		<div id="item_results"><?php print $item_results; ?></div>

	</div>

	<!-- payment history / transaction log -->

	<div id="generic_infobo" class="tx_log">
	<?php
	if ((isset($search_id)) && (is_numeric($search_id))) {
		$search_id_to_userObj = mysql_fetch_object(mysql_query("SELECT username FROM webUsers WHERE id = " . $search_id));
	}
	?>
	<br>
	<h1><?php if ($num_results == 1) { echo $search_id_to_userObj->username; } ?> (account mutations)</h1>

	<ul><li><font color="">(ATP = Auto Threshold Payment, MP = Manual Payment)</font></li></ul>

		<table cellpadding="1" cellspacing="1" width="100%" class="sortable">
		<thead style="font-size:13px;">
			<tr>
			<th>TX #</th>
			<th>Date</th>
			<th>TX Type</th>
			<th>Payment Address</th>
			<th>Block #</th>
			<th>Amount</th>
			</tr>
		</thead>

		<tbody>
	        <?php
	        if (($num_results == 1) && (!is_null($search_id))) {
        	        $sql = mysql_query("SELECT * FROM `ledger` where userId = " .$search_id. " ORDER BY timestamp DESC");

	                while ($obj = mysql_fetch_object($sql)) {

	                        $mutation = explode(".", $obj->amount);

	                        if ($obj->transType == "Debit_ATP" || $obj->transType == "Debit_MP") {
	                                $obj->amount = "<font color='red'>-".$obj->amount."</font>";
					//echo "<tr style='background-color:orange;'>";
	                        } else {
	                                $obj->amount = "&nbsp;<font color='green'>".$obj->amount."</font>";
					//echo "<tr style='background-color:#99EB99;'>";
	                        }
				if ($obj->assocBlock == "0") { $obj->assocBlock = ''; }

				$feeAmount = number_format($obj->feeAmount, 8, '.', '');
				if ($feeAmount > 0.0000001) {
					printf("<td><font size='1'>%s</td><td><font size='1'>%s</td><td><font size='2'>Don_Fee</td><td><font size='1'>&nbsp;</font></td><td><font size='2'>%s</td><td align=''><font size='2' color='orange'>-%s</td></tr>".
	                                	"", 10000+$obj->id, $obj->timestamp, $obj->assocBlock, $feeAmount);
				}

				printf("<td><font size='1'>%s</td><td><font size='1'>%s</td><td><font size='2'>%s</td><td><font size='1'>%s</font></td><td><font size='2'>%s</td><td align=''><font size='2'>%s</td></tr>".
	                                "", 10000+$obj->id, $obj->timestamp, $obj->transType, $obj->sendAddress, $obj->assocBlock, $obj->amount);
	                }

	        } else {
                	echo "<script>$('.tx_log').toggle();</script>";
	        }
		?>
		</tbody>
		</table>

	</div>
                </div>          <!-- nested block ends -->
                <div class="bendl"></div>
                <div class="bendr"></div>
                </div>


</div>
                </div>          <!-- .sidebar_content ends -->


        </div>          <!-- .block_content ends -->




        <div class="bendl"></div>
        <div class="bendr"></div>

</div>          <!-- .block ends -->

<?php include ("includes/templates/footer.php"); ?>
