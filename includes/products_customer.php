<?php	
	if(!isset($_SESSION['admin']) || !($_SESSION['admin']))
	{	
		$message = "";
		$message_for_id = "";
		
		if(isset($_GET['message']) && isset($_GET['id'])){
			$message = $_GET['message'];
			$message_for_id = $_GET['id'];
		}		
		
		include('includes/database.php');
		
		$sql = 'SELECT * FROM products';
		$result = mysqli_query($conn, $sql);
		
		$html = file_get_contents('templates/products_customer_template.html');
		$html_pieces = explode('<!--==xxx==-->', $html);
		
		//Show products
		if (mysqli_num_rows($result) > 0) {
			while($row = mysqli_fetch_assoc($result)) {
				$html_pieces[0] = str_replace('---$image---', $row["image_filename"], $html_pieces[0]);
				$html_pieces[0] = str_replace('---$id---', $row["id"], $html_pieces[0]);
				$html_pieces[0] = str_replace('---$name---', $row["name"], $html_pieces[0]); 
				$html_pieces[0] = str_replace('---$sales_price---', $row["sales_price"], $html_pieces[0]);
				$html_pieces[0] = str_replace('---$category---', $row["category"], $html_pieces[0]);
				$html_pieces[0] = str_replace('---$in_stock---', $row["in_stock"], $html_pieces[0]);
				
				if($row["in_stock"] < 1)
					$html_pieces[1] = "Slut i lager";
				else if(isset($_SESSION['cart'][$row["id"]]) && ($_SESSION['cart'][$row["id"]] >= $row["in_stock"]))
					$html_pieces[1] = "Produkten är tillagd varukorgen. Du kan tyvärr inte handla fler pga lagersaldot.";
				else{	
					$html_pieces[1] = str_replace('---$id---', $row["id"], $html_pieces[1]);
					
					if($row["id"] == $message_for_id)
						$html_pieces[1] = str_replace('---$message---', $message, $html_pieces[1]);
					else
						$html_pieces[1] = str_replace('---$message---', '', $html_pieces[1]);
				}
				
				echo $html_pieces[0];
				echo $html_pieces[1];
				echo $html_pieces[2];
				
				$html_pieces = explode('<!--==xxx==-->', $html);
			}
		} else {
			echo "0 resultat";
		}

		mysqli_close($conn);
	}
?>