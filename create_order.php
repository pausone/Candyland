<?php
	session_start();

	$items_in_cart = false;
	$admin = false;
	$customer_loggedin = false;

	//Chack cart content exists
	if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0){
		foreach($_SESSION['cart'] as $id => $quantity){
			if($quantity > 0)
				$items_in_cart = true;
		}
	}		
	
	if(isset($_SESSION['admin'])){
		if($_SESSION['admin'])
			$admin = true;
		else
			$customer_loggedin = true;
	}
	
	//Create order if customer is logged in and there is something in the cart
	if(!$admin && $items_in_cart && $customer_loggedin)
	{		
		$all_in_stock = true; //set to false if something is missing
		
		include('includes/database.php');
		//Check that there is enough i stock
		foreach($_SESSION['cart'] as $id => $quantity){
			//Get current number of items in stock for product
			$sql = "SELECT in_stock FROM products WHERE id = $id";
			$result = mysqli_query($conn, $sql);		
			
			if (mysqli_num_rows($result) > 0) {
				while($row = mysqli_fetch_assoc($result)) {	
					if($row['in_stock'] < $quantity){
						$all_in_stock = false;						
					}
				}
			} else {
				echo "Lagersaldo ej funnet.";
			}
		}
		
		//Create order if all ordered items are in stock
		if(!$all_in_stock){
			header("location: shop.php?message=low_stock");
		}
		else if($stmt = mysqli_prepare($conn, 'INSERT INTO orders (user_id, order_date, products, sent) VALUES (?, ?, ?, ?)')) {
			
			mysqli_stmt_bind_param($stmt, 'sssd', $user_id, $order_date, $products, $sent);
			
			$user_id = $_SESSION['user_id'];
			$order_date = date("Y-m-d"); 
			$products = serialize($_SESSION['cart']); 
			$sent = 0; 
			mysqli_stmt_execute($stmt);
			mysqli_stmt_close($stmt);
			
			//Update items in stock for each purchased product
			foreach($_SESSION['cart'] as $id => $quantity){
				//Get current number of items in stock for product
				$sql = "SELECT * FROM products WHERE id = $id";
					$result = mysqli_query($conn, $sql);		
					
					if (mysqli_num_rows($result) > 0) {
						while($row = mysqli_fetch_assoc($result)) {			
							$in_stock = $row['in_stock'];
						}
					} else {
						echo "Lagersaldo ej funnet.";
					}					

				//Update number of items in stock for product
				if ($stmt = mysqli_prepare($conn, 'UPDATE products SET in_stock = ? WHERE id = '. $id)) {
					
					mysqli_stmt_bind_param($stmt, 'd', $new_number);
					
					$new_number = $in_stock - $quantity;
					
					mysqli_stmt_execute($stmt);
					mysqli_stmt_close($stmt);
				}
				else
					echo "Det gick inte att ändra lagersaldo.";
			}
			
			mysqli_close($conn);
			
			//Empty cart after created order
			if(isset($_SESSION['cart']))
			{
				unset($_SESSION['cart']);
			}
			
			//Show success message
			header("location: success.php");				
		}
		else
			header("location: shop.php?message=failed_order");
		
	}
	else{
		$error = "";
		
		if($admin)
			$error = "admin.";
		else if($items_in_cart == false)
			$error = "empty_cart";
		else if($customer_loggedin == false)
			$error = "not_logged_in";
		
		header("location: shop.php?message=$error");		
	}	
?>