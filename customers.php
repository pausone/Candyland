<?php
	include('includes/header.php');

	if(isset($_SESSION['admin']) && $_SESSION['admin']){		
		$html = file_get_contents('templates/customers_template.html');
		$html_pieces = explode('<!--==xxx==-->', $html);
		
		echo $html_pieces[0];
		
		include('includes/database.php');				
		
		//Get users from database that are not admin
		$sql = "SELECT * FROM users WHERE admin = 0";
		$result = mysqli_query($conn, $sql);
		
		if (mysqli_num_rows($result) > 0) {
			while($row = mysqli_fetch_assoc($result)) {	
				$tmp_html_piece = $html_pieces[1];
				$tmp_html_piece = str_replace('---$username---', $row['username'], $tmp_html_piece);
				$tmp_html_piece = str_replace('---$first_name---', $row['first_name'], $tmp_html_piece);
				$tmp_html_piece = str_replace('---$last_name---', $row['last_name'], $tmp_html_piece);
				$tmp_html_piece = str_replace('---$adress---', $row['adress'], $tmp_html_piece);
				$tmp_html_piece = str_replace('---$postal---', $row['postal'], $tmp_html_piece);
				$tmp_html_piece = str_replace('---$city---', $row['city'], $tmp_html_piece);
				
				echo $tmp_html_piece;				
			}
		}
		else
			echo "Kunder ej funna";
		
		mysqli_close($conn);
		
		echo $html_pieces[2];
		include('includes/footer.html');
	}
	else
		header("location: index.php");

?>