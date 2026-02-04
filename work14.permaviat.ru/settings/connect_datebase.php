<?php
	$mysqli = new mysqli('127.0.0.1', 'root', '', 'security');

	function getClientIp(){
		if(!empty($_SERVER['HTTP_CLIENT_IP'])){
			return $_SERVER['HTTP_CLIENT_IP'];
		}elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
			$ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
			return trim($ips[0]);
		}else{
			return $_SERVER['REMOTE_ADDR'];
		}
	}

	$user_ip = getClientIp();

	setcookie("IP", $user_ip);
	setcookie("DateTime", date("Y-m-d H:i:s"));

	$NowDate = date("Y-m-d H:i:s");
	$Sql = "SELECT * FROM `access_ip` WHERE `ip` = '$user_ip';";
	$QueryAccess = $mysqli->query($Sql);
	if($QueryAccess->num_rows > 0){
		$ReadAccess = $QueryAccess->fetch_assoc();
		$EndDate = $ReadAccess["endDate"];
		$StartDate = $ReadAccess["startDate"];

		if($StartDate==$EndDate){
			echo md5(md5(-1));
			exit;
		}else{
			$Sql = "UPDATE `access_ip` SET `startDate`='$EndDate', `endDate` = '$NowDate' WHERE `ip` = '$user_ip'";
			$mysqli->query($Sql);
		}
	}else{
		$Sql = "INSERT INTO `access_ip`(`ip`, `startDate`, `endDate`) VALUES ('$user_ip', NULL,'$NowDate')";
		$mysqli->query($Sql);
	}
?>