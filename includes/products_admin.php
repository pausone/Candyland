<?php	
	if(isset($_SESSION['admin']))
	{	
		if($_SESSION['admin'])
		{
			//Prepare message templates
			$message_html = file_get_contents('templates/message_template.html');
			$failed_message_html = file_get_contents('templates/failed_message_template.html');
			
			$name = "";
			$sales_price = ""; 
			$category = "";
			$orig_price = "";
			$filename = "";
			$in_stock = "";
			$edit_id = "";			
			
			//Change depending on Insert or Update
			$heading = "Lägg till produkt";
			$visibility = "hide";
			
			include('includes/database.php');
			
			//If product form is filled, create new product
			if(isset($_POST['product_name']) && isset($_POST['sales_price']) && isset($_POST['category']) && isset($_POST['original_price']) && isset($_POST['image_filename']) && isset($_POST['in_stock'])){

				$name = strip_tags($_POST['product_name']);
				$sales_price = strip_tags($_POST['sales_price']); 
				$category = strip_tags($_POST['category']);
				$original_price = strip_tags($_POST['original_price']);
				$image_filename = strip_tags($_POST['image_filename']);
				$in_stock = strip_tags($_POST['in_stock']);	
	
				if(isset($_GET['id'])){//Update product
					if ($update = mysqli_prepare($conn, "UPDATE products SET name = '$name', sales_price = $sales_price, category = '$category', original_price = $original_price, image_filename = '$image_filename', in_stock = $in_stock WHERE id = " . $_GET['id'])) {
						
						mysqli_stmt_bind_param($update, 'sdsdsd', $name, $sales_price, $category, $original_price, $image_filename, $in_stock);
						
						mysqli_stmt_execute($update);
						
						mysqli_stmt_close($update);
						
						$name = "";
						$sales_price = ""; 
						$category = "";
						$orig_price = "";
						$filename = "";
						$in_stock = "";	
						
						header("location: index.php?id=".$_GET['id']."&update=success");
					}
					else
						header("location: index.php?id=".$_GET['id']."&update=failed");
				}
				else{//Add product
					if ($insert = mysqli_prepare($conn, 'INSERT INTO products (name, sales_price, category, original_price, image_filename, in_stock) VALUES (?, ?, ?, ?, ?, ?)')) {
						mysqli_stmt_bind_param($insert, 'sdsdsd', $name, $sales_price, $category, $original_price, $image_filename, $in_stock);

						mysqli_stmt_execute($insert);

						mysqli_stmt_close($insert);
						
						$name = "";
						$sales_price = ""; 
						$category = "";
						$orig_price = "";
						$filename = "";
						$in_stock = "";	
						
						header("location: index.php?add=success");
					}
					else
						header("location: index.php?add=failed");
				}
			}
			
			if(isset($_GET['id'])){
				$sql = 'SELECT * FROM products WHERE id = ' . $_GET['id'];
				$result = mysqli_query($conn, $sql);
				
				//Show product
				if (mysqli_num_rows($result) > 0) {
					while($row = mysqli_fetch_assoc($result)) {
						
						$name = $row["name"];
						$sales_price = $row["sales_price"]; 
						$category = $row["category"];
						$orig_price = $row["original_price"];
						$filename = $row["image_filename"];
						$in_stock = $row["in_stock"];	
					}
				} else {
					echo "0 resultat";
				}
				
				$heading = "Uppdatera produkt";
				$visibility = "";
				$edit_id = '?id=' . $_GET['id']; 
			}	
			
			
			//Show message if exists
			if(isset($_GET['update'])){
				if($_GET['update'] == 'failed'){
					$failed_message_html = str_replace('---$message---', 'Uppdatering misslyckades', $failed_message_html);
					echo $failed_message_html;					
				}
					else{						
						$message_html = str_replace('---$message---', 'Uppdatering lyckades!', $message_html);
						echo $message_html;
					}
			}
			else if(isset($_GET['add'])){
				if($_GET['add'] == 'failed'){
					$failed_message_html = str_replace('---$message---', 'Lägg till produkt misslyckades', $failed_message_html);
					echo $failed_message_html;					
				}
					else{						
						$message_html = str_replace('---$message---', 'Lägg till produkt lyckades!', $message_html);
						echo $message_html;
					}
			}
			
			//Add heading text
			$html_heading = file_get_contents('templates/heading_2_template.html');
			$html_heading = str_replace('---$heading_2---', $heading, $html_heading);

			echo $html_heading;
			
			//Add product form and products information
			$html = file_get_contents('templates/products_admin_template.html');
			$html_pieces = explode('<!--==xxx==-->', $html);
			
			//Set Get variable if needed
			$html_pieces[0] = str_replace('---$edit_id---', $edit_id, $html_pieces[0]);			
			
			$html_pieces[0] = str_replace('---$name---', $name, $html_pieces[0]); 
			$html_pieces[0] = str_replace('---$sales_price---', $sales_price, $html_pieces[0]);
			$html_pieces[0] = str_replace('---$category---', $category, $html_pieces[0]);
			$html_pieces[0] = str_replace('---$orig_price---', $orig_price, $html_pieces[0]);
			$html_pieces[0] = str_replace('---$filename---', $filename, $html_pieces[0]);
			$html_pieces[0] = str_replace('---$in_stock---', $in_stock, $html_pieces[0]);			
			
			//Set visibility for Add product link
			$html_pieces[0] = str_replace('---$visibility---', $visibility, $html_pieces[0]);

			echo $html_pieces[0];
			
			$tmp_html_piece = $html_pieces[1];			

			//Get products info from database
			$sql = 'SELECT * FROM products';
			$result = mysqli_query($conn, $sql);
			
			//Show products
			if (mysqli_num_rows($result) > 0) {
				while($row = mysqli_fetch_assoc($result)) {
					$tmp_html_piece = str_replace('---$id---', $row["id"], $tmp_html_piece);
					$tmp_html_piece = str_replace('---$name---', $row["name"], $tmp_html_piece); 
					$tmp_html_piece = str_replace('---$sales_price---', $row["sales_price"], $tmp_html_piece);
					$tmp_html_piece = str_replace('---$category---', $row["category"], $tmp_html_piece);
					$tmp_html_piece = str_replace('---$orig_price---', $row["original_price"], $tmp_html_piece);
					$tmp_html_piece = str_replace('---$filename---', $row["image_filename"], $tmp_html_piece);
					$tmp_html_piece = str_replace('---$in_stock---', $row["in_stock"], $tmp_html_piece);
					
					echo $tmp_html_piece;
					
					$tmp_html_piece = $html_pieces[1];
				}
			} else {
				echo "0 resultat";
			}

			mysqli_close($conn);
		}
	}
?>