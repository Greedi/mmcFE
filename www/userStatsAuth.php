<?php

	include("includes/templates/header.php");
	include("includes/userStatsAuth.inc.php");

	// Interval in Hours
	$interval = "96";

	// check if logged in
	if( !$cookieValid ){
	        header("Location: /index");
	        exit();
	}

	// debug: page load time
	$time = microtime();
	$time = explode(" ", $time);
	$time = $time[1] + $time[0];
	$start = $time;

	// check which graph to display
	if (!isset($_GET["graph"])) { $graph = "mine"; } else { $graph = $_GET["graph"]; }
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

<!-- <div class="message warning" style="width:auto;"><p>This page and its contents are in Beta testing.  Data may sometimes be inaccurate.</p></div> -->

	<!-- START User Stats Block -->

                <div class="block" style="clear:none;">
                 <div class="block_head">
                  <div class="bheadl"></div>
                  <div class="bheadr"></div>
                  <h2>User Stats</h2>
			<ul class="tabs">
				<li><a href="?graph=mine">Mine &nbsp;</a></li>
				<li><a href="?graph=pool">Pool &nbsp;</a></li>
				<li><a href="?graph=both">Both</a></li>
			</ul>
                </div>

                <div class="block_content"> <!-- Right content block begin -->

			<div id="hashstats" style="">	<!-- hash graphs -->
			<?php
				hashGraphs($graph, $userId, $interval);
			?>
			</div>

			<center>
			<p style="padding-left:30px; padding-right:30px; margin-top:0px; font-size:10px;">
			Hashrate graphs update every ~60 seconds if you have active workers.
			</p></center>

			<div id="financestats" style="">   <!-- financial graphs -->
			<?php
				financialGraphs($graph, $userId);
			?>
			</div>

		</div>          <!-- nested right content block ends -->

                <div class="bendl"></div>
                <div class="bendr"></div>
                </div>

	<!-- END User Stats Block -->

                </div>          <!-- .sidebar_content ends -->

        </div>          <!-- .block_content ends -->

        <div class="bendl"></div>
        <div class="bendr"></div>

</div>          <!-- .block ends -->


<?php
	include("includes/templates/footer.php");

	// debug: page load time
	$time = microtime();
	$time = explode(" ", $time);
	$time = $time[1] + $time[0];
	$finish = $time;
	$totaltime = ($finish - $start);
	printf ("<center><font size=1>%fs</font></center>", $totaltime);
?>
