<?php	
	include('includes/header.php');

	if(isset($_SESSION['admin']) && $_SESSION['admin']){		
		
		$html = file_get_contents('templates/view_orders_template.html');
		$html_pieces = explode('<!--==xxx==-->', $html);
		$tmp_html_piece = $html_pieces[1];
		
		echo $html_pieces[0];
		
		include('includes/database.php');

		//Get orders info from database
		$sql = "SELECT * FROM orders";
		$result = mysqli_query($conn, $sql);
		
		if (mysqli_num_rows($result) > 0) {
			while($row = mysqli_fetch_assoc($result)) {
				$tmp_html_piece = str_replace('---$order_id---', $row['id'], $tmp_html_piece);
				$tmp_html_piece = str_replace('---$user_id---', $row['user_id'], $tmp_html_piece);
				$tmp_html_piece = str_replace('---$order_date---', $row['order_date'], $tmp_html_piece);
				$sent = $row['sent'] ? 'Ja' : 'Nej';
				$tmp_html_piece = str_replace('---$sent---', $sent, $tmp_html_piece);
				
				echo $tmp_html_piece;	
				
				$tmp_html_piece = $html_pieces[1];
			}
		}
		else
			echo "Inga orders funna";		
		
		mysqli_close($conn);
		
		echo $html_pieces[2];
		include('includes/footer.html');
	}
	else
		header("location: index.php");		
?>