<?php
	session_start();

	$items_in_cart = false;
	$admin = false;
	$customer_not_loggedin = false;
	$customer_loggedin = false;
	
	if(isset($_SESSION['cart'])){
		if(count($_SESSION['cart']) > 0)
			$items_in_cart = true;
		else	
			unset($_SESSION['cart']);
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
		if(isset($_GET['id']) && isset($_GET['action'])){
			if($_GET['action'] == 'sub'){
				//Remove item if exists.				
				if($_SESSION['cart'][$_GET['id']] > 0)
					$_SESSION['cart'][$_GET['id']]--;	//The product id is kept even if items are zero in case customer wants to add to it again in register
			}
			else if($_GET['action'] == 'add'){
				$_SESSION['cart'][$_GET['id']]++;
			}
			else if($_GET['action'] == 'del'){
				unset($_SESSION['cart'][$_GET['id']]);
				//Remove cart if empty
				if(count($_SESSION['cart']) == 0)
					unset($_SESSION['cart']);
			}
			
			header("location: shop.php");
		}
		else
			header("location: shop.php?message=update_failed");
		
	}	
	else
		header("location: shop.php?message=update_failed");	
?>