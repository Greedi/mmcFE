<div class="block withsidebar">

	<div class="block_head">
		<div class="bheadl"></div>
		<div class="bheadr"></div>

		<h2>Welcome, <?php if($cookieValid) { echo $userInfo->username; } else { echo "Guest"; } ?></h2>
	</div>		<!-- .block_head ends -->




	<div class="block_content">

		<div class="sidebar">
			<?php include ("includes/leftsidebar.php"); ?>
		</div>		<!-- .sidebar ends -->


		<div class="sidebar_content">
		<?php
		//Display Error and Good Messages(If Any)
		if ($goodMessage) { echo "<div class=\"message success\"><p>".antiXss($goodMessage)."</p></div>"; }
		if ($returnError) { echo "<div class=\"message errormsg\"><p>".antiXss($returnError)."</p></div>"; }
		?>

		<div class="block" style="clear:none;">
                 <div class="block_head">
                  <div class="bheadl"></div>
                  <div class="bheadr"></div>
		  <h2>Join our pool</h2>
		 </div>

                 <div class="block_content" style="padding:10px;">
		 <p>
		 <form action="/register" method="post">
		        <input type="hidden" name="act" value="attemptRegister">
		        <table width="90%" border="0">
		        <tr><td>Username:</td><td><input type="text" class="text tiny" name="user" value="" size="15" maxlength="20"></td></tr>
		        <tr><td>Password:</td><td><input type="password" class="text tiny" name="pass" value="" size="15" maxlength="20"></td></tr>
		        <tr><td>Repeat Password:</td><td><input type="password" class="text tiny" name="pass2" value="" size="15" maxlength="20"></td></tr>
		        <tr><td>Email:</td><td><input type="text" name="email" class="text small" value="" size="15"><font size="1"> (Optional) </font></td></tr>
		        <tr><td>Email Repeat:</td><td><input type="text" class="text small" name="email2" value="" size="15"><font size="1"> (Optional) </font></td></tr>
		        <tr><td>PIN:</td><td><input type="password" class="text pin" name="authPin" value="" size="4" maxlength="4"><font size="1"> (4 digit number. <b>Remember this pin!</b>)</font></td></tr>
		        </table>
		        <input type="submit" class="submit small" value="Register">
		 </form>
		 </p>
		</div>		<!-- nested block ends -->
                <div class="bendl"></div>
                <div class="bendr"></div>
		</div>
		</div>		<!-- .sidebar_content ends -->


	</div>		<!-- .block_content ends -->




	<div class="bendl"></div>
	<div class="bendr"></div>

</div>		<!-- .block ends -->
