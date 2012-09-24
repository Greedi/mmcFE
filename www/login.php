<?php
//
//This page will attempt to take informtion from the user and create an ecrypted session inside of a cookie
//

//Include site functions
include("includes/requiredFunctions.php");

//Filter input results before querying them into database
if (isset($_POST["username"])) { $user = mysql_real_escape_string($_POST["username"]); } else { $user = NULL; }
if (isset($_POST["password"])) { $pass = mysql_real_escape_string($_POST["password"]); } else { $pass = NULL; }

//Check the supplied username & password with the saved username & password
$checkPassQ = mysql_query("SELECT id, secret, pass, accountLocked, accountFailedAttempts FROM webUsers WHERE username = '".$user."' LIMIT 0,1");
$checkPass = mysql_fetch_object($checkPassQ);
if (isset($checkPass->id)) { $userExists = $checkPass->id; }

//Check if user exists before checking login data
if(isset($userExists)){
	//Check to see if this user has an `accountLocked`
	if($checkPass->accountLocked < time()){
		//Check to see if this user has attempted to login more then the maximum allowed failed attempts
		if($checkPass->accountFailedAttempts < 5){
			$dbHash = $checkPass->pass;
			$inputHash = hash("sha256", $pass.$salt);
			//Do Check
			if($dbHash == $inputHash){
				//Give out the secrect SHHH!!
				//Get ip address so we can hash with the cookie so no one can steal the password
				$ip = $_SERVER['REMOTE_ADDR'];
				$timeoutStamp = time()+60*60*24*7; //1 week session
				//Update logged in ip address so no one can steal this cookie hash unless
				mysql_query("UPDATE `webUsers` SET `sessionTimeoutStamp` = ".$timeoutStamp.", `loggedIp` = '".$ip."' WHERE `id` = ".$userExists);

				//Set cookie in browser for session
				$hash		= $checkPass->secret.$dbHash.$ip.$timeoutStamp;
				$cookieHash = hash("sha256", $hash.$salt);
				setcookie($cookieName, $checkPass->id."-".$cookieHash, $timeoutStamp, $cookiePath, $cookieDomain);
				$cookieValid = true;

				//Display output message
				$outputMessage = "Login Successful.";
				header("Location: news");

			}else{
				$outputMessage =  "Wrong username or password.";
				sleep(2);
				header("Location: index");
			}
		}
	}
}else{
	$outputMessage = "User name doesn't exist.";
	sleep(2);
	header("Location: index");
}

//sleep(1);
//header("Location: news");

?>
