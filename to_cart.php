<?php	
	if(isset($_GET['id']))
	{	
		$itemid = $_GET['id'];
		$itemquantity = 1;
		
		session_start();
		
		if(isset($_SESSION['cart'])){
			if(isset($_SESSION['cart'][$itemid])){
				
				$_SESSION['cart'][$itemid]++;
				
				//Check if enough in stock
				include('includes/database.php');
				$sql = "SELECT in_stock FROM products WHERE id = " . $_GET['id'];
				$result = mysqli_query($conn, $sql);
				
				if (mysqli_num_rows($result) > 0) {
					while($row = mysqli_fetch_assoc($result)) {
						$in_stock = $row["in_stock"];
						
						if($row["in_stock"] < $_SESSION['cart'][$itemid]){
							$message = "OBS! Det går inte att köpa fler. Ej tillräckligt på lager.";
							$_SESSION['cart'][$itemid]--; //Remove item from cart if not enough in stock
						}
						else	
							$message = "Lyckat tillägg i varukorgen.";				
					}
				} 
				else 
					echo "0 resultat";		
			}
			else{
				$_SESSION['cart'][$itemid] = $itemquantity;
				$message = "Lyckat tillägg i varukorgen.";
			}
		}
		else{
			$_SESSION['cart'] = array();

			$_SESSION['cart'][$itemid] = $itemquantity;
			
			$message = "Lyckat tillägg i varukorgen.";
		}
	
	}		
		
	header("location: index.php?message=$message&id=$itemid");
?>