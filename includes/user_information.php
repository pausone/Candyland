<?php
	$items_in_cart = false;
	$admin = false;
	$customer_not_loggedin = false;
	$customer_loggedin = false;
	$sum = 0;
	
	if(isset($_SESSION['cart']))
		$items_in_cart = true;
	
	if(isset($_SESSION['admin'])){
		if($_SESSION['admin'])
			$admin = true;
		else
			$customer_loggedin = true;
	}
	else
		$customer_not_loggedin = true;
	
	if(isset($_SESSION['name']))
	{
		$html = file_get_contents('templates/user_information_template.html');
		
		include('includes/database.php');				
		
		$sql = "SELECT * FROM users WHERE username = '". $_SESSION['name'] . "'";
		$result = mysqli_query($conn, $sql);
		
		//Show user information
		if (mysqli_num_rows($result) > 0) {
			while($row = mysqli_fetch_assoc($result)) {
				$html = str_replace('---$username---', $row['username'], $html);
				$html = str_replace('---$first_name---', $row['first_name'], $html);
				$html = str_replace('---$last_name---', $row['last_name'], $html);
				$html = str_replace('---$adress---', $row['adress'], $html);
				$html = str_replace('---$postal---', $row['postal'], $html);
				$html = str_replace('---$city---', $row['city'], $html);				
			}
		}
		else
			echo "Kund ej funnen";
		
		echo $html;
		mysqli_close($conn);		
	}	
	else
		header("location: index.php");
?>