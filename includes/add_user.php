<?php
	//Prepare message templates
	$message_html = file_get_contents('templates/message_template.html');
	$failed_message_html = file_get_contents('templates/failed_message_template.html');
	
	//To be set from form
	$username = '';
	$password = ''; 
	$first_name = ''; 
	$last_name = '';
	$adress = '';
	$postal = '';
	$city = '';
	
	//No admin right as default for new users
	$admin = 0;	
	
	//If new user form is filled in and posted, create new user if not username exists in db
	if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['adress']) && isset($_POST['postal']) && isset($_POST['city'])){
		include('includes/database.php');
		
		$username = strip_tags($_POST['username']);
		$password = strip_tags($_POST['password']); 
		$first_name = strip_tags($_POST['first_name']); 
		$last_name = strip_tags($_POST['last_name']);
		$adress = strip_tags($_POST['adress']);
		$postal = strip_tags($_POST['postal']);
		$city = strip_tags($_POST['city']);		
		
		//Check for duplicate username
		$duplicate_exists = false;
		$sql = 'SELECT * FROM users WHERE username = "'. $username .'"';
		$result = mysqli_query($conn, $sql);		
		
		if (mysqli_num_rows($result) > 0) {
			while($row = mysqli_fetch_assoc($result)) {			
				$duplicate_exists = true;	
			}
			
			if($duplicate_exists){
				$failed_message_html = str_replace('---$message---', "Användarnamnet finns redan. Var vänlig välj ett annat.", $failed_message_html);
				echo $failed_message_html;			
				
				//Keep input in form if duplicate username exists
				$html = file_get_contents('templates/add_user_form_template.html');
				$html = str_replace('---$username---', $username, $html);
				$html = str_replace('---$password---', $password, $html);
				$html = str_replace('---$first_name---', $first_name, $html);
				$html = str_replace('---$last_name---', $last_name, $html);
				$html = str_replace('---$adress---', $adress, $html);
				$html = str_replace('---$postal---', $postal, $html);
				$html = str_replace('---$city---', $city, $html);

				echo $html;	
			}
		} 
		else if ($stmt = mysqli_prepare($conn, 'INSERT INTO users (username, password, first_name, last_name, adress, postal, city, admin) VALUES (?, ?, ?, ?, ?, ?, ?, ?)')) {

			mysqli_stmt_bind_param($stmt, 'ssssssss', $username, $password, $first_name, $last_name, $adress, $postal, $city, $admin);

			mysqli_stmt_execute($stmt);

			mysqli_stmt_close($stmt);
			
			header("location: login.php");	
		}
		else
			echo "Det gick inte att lägga till en ny användare.";
		
		mysqli_close($conn);		
	}
	else if(isset($_SESSION['name'])){//If user is logged in, show user information	
		$message_html = str_replace('---$message---', "Du är inloggad.", $message_html);
		echo $message_html;
		include('includes/user_information.php');
	}
	else{//Show new user form		
		$html = file_get_contents('templates/add_user_form_template.html');
		$html = str_replace('---$username---', $username, $html);
		$html = str_replace('---$password---', $password, $html);
		$html = str_replace('---$first_name---', $first_name, $html);
		$html = str_replace('---$last_name---', $last_name, $html);
		$html = str_replace('---$adress---', $adress, $html);
		$html = str_replace('---$postal---', $postal, $html);
		$html = str_replace('---$city---', $city, $html);

		echo $html;
	}	
?>