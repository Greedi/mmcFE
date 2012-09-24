<?php

include ("includes/templates/header.php");

$returnError = "";
$goodMessage = "";

	// sanitize the input
	if (!empty($_POST["email"])) { $email = mysql_real_escape_string($_POST["email"]); } else {
		$email = NULL;
		$returnError = "Empty Email or Pin #";
	}
	if (!empty($_POST["authPin"])) { $inputAuthPin = mysql_real_escape_string(hash("sha256", $_POST["authPin"].$salt)); } else {
		$inputAuthPin = NULL;
		$returnError = "Empty Email or Pin #";
	}

	// see if we can get a match on email and pin number entered
	if ((!is_null($email)) && (!is_null($inputAuthPin))) {
		$findUser = mysql_query("SELECT username, email, pin FROM webUsers WHERE email = '" .$email. "' AND pin ='" .$inputAuthPin. "'");
		if (($findUser == false) || (mysql_num_rows($findUser) < 1)) {
			$returnError = "User not found or PIN incorrect";

		// eek! there are 2 or more users with the same email and pin!? this should never happen... bail!
		} else if (mysql_num_rows($findUser) > 1) {
			$returnError = "Unknown error while retrieving credentials. Please contact Support.";

		} else {
			// we got a matching pair and only a single hit.  Lets reset the pass and email the accountholder.
			$goodMessage = "Account match found. ";
			$user = mysql_fetch_array($findUser, MYSQL_ASSOC);

			// generate a new password and update the db
			$new_pass = genRandomString(12);
			$new_hash = hash("sha256", $new_pass.$salt);

		        $passchangeSuccess = mysql_query("UPDATE `webUsers` SET `pass` = '".$new_hash."' WHERE `username` = '".$user['username']."'");
                        if($passchangeSuccess) {
				$goodMessage .= "An email has been dispatched to you with a new temporary password.";
                        } else {
                                $returnError .= "Database Failure - Unable to change password";
                        }

			// set up mailer here
			$to      = $user['email'];
			$subject = 'Mainframe Mining Cooperative Password Recovery';
			$headers = 'From: Mainframe Mining Cooperative <do_not_reply@mining.mainframe.nl>' . "\r\n" .
			           'Reply-To: do_not_reply@mining.mainframe.nl' . "\r\n"; //.
			           //'X-Mailer: PHP/' . phpversion();

			$message = 	"Dear MMC Miner,\n".
					"\n".
					"You or someone claiming to be you has requested a password reset for your account at mining.mainframe.nl and\n".
					"were successfully able to authenticate themself with the email address and 4 digit pin you chose when registering your account.\n".
					"\n".
					"We have reset your account password and suggest that you immediately login and change this to a new password and store it\n".
					"securely for safekeeping.\n".
					"\n".
					"Your username is: \t" .$user['username']. "\n".
					"Your new password is: \t" .$new_pass. "\n".
					"\n".
					"Thank you and kind regards,\n".
					"The Mainframe Staff";

		        mail($to, $subject, $message, $headers);
		}
	}

?>

<div class="block withsidebar">

        <div class="block_head">
                <div class="bheadl"></div>
                <div class="bheadr"></div>

                <h2>Welcome, <?php if($cookieValid) { echo $userInfo->username; } else { echo "Guest"; } ?></h2>
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

                <div class="block" style="clear:none;">
                 <div class="block_head">
                  <div class="bheadl"></div>
                  <div class="bheadr"></div>
                  <h2>Password Recovery</h2>
                 </div>

                 <div class="block_content" style="padding:10px;">
<p>
 <form action="/lostPass" method="post">
	<h1>Oops! Lost Your Password?</h1>
	<hr size="1" width="100%">
	<br>
	<ul><li>
	If you have forgotten or lost your authentication credentials, it is still possible to gain access to your account
	providing that you have not ALSO forgotten or lost your 4 DIGIT PIN code AND your email address.<br><br>
	Please fill in the form below with this information and a mail will be sent with instructions for regaining access
	to your account.
	</li>
	<br>
	<input type="hidden" name="act" value="attemptRegister">
	<table border="0">
	<tr><td>Email Address: </td><td><input type="text" class="text small" name="email" value="<?php echo antiXss($email); ?>" size="35"><font size="2px"></font></td></tr>
	<tr><td>PIN: </td><td><input type="password" class="text pin" name="authPin" value="" size="4" maxlength="4"><font size="2px"> (4 Digit PIN #)</font></td></tr>
	</table><br>
	<input type="submit" class="submit small" value="Submit">
	</ul>
 </form>

<br><ul><li>If you have problems recovering your password, please use the Support link in the top menu to contact us directly.</li></ul>
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
