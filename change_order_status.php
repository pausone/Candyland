<?php	
	session_start();
	if(isset($_SESSION['admin']) && $_SESSION['admin']){
		include('includes/database.php'); 
		
		//Get current order status
		$sql = 'SELECT * FROM orders WHERE id = '. $_GET['order'];
			$result = mysqli_query($conn, $sql);		
			
			if (mysqli_num_rows($result) > 0) {
				while($row = mysqli_fetch_assoc($result)) {			
					$sent = $row['sent'];
				}
			} else {
				echo "Status ej funnen.";
			}			

		//Change order status
		if ($stmt = mysqli_prepare($conn, 'UPDATE orders SET sent = ? WHERE id = '. $_GET['order'])) {
			
			mysqli_stmt_bind_param($stmt, 'd', $new_status);
			
			$new_status = !$sent;
			
			mysqli_stmt_execute($stmt);
			mysqli_stmt_close($stmt);
		}
		else
			echo "Det gick inte att ändra orderstatus.";
		
		mysqli_close($conn);
		
		header("location: order_info.php?order=" . $_GET['order']);		
	}
	else
		header("location: index.php");	
?>