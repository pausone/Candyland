<?php
	include('includes/header.php');
	
	$message_html = file_get_contents('templates/message_template.html');		
	$message_html = str_replace('---$message---', 'Order skapad!', $message_html);
	echo $message_html;
	
	include('includes/footer.html');
?>