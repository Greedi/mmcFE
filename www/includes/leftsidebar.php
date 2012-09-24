<div class="block" style="clear:none; margin-top:15px; margin-left:13px;">

 <div class="block_head">
 <div class="bheadl"></div>
 <div class="bheadr"></div>
  <h1>Dashboard</h1>
 </div>

<div class="block_content" style="padding-top:10px;">
	<?php

		if(!$cookieValid){
			//No valid cookie show login//
	?>
	<div style="margin-left:-10px;p-align:center;text-align:center;">
		<!--Login Input Field-->
		<form action="/login" method="post" id="loginForm">
			<p><input type="text" name="username" value="" id="userForm" maxlength="20"></p>
			<p><input type="password" name="password" value="" id="passForm" maxlength="20"></p>
			<p><input type="submit" class="submit small" value="Login"></p>
		</form>
		<p><a href="/lostPass"><font size="1">Forgot your password?</font></a></p>
	</div>

	<?php
		} else if ($cookieValid) {	//Valid cookie YES! Show this user stats//
	?>
	<p>
		<?php
			/* check account type and fee percentage for logged in user
			$account_type = 0;
			$account_type = account_type($userInfo->id);

			if ($account_type == 9) {
				$account_type = "<b>Early-Adopter</b>: <b>0%</b> Pool Fee";
			} else {
				$account_type = "<b>Active Account</b>: <b>" .$settings->getsetting("sitepercent"). "%</b> Pool Fee";
			}

			echo "<font size='1px'>" .$account_type."</font><br>";
			echo "<font size='1px'><i>You are <a href='/osList'>donating</a> <b></i>" .antiXss($donatePercent)."%</b> of your earnings.</font><br>";
			*/

			echo "<b><u>Your Current Hashrate</u></b><br><i><b>".$currentUserHashrate." KH/s</b></i><br/><br/>";

			echo "<u><b>Paid Shares</b></u> <span id='tt'><img src='images/questionmark.png' height='15px' width='15px' ".
			     "title='All submitted shares from previous rounds which are already accounted and ".
			     "paid for.'></span>".
			     "<br>";

			if (($lifetimeUserShares < 0) || ($lifetimeUserInvalidShares < 0)) {
				echo "Your Valid: <i><b>Updating...</b></i><br/>";
				echo "Invalid: <i><b>Updating...</b></i><br/><br/>";
			} else {
				echo "Your Valid: <i><b>" .$lifetimeUserShares. "</b></i><br/>";
				echo "Invalid: <i><b>".($lifetimeUserInvalidShares / 1)."</b></i><br/><br/>";
			}

			echo "<u><b>Unpaid Shares</b></u> ".
			     "<span id='tt'><img src='images/questionmark.png' height='15px' width='15px' title='Submitted shares between the last 120 confirms ".
			     "block until now.'></span>".
			     "<br>";

			echo "Your Valid: <b><i>".($totalUserShares / 1)."</i> <font size='1px'></font></b><br/>";
			echo "Pool Valid: <b><i>".$totalOverallShares."</i> <font size='1px'></font></b><br/><br>";

			// calculate number of shares towards next block (traditional round shares calculation)
			$pending_sharesQ = mysql_fetch_object(mysql_query("SELECT sum(shareCount) as count FROM winning_shares WHERE blockNumber >= (".
									  "SELECT blockNumber FROM networkBlocks WHERE confirms != '' AND confirms < 120 ".
									   "ORDER BY blockNumber ASC LIMIT 1)"));
			$pending_shares = $pending_sharesQ->count;
			if ($pending_shares) {
				$nextblock_shares = ($totalOverallShares - $pending_shares);
			} else {
				$nextblock_shares = $totalOverallShares;
			}
			if ($nextblock_shares < 0) { $nextblock_shares = "Updating..."; }

			echo "<u><b>Round Shares </b></u>";
			echo "<span id='tt'><img src='images/questionmark.png' height='15px' width='15px' title='Submitted shares since last found block (ie. round shares)'></span><br>";
			echo "Pool Valid: <b><i>".$nextblock_shares."</i></b><br><br>";

			echo "<u><b>Round Estimate</b></u><font size='1'></font></u><br><b><i>".round($userRoundEstimate, 8)."</i> <font size='1px'>LTC</font></b><br><br>";

			echo "<u><b>Account Balance</b></u><br><b><i>".$currentBalance." </i><font size='1px'>LTC</font></b><br/><br>";
			//echo "</p>";

		?>

<?php
	}
?>
 </p>

	<center><hr width="90%"></center>
	<div style="margin-top:-13px; margin-bottom:-15px;">
	<p>
		<b><font size="1">
		Stats last updated:</b>
		<br><i>
		<?php echo "".date("H:i:s", $settings->getsetting('statstime'))." GMT+2"; ?>
		<br>
		(updated every 60 secs)
		</font></i><br/>
	</p>
	</div> <!-- stats update div ends -->

 </div> <!-- block_content ends -->
 <div class="bendl"></div>
 <div class="bendr"></div>
</div> <!-- block ends -->
