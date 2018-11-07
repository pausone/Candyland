<?php
	include('includes/header.php');

	$items_in_cart = false;
	$admin = false;
	$customer_not_loggedin = false;
	$customer_loggedin = false;
	$sum = 0;

	
	if(isset($_SESSION['cart'])){
		$items_in_cart = true;
	}
	
	if(isset($_SESSION['admin'])){
		if($_SESSION['admin'])
			$admin = true;
		else
			$customer_loggedin = true;
	}
	else
		$customer_not_loggedin = true;
	
	
	//Check that user is a customer and there is something in the cart
	if(!$admin && $items_in_cart && ($customer_loggedin || $customer_not_loggedin))
	{		
		include('includes/database.php');
		
		//Check and handle possible error message
		if(isset($_GET['message'])){
			$message_html = file_get_contents('templates/failed_message_template.html');
			$message = "";
			
			if($_GET['message'] == 'low_stock')
				$message = "Inte tillräckligt på lagret.";
			else if($_GET['message'] == 'failed_order')
				$message = "Det gick inte att lägga till en ny order.";
			else if($_GET['message'] == 'update_failed')
				$message = "Det gick inte att uppdatera varukorgen.";
			else if($_GET['message'] == 'admin')
				$message = "Admin kan inte skapa orders.";
			else if($_GET['message'] == 'empty_cart')
				$message = "Varukorgen är tom.";
			else if($_GET['message'] == 'not_logged_in')
				$customer_loggedin = false;
			
			$message_html = str_replace('---$message---', $message, $message_html);
			
			if($message != "")
				echo $message_html;

		}	
		
		//First and last html piece should only be printed if needed and only once 
		$print_first_piece = true; //Only checked if stock is low. Set to false after being printed.
		$print_last_piece = false; //Set to true if stock is low
		
		//Prepare template for table			
		$html = file_get_contents('templates/low_stock_template.html');
		$html_pieces = explode('<!--==xxx==-->', $html);
		$tmp_html_piece = $html_pieces[1];	
		
		//Check if all products are available in stock 
		foreach($_SESSION['cart'] as $id => $quantity){
			//Get current number of items in stock for product
			$sql = "SELECT name, in_stock FROM products WHERE id = $id";
			$result = mysqli_query($conn, $sql);		
			
			if (mysqli_num_rows($result) > 0) {
				while($row = mysqli_fetch_assoc($result)) {						
					if($row['in_stock'] < $quantity){
						
						//Print first piece once if stock is low
						if($print_first_piece){		
							echo $html_pieces[0];						
							$print_first_piece = false; //Set to false since printed once
							$print_last_piece = true; //Last piece needed later
						}
						
						$tmp_html_piece = str_replace('---$id---', $row['name'], $tmp_html_piece);
						$tmp_html_piece = str_replace('---$in_stock---', $row['in_stock'], $tmp_html_piece);
						$tmp_html_piece = str_replace('---$quantity---', $quantity, $tmp_html_piece);
						echo $tmp_html_piece;
						$tmp_html_piece = $html_pieces[1];
					}
				}
			} 
			else 
				echo "Lagersaldo ej funnet.";
		}
		
		//Print last piece if items are missing from stock
		if($print_last_piece)
			echo $html_pieces[2];
		
		$html = file_get_contents('templates/cart_table_template.html');
		$html_pieces = explode('<!--==xxx==-->', $html);
		$tmp_html_piece = $html_pieces[1];
		
		echo $html_pieces[0];
		
		include('includes/database.php');
		
		foreach($_SESSION['cart'] as $id => $quantity){
			//Replace quantity in template with info from cart			
			$tmp_html_piece = str_replace('---$quantity---', $quantity, $tmp_html_piece);					
			
			//Get product info from database
			$sql = 'SELECT * FROM products WHERE id = '. $id;
 			$result = mysqli_query($conn, $sql);
			
			if (mysqli_num_rows($result) > 0) {
				while($row = mysqli_fetch_assoc($result)) {
					$tmp_html_piece = str_replace('---$name---', $row['name'], $tmp_html_piece);
					$tmp_html_piece = str_replace('---$quantity---', $quantity, $tmp_html_piece);					
					$tmp_html_piece = str_replace('---$id---', $id, $tmp_html_piece);
					$tmp_html_piece = str_replace('---$sales_price---', $row['sales_price'], $tmp_html_piece);
					$tmp_html_piece = str_replace('---$sum_post---', $row['sales_price']*$quantity, $tmp_html_piece);

					$sum += $row['sales_price']*$quantity;
				}
			}		
			
			echo $tmp_html_piece;
			$tmp_html_piece = $html_pieces[1];
		}
		mysqli_close($conn);
		
		$html_pieces[2] = str_replace('---$sum---', $sum, $html_pieces[2]);
		echo $html_pieces[2];
		
		//If user is logged in, show user information, otherwise show new user form
		if($customer_loggedin)
		{
			include('includes/user_information.php');
		}
		else{ 
			if(!isset($_POST['username']))		
				include('includes/customer_message.html');				
			$_SESSION['redirect_after_login'] = "shop.php";
			include('includes/add_user.php');
		}		
	}	
	else
		header("location: index.php");
	
	include('includes/footer.html');	
	
?>