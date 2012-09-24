<?php
include ("includes/templates/header.php");

//Test registration information
$returnError = "";
$goodMessage = "";
if (isset($_POST["act"]))
	{
	$act = $_POST["act"];
	if($act == "attemptRegister"){
		//Valid date all fields
		$username	= mysql_real_escape_string($_POST["user"]);
		$pass		= mysql_real_escape_string($_POST["pass"]);
		$rPass		= mysql_real_escape_string($_POST["pass2"]);
		$email		= mysql_real_escape_string($_POST["email"]);
		$email2		= mysql_real_escape_string($_POST["email2"]);
		$authPin	= mysql_real_escape_string($_POST["authPin"]);

		$validRegister = 1;
			//Validate username
			if (!preg_match('/^[a-z\d_]{4,20}$/i', $username)) {
				$validRegister = 0;
			   	$returnError .= "Wrong username format or username too short";
			}

			//Validate passwords
			if($pass != $rPass){
				if(strlen($pass) < 5){
					$validRegister = 0;
					$returnError .= " | Password is too short";
				}else{
					$validRegister = 0;
					$returnError .= " | Passwords do not match";
				}
			}

			//Email Validation
			if ($email !== "") {
				if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
					$validRegister = 0;
				    	$returnError .= " | Wrong email address format.";
				}else{
					//Validate that emails match
					if($email != $email2){
						$validRegister = 0;
						$returnError .= " | Emails didn't match!";
					}
				}
			}

			//validate authpin
			if(strlen($authPin) >= 4){
				if(!is_numeric($authPin)){
					$validRegister = 0;
					$returnError .= " | Not a valid authpin";
				}
			}else{
				$validRegister = 0;
				$returnError .= " | Authorization pin number is not valid";
			}

		if($validRegister){
			//Add user to webUsers
			$emailAuthPin = genRandomString(10);
			$secret = genRandomString(10);
			$apikey = hash("sha256",$username.$salt);
			//Check to see if user exists already
			$testUserQ = mysql_query("SELECT id FROM webUsers WHERE username = '".$username."' LIMIT 1");
			//If not, create new user
			//if (!$testUserQ) {
			if (($testUserQ == false) || (mysql_num_rows($testUserQ) == 0)) {
				$result = mysql_query("INSERT INTO webUsers (admin, username, pass, email, emailAuthPin, secret, loggedIp, sessionTimeoutStamp, accountLocked, accountFailedAttempts, pin, share_count, stale_share_count, shares_this_round, api_key)
				VALUES (0, '".$username."', '".hash("sha256", $pass.$salt)."', '".$email."', '".$emailAuthPin."', '".$secret."', '0', '0', '0', '0', '".hash("sha256", $authPin.$salt)."', '0', '0', '0', '".$apikey."')");

				$returnId = mysql_insert_id();
				mysql_query("INSERT INTO accountBalance (userId, balance) VALUES (".$returnId.",'0')");
				mysql_query("INSERT INTO pool_worker (associatedUserId, username, password) VALUES (".$returnId.",'".$username.".1','x')");
				$goodMessage = "Your account has been successfully created. Please login to continue.";
			} else {
				$returnError = "Account already exists. Please choose a different username.";
			}
		}
	}
}

include("includes/templates/register.php");
include("includes/templates/footer.php");

?>
