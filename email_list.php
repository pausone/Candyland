<?php
	include('includes/header.php');
	
	if(isset($_SESSION['admin']) && $_SESSION['admin']){				
		$html = file_get_contents('templates/email_list_template.html');
		$html_pieces = explode('<!--==xxx==-->', $html);
		
		echo $html_pieces[0];
		
		include('includes/database.php');				
		
		//Get emails from database
		$sql = "SELECT * FROM email_list";
		$result = mysqli_query($conn, $sql);
		
		if (mysqli_num_rows($result) > 0) {
			while($row = mysqli_fetch_assoc($result)) {
				$tmp_html_piece = $html_pieces[1];
				$tmp_html_piece = str_replace('---$email---', $row['email'], $tmp_html_piece);				

				echo $tmp_html_piece;				
			}
		}
		else
			echo "Email ej funna";
		
		mysqli_close($conn);
		
		echo $html_pieces[2];
		include('includes/footer.html');
	}
	else
		header("location: index.php");

?>