<?php

include ("includes/templates/header.php");

if(!$cookieValid) {
	header('Location: /');
	exit;
}

$returnError = "";
$goodMessage = "";

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
		<?php
		//Display Error and Good Messages(If Any)
		if ($goodMessage) { echo "<div class=\"message success\"><p>".antiXss($goodMessage)."</p></div>"; }
		if ($returnError) { echo "<div class=\"message errormsg\"><p>".antiXss($returnError)."</p></div>"; }
		?>

		<!-- payment history -->
		<div class="block" style="clear:none;">

		<div class="block_head">
		 <div class="bheadl"></div>
		 <div class="bheadr"></div>
		 <h2>Payout / Transaction Log</h2>
			<ul class="tabs">
				<li style="font-size:9px;"><a href="#confirmed">Confirmed &nbsp;</a></li>
				<li style="font-size:9px;"><a href="#unconfirmed">Unconfirmed &nbsp;</a></li>
			</ul>
		</div>

		<div class="block_content tab_content" id="confirmed" style="clear:;">
			<br><center><p><font color="" size="1"><b>ATP</b> = Auto Threshold Payment, <b>MP</b> = Manual Payment, <b>Don_Fee</b> = donation amount + pool fees (if applicable)</font></p>

			<table cellpadding="1" cellspacing="1" width="98%" class="sortable">
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
			if ((isset($_GET["page"])) && (is_numeric($_GET["page"]))) { $page = mysql_real_escape_string($_GET["page"]); } else { $page = 1; }
			if ($page == 0) { $page = 1; }
			$start = ($page * 15 - 15);

			$sql = mysql_query("SELECT * FROM `ledger` where userId = " .$userId. " ORDER BY timestamp DESC LIMIT ".$start.",15");

			$num_results = mysql_num_rows(mysql_query("SELECT * FROM `ledger` where userId = " .$userId. " ORDER BY timestamp DESC"));
			$num_pages = $num_results / 15;

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

		        ?>
			</tbody>
			</table>

			<div class="pagination right" style="">
				<?php

				if ($num_pages > 1) {
					$page_no = 0;

					if ($page > 1) {
						echo "<a href='accounttransactions?page=".($page - 1)."'>&laquo;</a>";
					}

					while ($num_pages > $page_no) {
						if  (($page_no + 1) == $page) {
							echo "<a href='accounttransactions?page=".($page_no + 1)."' class='active'>" .($page_no + 1). "</a>";
						} else {
							echo "<a href='accounttransactions?page=".($page_no + 1)."'>" .($page_no + 1). "</a>";
						}
						$page_no++;
					}

					if ($page < $page_no) {
						echo "<a href='accounttransactions?page=".($page + 1)."'>&raquo;</a>";
					}
				}
				?>
			</div>		<!-- .pagination ends -->

		</div>		<!-- nested block content ends -->


		<div class="block_content tab_content" id="unconfirmed" style="">	<!-- unconfirmed rewards -->

                        <center><br>
			<p><font color="" size="2">Listed below are your estimated rewards and donations/fees for all blocks awaiting 120 confirmations.</font></p>
			<table cellpadding="1" cellspacing="1" width="98%" class="sortable">
                        <thead style="font-size:13px;">
                                <tr>
                                <th>Block #</th>
                                <th>Estimated Reward</th>
                                <th>Valid Shares</th>
                                <th>Donation / Fee</th>
                                <th>Validity</th>
                                </tr>
                        </thead>

			<tbody style="font-size:12px;">
			<?php
				include("includes/helperFunctions.inc.php");

				$unconf_blocksQ = mysql_query("SELECT DISTINCT blockNumber from `networkBlocks` WHERE `confirms` < 120 AND `confirms` > 0 ORDER BY blockNumber DESC LIMIT 10");

				while ($blocks = mysql_fetch_object($unconf_blocksQ)) {
					$block = $blocks->blockNumber;
					estimate_user_rewards($block, $userId);
					$pendingTotal += $userReward;
					$pendingFee += $donation;
				}
				echo "<tr><td></td><td></td><td></td><td></td><td></td></tr>";
				echo "<tr><td><b>Unconfirmed Totals:</b></td><td><b>" .number_format($pendingTotal, 8, '.', ''). "</b></td><td></td><td><b>" .number_format($pendingFee, 8, '.', ''). "</b></td><td></td></tr>";
			?>
			</tbody>

			</table></center>

		</div>		<!-- nested block content ends -->


		<div class="bendl"></div>
		<div class="bendr"></div>
		</div>
		</div>          <!-- .sidebar_content ends -->

        </div>          <!-- .block_content ends -->

        <div class="bendl"></div>
        <div class="bendr"></div>

</div>          <!-- .block ends -->

<?php include ("includes/templates/footer.php");?>
