<?php
		session_start();
		
		if(isset($_SESSION['name']))
		{
			unset($_SESSION['name']);
			unset($_SESSION['admin']);
			unset($_SESSION['user_id']);
			if(isset($_COOKIE['session'])){
				unset($_COOKIE['session']);				
				$res = setcookie('session', '', time() - 3600); // empty value and expire one hour before
			}
			if(isset($_SESSION['redirect_after_login']))
				unset($_SESSION['redirect_after_login']);
		}
		header("location: index.php");
?>
	