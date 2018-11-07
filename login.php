<?php
	include('includes/header.php');
	
	if(!isset($_SESSION['name'])){
		if(isset($_POST['username']) && isset($_POST['password'])){		
			//Check user in database	
			include('includes/database.php');	
			$sql = 'SELECT * FROM users WHERE username = "'.strip_tags($_POST['username']) .'" AND password = "'. strip_tags($_POST['password']) .'"';
			
			$result = mysqli_query($conn, $sql);		
			
			if (mysqli_num_rows($result) > 0) {
				while($row = mysqli_fetch_assoc($result)) {			
					$_SESSION['user_id'] = $row['id'];
					$_SESSION['name'] = $row['username'];
					$_SESSION['admin'] = $row['admin'];
					
					//If user wants autologin, add/update Cookie. 
					if(isset($_POST['remember'])){
						$cookie = md5(uniqid('', true));
						$sql = 'SELECT * FROM sessions WHERE username = "'. $row['username'] .'"';
						$next_result = mysqli_query($conn, $sql);

						//Update sessions id if user exists in db
						if (mysqli_num_rows($next_result) > 0) {						
							if ($stmt = mysqli_prepare($conn, 'UPDATE sessions SET session_id = ? WHERE username = "'. $row['username'] .'"')) {
								
								mysqli_stmt_bind_param($stmt, 's', $cookie);
								
								mysqli_stmt_execute($stmt);
								
								mysqli_stmt_close($stmt);
								
								setcookie('session', $cookie, time() + (86400 * 30)); //1 month
								
							}
							else
								echo "Det gick inte att ändra sessions_id.";
						}
						//Insert sessions id if user missing from db
						else if ($stmt = mysqli_prepare($conn, 'INSERT INTO sessions (session_id, username) VALUES (?, ?)')) {
							mysqli_stmt_bind_param($stmt, 'ss', $cookie, $row['username']);
							
							mysqli_stmt_execute($stmt);
							
							mysqli_stmt_close($stmt);
							
							setcookie('session', $cookie, time() + (86400 * 30)); //1 month	
						}
						else
							echo "Det gick inte att lägga till sessions_id.";					
										
					}
					
					if(isset($_GET['redirect_after_login'])){
						if(isset($_SESSION['redirect_after_login']))
							unset($_SESSION['redirect_after_login']);
						
						header("location: " . $_GET['redirect_after_login']);
					}
					else if(isset($_SESSION['redirect_after_login'])){
						$url = $_SESSION['redirect_after_login'];
						unset($_SESSION['redirect_after_login']);
						header("location: $url");					
					}
					else
						header("location: index.php");				
				} 
			}
			else 
				echo "Inloggning misslyckad.";
		
			mysqli_close($conn);
		}
				
		$html = file_get_contents('templates/login_form_template.html');

		$username = "";
		$password = "";
		
		$html = str_replace('---$username---', $username, $html);
		$html = str_replace('---$password---', $password, $html);
			
		echo $html;			
		
		include('includes/footer.html');
	}
?>
