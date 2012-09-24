<?php
/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
Website Reference:http://www.gnu.org/licenses/gpl-2.0.html
*/

// RPC Bitcoind Credentials
//
$rpcType = "http"; 				// http or https
$rpcUsername = ""; 				// username as specified in your bitcoin.conf configuration file
$rpcPassword = ""; 				// password
$rpcHost = "localhost:9332";


// MySql Credentials
//
$dbHost = "localhost";
$dbUsername = "";
$dbPassword = "";
$dbPort = "3306";
$dbDatabasename = "";

// Cookie settings (more info @http://us.php.net/manual/en/function.setcookie.php)
//
$cookieName = ""; 				//Set this to what ever you want (text string)
$cookiePath = "/";				//Choose your path!
$cookieDomain = ".domain.net";			//Set this to your domain

include("bitcoinController/bitcoin.inc.php");	// Dont touch.

// Salt & Pretzels
$salt = "LJKEHFuhgu7%&¤Hg783tr7gf¤%¤fyegfredfoGHYFGYe(%/(&%6"; 	// random series of numbers and letters; set it to anything or any length you want.
$cookieValid = false; 				// leave as: false

connectToDb();
include('settings.php');

$settings = new Settings();

//						//
//--------- End Configuration Section ----------//
//						//



function connectToDb(){
	//Set variables to global retireve outside of the scope
	global $dbHost, $dbUsername, $dbPassword, $dbDatabasename;

	//Connect to database
	mysql_connect($dbHost, $dbUsername, $dbPassword)or die(mysql_error());
	mysql_select_db($dbDatabasename);
}

class checkLogin
{
	function checkCookie($input, $ipaddress){
		global $salt;
		connectToDb();
		/*$input comes in the following format userId-passwordhash

		/*Validate that the cookie hash meets the following criteria:
			Cookie Ip: matches $ipaddres;
			Cookie Timeout: Is still greater then the current time();
			Cookie Secret: matches the mysql database secret;
		*/

		//Split cookie into 2 mmmmm!
		$cookieInfo = explode("-", $input);

		//Get "secret" from MySql database
		$getSecretQ	= mysql_query("SELECT secret, pass, sessionTimeoutStamp FROM webUsers WHERE id = ".mysql_real_escape_string($cookieInfo[0])." LIMIT 0,1");
		$getSecret	= mysql_fetch_object($getSecretQ);
		if (isset($getSecret->pass)) { $password = $getSecret->pass; }
		if (isset($getSecret->secret)) { $secret = $getSecret->secret; }
		if (isset($getSecret->sessionTimeoutStamp)) { $timeoutStamp = $getSecret->sessionTimeoutStamp;

			//Create a variable to test the cookie hash against
			$hashTest = hash("sha256", $secret.$password.$ipaddress.$timeoutStamp.$salt);

			//Test if $hashTest = $cookieInfo[1] hash value; return results
			$validCookie = false;
			if($hashTest == $cookieInfo[1]){
				$validCookie = true;
			}
			return $validCookie;
		}
	}

	function returnUserId($input){
		//Just split the cookie to get the userId
		$cookieInfo = explode("-", $input);

		return $cookieInfo[0];
	}
}



function outputPageTitle(){
	if (!isset($settings))
	{
		connectToDb();
		$settings = new Settings();
	}
	//Get page title
	return $settings->getsetting("pagetitle");;
}

function outputHeaderTitle(){
	if (!isset($settings))
	{
		connectToDb();
		$settings = new Settings();
	}
	return $settings->getsetting("websitename");
}

function outputSlogan(){
	if (!isset($settings))
	{
		connectToDb();
		$settings = new Settings();
	}
	return $settings->getsetting("slogan");
}

//Helpfull functions
function genRandomString($length=10) {
    $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $string = "";

    for ($p = 0; $p < $length; $p++) {
        $string .= $characters[mt_rand(0, strlen($characters) - 1)];
    }

    return $string;
}


function antiXss($input) {
	//strip HTML tags from input data
	return htmlentities(strip_tags($input), ENT_QUOTES);
}

function account_type($userid) {
        // return account type from DB
        //
        // 0 = normal account no special treatment
        // 9 = early adopter account 0% fees for life

        $q = mysql_fetch_object(mysql_query("SELECT account_type FROM webUsers WHERE id='" .$userid. "'"));

        return $q->account_type;
}

?>
