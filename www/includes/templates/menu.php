<br>
<div id="header">
		<ul id="nav">

		<li><a href="/index">Home</a></li>

        <?php
        if(!$cookieValid){
                //Display this menu if the user isn't logged in
	?>
			<li><a href="/register">Register</a></li>
	<?php
	} else if($cookieValid){
	?>
			<li><a href="/accountdetails">My Account</a>
				<ul>
					<li><a href="/accountdetails">Account Details</a></li>
					<li><a href="/accountworkers">My Workers</a></li>
					<li><a href="/accounttransactions">Transactions</a></li>
				</ul>
			</li>
	<?php
	//If this user is an admin show the adminPanel link
        	if($isAdmin){
	?>
			<li><a href="/adminPanel">Admin Panel</a></li>
	<?php
        	}
        }
	?>
	<?php if($cookieValid){ ?>
		<li><a href="/statsAuth">Stats</a>
			<ul>
				<li><a href="/statsAuth">Pool Stats</a></li>
				<li><a href="/blocksAuth">Block Stats</a></li>
			</ul>
		</li>
	<?php } else { ?>
		<li><a href="/stats">Stats</a></li>
	<?php } ?>
		<li><a href="/gettingstarted">Getting Started</a></li>

		<li><a href="/support">Support</a></li>

		<li><a href="/about">About</a>

		<li><a href="/news">News</a>
		<!--
			<ul>
				<li><a href="#">About Bitcoin</a></li>
				<li><a href="#">About Mainframe</a></li>
				<li><a href="#">API Details</a></li>
			</ul>
		-->
		</li>

		<?php if($cookieValid){ ?>
			<li><a href="/logout">Logout</a></li>
		<?php } ?>
	</ul>
</div>
