<?php	
	$admin = "";
	$customer = "";
	$shop = "";
	$login_out = "login.php";
	$login_out_option = "Logga in";	
	
	session_start();
	
	if(!isset($_SESSION['name']) && isset($_COOKIE['session'])){			
		include('includes/database.php');
		//Check sessions-id in database
		$sql = 'SELECT * FROM sessions WHERE session_id = "'. $_COOKIE['session'] .'"';
		$result = mysqli_query($conn, $sql);

		//Log in and update sessions-id if users session exists in db
		if (mysqli_num_rows($result) > 0) {
			while($row = mysqli_fetch_assoc($result)) {		
				$sql = 'SELECT * FROM users WHERE username = "'. $row['username'] .'"';				
				$next_result = mysqli_query($conn, $sql);		
				
				if (mysqli_num_rows($next_result) > 0) {
					while($next_row = mysqli_fetch_assoc($next_result)) {
						//Log in						
						$_SESSION['user_id'] = $next_row['id'];
						$_SESSION['name'] = $next_row['username'];
						$_SESSION['admin'] = $next_row['admin'];
			
						//Update Cookie. 
						if ($stmt = mysqli_prepare($conn, 'UPDATE sessions SET session_id = ? WHERE username = "'. $row['username'] .'"')) {
							
							mysqli_stmt_bind_param($stmt, 's', $cookie);
							
							$cookie = md5(uniqid('', true));							
							
							mysqli_stmt_execute($stmt);
							
							mysqli_stmt_close($stmt);
							
							setcookie('session', $cookie, time() + (86400 * 30)); //1 month
						}
						else
							echo "Det gick inte att ändra sessions_id.";
					}
				}
				else
					echo "Användare ej funnen";
			}
		}
	}
	
	
	//Hide admin options if customer
	if(!isset($_SESSION['admin']) || !$_SESSION['admin'])
	{	
		$admin = " hide";
	}

	//Hide customer options if admin
	if(isset($_SESSION['admin']) && $_SESSION['admin'])
	{	
		$customer = " hide";	
	}

	//Hide shop-link if cart is empty
	if(!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0)
	{	
		$shop = " hide";	
	}	

	//If user is logged in, show option to log out
	if(isset($_SESSION['name']))
	{	
		$login_out = "logout.php"; 
		$login_out_option = "Logga ut";	
	}	
	

	$html = file_get_contents('templates/header_template.html');
	$html_pieces = explode('<!--==xxx==-->', $html);	
	$html_pieces[0] = str_replace('---$customer---', $customer, $html_pieces[0]);
		
	echo $html_pieces[0];	
			
	//If items in cart, add them to dropdown area in menu
	if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0)
	{
		include('includes/database.php');
		
		foreach($_SESSION['cart'] as $id => $quantity){
			$tmp_html_piece = $html_pieces[1]; 
			$sql = 'SELECT * FROM products WHERE id = '. $id;			
			$tmp_html_piece = str_replace('---$quantity---', $quantity, $tmp_html_piece);
			
			$result = mysqli_query($conn, $sql);
			if (mysqli_num_rows($result) > 0) {
				while($row = mysqli_fetch_assoc($result)) {
					$tmp_html_piece = str_replace('---$item---', $row['name'], $tmp_html_piece);
					echo $tmp_html_piece;
				}
			}
		}
		mysqli_close($conn);
	}
	else
	{
		echo "Varukorgen är tom";		
	}
	
	$html_pieces[2] = str_replace('---$customer---', $customer, $html_pieces[2]);
	$html_pieces[2] = str_replace('---$shop---', $shop, $html_pieces[2]);
	$html_pieces[2] = str_replace('---$login_out---', $login_out, $html_pieces[2]);
	$html_pieces[2] = str_replace('---$login_out_option---', $login_out_option, $html_pieces[2]);
	$html_pieces[2] = str_replace('---$admin---', $admin, $html_pieces[2]);
	
	echo $html_pieces[2];
?>