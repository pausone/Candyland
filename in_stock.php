<?php	
	include('includes/header.php');

	if(isset($_SESSION['admin']) && $_SESSION['admin']){		
		include('includes/database.php');
		
		if(isset($_POST['id']) && $_POST['add_quantity']){		
			//Get current number of items in stock for product
			$sql = "SELECT * FROM products WHERE id = " . strip_tags($_POST['id']);
				$result = mysqli_query($conn, $sql);		
				
				if (mysqli_num_rows($result) > 0) {
					while($row = mysqli_fetch_assoc($result)) {			
						$in_stock = $row['in_stock'];
					}
				} else {
					echo "Lagersaldo ej funnet.";
				}				

			//Update number of items in stock for product
			if ($stmt = mysqli_prepare($conn, "UPDATE products SET in_stock = ? WHERE id = " . strip_tags($_POST['id']))) {
				
				mysqli_stmt_bind_param($stmt, 'd', $new_number);
				
				$new_number = $in_stock + strip_tags($_POST['add_quantity']);
				
				mysqli_stmt_execute($stmt);
				mysqli_stmt_close($stmt);
			}
			else
				echo "Det gick inte att ändra lagersaldo.";
		}		

		//Get products info from database
		$sql = 'SELECT * FROM products';
		$result = mysqli_query($conn, $sql);
		
		$html = file_get_contents('templates/in_stock_template.html');
		$html_pieces = explode('<!--==xxx==-->', $html);
		$tmp_html_piece = $html_pieces[1];			

		echo $html_pieces[0];
		
		//Show products
		if (mysqli_num_rows($result) > 0) {
			while($row = mysqli_fetch_assoc($result)) {
				$tmp_html_piece = str_replace('---$id---', $row["id"], $tmp_html_piece);
				$tmp_html_piece = str_replace('---$name---', $row["name"], $tmp_html_piece); 
				$tmp_html_piece = str_replace('---$in_stock---', $row["in_stock"], $tmp_html_piece);
				
				echo $tmp_html_piece;
				
				$tmp_html_piece = $html_pieces[1];
			}
		} else {
			echo "0 resultat";
		}

		mysqli_close($conn);
		
		echo $html_pieces[2];
		include('includes/footer.html');
	}
	else
		header("location: index.php");		
?>