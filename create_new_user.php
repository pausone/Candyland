<?php
	include('includes/header.php');
	
	$_SESSION['redirect_after_login'] = "create_new_user.php"; 

	$message_html = file_get_contents('templates/message_template.html');
	
	if(!isset($_POST['username']) && !isset($_SESSION['name'])){		
		$message_html = str_replace('---$message---', "Skapa ny användare.", $message_html);
		echo $message_html;
	}
	
	include('includes/add_user.php');
	include('includes/footer.html');	
?>