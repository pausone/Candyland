<?php
	include('includes/header.php');
	
	if(isset($_SESSION['admin']) && $_SESSION['admin']){				
		include('includes/database.php');
		
		$sales_sum = 0; //Sum of sales price of all sold items
		$bought_sum = 0;//Sum of original price for all bought items (items sold + items in stock)
		$profit = 1;//Sum of profit from sold items minus sum of price for all bought items
		$profit_sold_items = 0; //Profit from sold items(sales price minus original price)
		$in_stock_price = 0; //Sum original price for all items in stock		

		//Get ORDERS info from database
		$sql = "SELECT * FROM orders";
		$orders = mysqli_query($conn, $sql);
		
		if (mysqli_num_rows($orders) > 0) {
			while($row = mysqli_fetch_assoc($orders)) {
				$products = unserialize($row['products']); //For getting products information

				//Get product info from database		
				foreach($products as $id => $quantity){					
					$sql = 'SELECT * FROM products WHERE id = '. $id;
					$product = mysqli_query($conn, $sql);					
					
					if (mysqli_num_rows($product) > 0) {
						while($row = mysqli_fetch_assoc($product)) {
							$sales_sum += $row['sales_price']*$quantity;
							$bought_sum += $row['original_price']*$quantity;						
						}
					}
					else
						echo "Produkt ej funnen";
				}
			}
		}
		else
			echo "Orders ej funna";	

		$profit_sold_items = $sales_sum - $bought_sum;
		
		//Get product info from database		
		$sql = 'SELECT * FROM products';
		$product = mysqli_query($conn, $sql);					
		
		if (mysqli_num_rows($product) > 0) {
			while($row = mysqli_fetch_assoc($product)) {
				$in_stock_price += $row['original_price']*$row['in_stock'];	
			}
		}
		else
			echo "Produkter ej funna";
			
		$bought_sum = $in_stock_price + $bought_sum;		
		$profit = $profit_sold_items - $bought_sum;
		
		mysqli_close($conn);
			
		$html = file_get_contents('templates/finance_template.html');	
		$html = str_replace('---$sales_sum---', $sales_sum, $html);
		$html = str_replace('---$bought_sum---', $bought_sum, $html);
		$html = str_replace('---$profit---', $profit, $html);
		$html = str_replace('---$in_stock_price---', $in_stock_price, $html);
		$html = str_replace('---$profit_sold_items---', $profit_sold_items, $html);		

		echo $html;		
		include('includes/footer.html');	
	}
?>