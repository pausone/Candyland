<?php
	include('includes/header.php');	
	
	if(isset($_SESSION['admin']) && $_SESSION['admin'])
	{		
		include('includes/products_admin.php');			
	}	
	else
		include('includes/products_customer.php');

	include('includes/footer.html');
?>

