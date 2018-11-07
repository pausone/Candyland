<?php

	include('includes/header.php');
	
	if(isset($_POST['email'])){ 
		include('includes/database.php');
		if ($stmt = mysqli_prepare($conn, 'INSERT INTO email_list(email) VALUES (?)')){

			mysqli_stmt_bind_param($stmt, 's', $email);
			
			$email = strip_tags($_POST['email']);

			mysqli_stmt_execute($stmt);

			mysqli_stmt_close($stmt);			
			
			mysqli_close($conn);
			
			echo "Epost tillagd.";
		}
		else
			echo "Det gick inte att lägga till en ny epost.";
	}
	
	include('includes/subscribe_form.html');
	include('includes/footer.html');
?>