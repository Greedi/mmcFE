<?php

include ("includes/templates/header.php");

if(!$cookieValid) {
	header('Location: /');
	exit;
}
//Execute the following based on what $_POST["act"] is set to
$returnError = "";
$goodMessage = "";
if (isset($_POST["act"])) {
	$act = $_POST["act"];

	if($act == "addWorker"){
		//Add worker
		$prefixUsername = $userInfo->username;
		$inputUser = $prefixUsername.".".mysql_real_escape_string($_POST["username"]);
		$inputPass = mysql_real_escape_string($_POST["pass"]);

		//Check if username already exists
		$usernameExistsQ = mysql_query("SELECT `id` FROM `pool_worker` WHERE `associatedUserId` = ".$userId." AND `username` = '".$inputUser."'");
		$usernameExists = mysql_num_rows($usernameExistsQ);

		if($usernameExists == 0){
			$addWorkerQ = mysql_query("INSERT INTO `pool_worker` (`associatedUserId`, `username`, `password`) VALUES('".$userId."', '".$inputUser."', '".$inputPass."')");
			if($addWorkerQ){
				$goodMessage = "Worker successfully added!";
			}else if(!$addWorkerQ){
				$returnError = "Database Error - Worker was not added :(";
			}
		}else if($usernameExists == 1){
			$returnError = "Worker Name already Exists. Try a different Worker Name.";
		}
	}

	if($act == "Update Worker"){

		//Mysql Injection Protection
		$workerId = mysql_real_escape_string($_POST["workerId"]);
		$workernum = mysql_real_escape_string($_POST["workernum"]);
		$password = mysql_real_escape_string($_POST["password"]);

		$prefixUsername = $userInfo->username;
		$inputUser = $prefixUsername.".".mysql_real_escape_string($_POST["workernum"]);

                //Check if username already exists
                $usernameExistsQ = mysql_query("SELECT `id` FROM `pool_worker` WHERE `associatedUserId` = ".$userId." AND `username` = '".$inputUser."'");
                $usernameExists = mysql_num_rows($usernameExistsQ);

                if($usernameExists >= 1) {
			// Username already exists - Only allow password update
			mysql_query("UPDATE pool_worker SET password = '$password' WHERE id = '$workerId' AND associatedUserId = '$userId'");
			$goodMessage = "Worker password updated.";

                } else {
			//update both
			mysql_query("UPDATE pool_worker SET username = '$inputUser', password = '$password' WHERE id = '$workerId' AND associatedUserId = '$userId'");
			$goodMessage = "Worker updated.";
		}
	}


	if($act == "Delete Worker"){

		//Mysql Injection Protection
		$workerId = mysql_real_escape_string($_POST["workerId"]);

		//Delete worker OH NOES!
		mysql_query("DELETE FROM pool_worker WHERE id = '$workerId' AND associatedUserId = '$userId'");
		$goodMessage = "Worker successfully deleted.";
	}
}

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

                <div class="block" style="clear:none;">
                 <div class="block_head">
                  <div class="bheadl"></div>
                  <div class="bheadr"></div>
                  <h2>Worker Accounts</h2>
                 </div>

                 <div class="block_content" style="padding:10px;">

<font color="red">
<ul><li>
CAUTION! </font>Deletion of a worker could cause all associated shares for that worker to be lost.
Do not delete Workers unless you are certain all of their shares have been counted or that you have never used that worker account.
</li></ul>
</font>

<center>
<table border="0" cellpadding="3" cellspacing="3">
<tr><td>Worker Name</td><td>Password</td><td>Active</td><td>Mhash/s</td><td>&nbsp;</td><td>&nbsp;</td></tr>
<?php
//Get list of workers from the associatedUserId
$getWorkers = mysql_query("SELECT `id`, `username`, `password`, active, hashrate FROM `pool_worker` WHERE `associatedUserId` = '".$userId."'");
while($worker = mysql_fetch_array($getWorkers)){
?>
<form action="/accountworkers" method="post">
<input type="hidden" name="workerId" value="<?php echo antiXss($worker["id"]); ?>">
<?php
//Display worker information and the forms to edit or update them
$splitUsername = explode(".", $worker["username"]);
$realUsername = $splitUsername[1];
?>
<tr>

<td <?php if ($worker["active"] == 0) { ?>style="color: orange"<?php } ?>><?php echo antiXss($userInfo->username); ?>.<input type="text" name="workernum" value="<?php echo antiXss($realUsername); ?>" size="10"></td>
<td><input type="text" name="password" value="<?php echo antiXss($worker["password"]);?>" size="10"></td>
<td><?php if ($worker["active"] == 1) echo "Y"; else echo "N"; ?>
<td><?php echo antiXss($worker["hashrate"])?></td>
<td><input type="submit" name="act" value="Update Worker" style="padding:5px;"></td>
<td><input type="submit" name="act" value="Delete Worker" style="padding:5px;"></td>
</tr>
</form>
<?php
}
?>
</table>
</center>

<!-- Add new Worker -->
<p><center><h2>Add a New Worker</h2>
<form action="/accountworkers" method="post"><input type="hidden"
	name="act" value="addWorker"><!--  AuthPin:<input type="password"
	name="authPin" size="4" maxlength="4"><br /> -->
<?php echo antiXss($userInfo->username);?>.<input type="text" name="username"
	value="user" size="10" maxlength="20"> &middot; <input type="text"
	name="pass" value="pass" size="10" maxlength="20"> <input type="submit"
	value="Add New Worker" style="padding:5px;"></form>
</p></center>
                </div>          <!-- nested block ends -->
                <div class="bendl"></div>
                <div class="bendr"></div>
                </div>

                </div>          <!-- .sidebar_content ends -->


        </div>          <!-- .block_content ends -->




        <div class="bendl"></div>
        <div class="bendr"></div>

</div>          <!-- .block ends -->

<?php include ("includes/templates/footer.php");?>
