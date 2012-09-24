<?php

	//Include site functions
	include("includes/requiredFunctions.php");

	setcookie($cookieName, 0, 0, $cookiePath, $cookieDomain);
	header("Location: /index");

?>
