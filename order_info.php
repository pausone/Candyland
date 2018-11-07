<?php	
	include('includes/header.php');
	if(isset($_SESSION['admin']) && $_SESSION['admin']){	
		
		$html = file_get_contents('templates/order_info_template.html');
		$html_pieces = explode('<!--==xxx==-->', $html);
		
		include('includes/database.php');

		//Get orders info from database
		$sql = "SELECT * FROM orders WHERE id = " . $_GET['order'];
		$result = mysqli_query($conn, $sql);
		
		if (mysqli_num_rows($result) > 0) {
			while($row = mysqli_fetch_assoc($result)) {
				$html_pieces[0] = str_replace('---$order_id---', $row['id'], $html_pieces[0]);
				$html_pieces[0] = str_replace('---$user_id---', $row['user_id'], $html_pieces[0]);
				$html_pieces[0] = str_replace('---$order_date---', $row['order_date'], $html_pieces[0]);
				$sent = $row['sent'] ? 'Ja' : 'Nej';
				$html_pieces[0] = str_replace('---$sent---', $sent, $html_pieces[0]);
				
				$user = $row['user_id']; //For getting user information
				$products = unserialize($row['products']); //For getting products information
			}
		}
		else
			echo "Order ej funnen";		
		
		echo $html_pieces[0];
		
		$sum = 0;
		
		//Get products data from database		
		foreach($products as $id => $quantity){
			$tmp_html_piece = $html_pieces[1];
			
			//Replace quantity in template with info from order			
			$tmp_html_piece = str_replace('---$quantity---', $quantity, $tmp_html_piece);					
			
			//Get product info from database
			$sql = 'SELECT * FROM products WHERE id = '. $id;
 			$result = mysqli_query($conn, $sql);			
			
			if (mysqli_num_rows($result) > 0) {
				while($row = mysqli_fetch_assoc($result)) {
					//Replace various info in template with info from database	
					$tmp_html_piece = str_replace('---$name---', $row['name'], $tmp_html_piece);
					$tmp_html_piece = str_replace('---$sales_price---', $row['sales_price'], $tmp_html_piece);					
					$tmp_html_piece = str_replace('---$sum_post---', $row['sales_price']*$quantity, $tmp_html_piece);
					$sum += $row['sales_price']*$quantity;
					
					//Echo modified html piece
					echo $tmp_html_piece;
				}
			}
		}
		
		//Add sum to next piece
		$html_pieces[2] = str_replace('---$sum---', $sum, $html_pieces[2]);
		
		echo $html_pieces[2];
		
		//Get user info from database
		$sql = "SELECT * FROM users WHERE id = $user";
		$result = mysqli_query($conn, $sql);
		
		if (mysqli_num_rows($result) > 0) {
			while($row = mysqli_fetch_assoc($result)) {	
				$html_pieces[3] = str_replace('---$username---', $row['username'], $html_pieces[3]);
				$html_pieces[3] = str_replace('---$first_name---', $row['first_name'], $html_pieces[3]);
				$html_pieces[3] = str_replace('---$last_name---', $row['last_name'], $html_pieces[3]);
				$html_pieces[3] = str_replace('---$adress---', $row['adress'], $html_pieces[3]);
				$html_pieces[3] = str_replace('---$postal---', $row['postal'], $html_pieces[3]);
				$html_pieces[3] = str_replace('---$city---', $row['city'], $html_pieces[3]);				
			}
			echo $html_pieces[3];
		}
		else
			echo "Kund ej funnen";		
		
		mysqli_close($conn);
		
		include('includes/footer.html');
	}
	else
		header("location: index.php");		
?>