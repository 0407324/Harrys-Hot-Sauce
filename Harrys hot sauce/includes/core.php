<?php
date_default_timezone_set('America/New_York');
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 'On');
session_start();
$current_file = $_SERVER['SCRIPT_NAME'];
$http_referer = $_SERVER['HTTP_REFERER'];

function loggedin()
{
	if(isset($_SESSION['Login_id'])&&!empty($_SESSION['Login_id']))
	{
		return true;
	}
	else
	{
		return false;
	}
}

function getuserfield($field)
{
	$query = "SELECT `$field` FROM `Login` WHERE `Login_username`
='".$_SESSION['Login_username']."'";
	if ($query_run = mysqli_query($link, $query))
	{
		if ($query_result = mysqli_fetch_assoc($query_run,MYSQLI_BOTH));
		{
			return $query_result[$field];
		}
	}
}

function build_order($account_id,$link)
{
	// Select all the information from the shopping cart for a specific user account
	$query1 = "SELECT COUNT(*) FROM `Shopping_cart` WHERE `SC_account_id` = $account_id";

	if (!$query_run1 = mysqli_query($link, $query1))
	{
		$query_run1 = 0;
		echo ('Error executing query1 of Core: ' . mysqli_errno($link)." - ".mysqli_error($link)."<BR>");
	}
	else
	{
		$row1 = mysqli_fetch_array($query_run1,MYSQLI_BOTH);
		$row_count = $row1[0];
	}

	// Empty cart test
	if ($row_count > 0)
	{
		// Get the next order number
		$query2 = "SELECT MAX(Order_header_number) FROM `Order_headers`";
		if (!$query_run2 = mysqli_query($link, $query2))
		{
			$query_run2 = 0;
			echo ('Error executing query2 of core: ' . mysqli_errno($link)." - ".mysqli_error($link)."<BR>");
		}
		else
		{
			$row2 = mysqli_fetch_array($query_run2,MYSQLI_BOTH);
			$order_number = $row2[0];
			if (is_null($order_number))
			{$order_number = 1001;}
			else
			{$order_number++;}
		}

		// Load order number into session variable
		$_SESSION['OrderNumber'] = $order_number;

		// Create the order header record
		$query3 = "INSERT INTO Order_headers (`Order_header_number`, `Order_header_orderdate`, `Order_header_account_id`, `Order_header_status_id`,
		`Order_header_created_date`, `Order_header_created_by`, `Order_header_last_updated_date`, `Order_header_last_updated_by`)
		VALUES ('$order_number', now(), '$account_id', '1', now(), 'CheckOut', now(), 'CheckOut') ";

		if (!$query_run3 = mysqli_query($link, $query3))
		{
			$query_run3 = 0;
			echo ('Error executing query3 of Core: ' . mysqli_errno($link)." - ".mysqli_error($link)."<BR>");
		}

		// Get the order header id
		$query4 = "SELECT Order_header_id FROM `Order_headers` WHERE `Order_header_number` = $order_number ";

		if (!$query_run4 = mysqli_query($link, $query4))
		{
			$query_run4 = 0;
			echo ('Error executing query4 of Core: ' . mysqli_errno($link). " - ".mysqli_error($link)."<BR>");
		}
		else
		{
			$row4 = mysqli_fetch_array($query_run4,MYSQLI_BOTH);
			$order_header_id = $row4[0];
			$order_details_line_number = 1;
		}

		// Select all the items from the shopping cart for a specific user account
		$query5 = "SELECT * FROM `Shopping_cart` WHERE `SC_account_id` = $account_id";
		if (!$query_run5 = mysqli_query($link, $query5))
		{
			$query_run5 = 0;
			echo ('Error executing query5 of Core: ' . mysqli_errno($link). " - " .mysqli_error($link)."<BR>");
		}
		while($row5 = mysqli_fetch_array($query_run5, MYSQLI_BOTH))
		{
			// Create the variables from the shopping cart
			$SCInvId = $row5['SC_inventory_id'];
			$SCQty = $row5['SC_order_quantity'];
			$SCUPrice = $row5['SC_unit_price'];
			$SCDPercent = $row5['SC_discount_percentage'];
			$SCDAmt = $row5['SC_discount_amount'];

		// Create order details records
		$query6 = "INSERT INTO `Order_details` (`Order_details_header_id`,
		`Order_details_line_number`, `Order_details_date`,
		`Order_details_inventory_id`, `Order_details_ordered_quantity`,
		`Order_details_unit_price`,`Order_details_created_date`,
		`Order_details_created_by`, `Order_details_last_updated_date`,
		`Order_details_last_updated_by`) VALUES ('$order_header_id',
		'$order_details_line_number', now(), '$SCInvId', '$SCQty',
		'$SCUPrice', now(), 'CheckOut', now(), 'CheckOut');";
		if (!$query_run6 = mysqli_query($link, $query6))
		{
			$query_run6 = 0;
			echo ('Error executing query6 of Core: ' . mysqli_errno($link). " - " .mysqli_error($link)."<BR>");
		}
			// Decrement inventory on hand balance
			$query7 = "SELECT Inventory_units_in_stock FROM `Inventory` WHERE `Inventory_product_id` = $SCInvId;";
			if (!$query_run7 = mysqli_query($link, $query7))
			{
				$query_run7 = 0;
				echo ('Error executing query7 of Core: ' . mysqli_errno($link). " - " .mysqli_error($link)."<BR>");
			}
			else
			{
				$row7 = mysqli_fetch_array($query_run7,MYSQLI_BOTH);
				$Onhand_qty = $row7[0];
				$New_onhand_qty = $Onhand_qty -$SCQty;
				if ($New_onhand_qty < 0) {$New_onhand_qty = 0;}
			}

			// Update inventory on hand balance
			$query8 = "UPDATE Inventory SET `Inventory_units_in_stock` =
		  $New_onhand_qty, Inventory_last_updated_date = now(),
			Inventory_last_updated_by = 'Check Out' WHERE `Inventory_product_id` =
			$SCInvId ;";
			if (!$query_run8 = mysqli_query($link, $query8))
			{
				$query_run8 = 0;
				echo ('Error executing query8 of Core: ' . mysqli_errno($link). " - " .mysqli_error($link)."<BR>");
			}

			// Delete item from shopping cart
			$query9 = "DELETE FROM `Shopping_cart` WHERE `SC_account_id` = $account_id";
			if (!$query_run9 = mysqli_query($link, $query9))
			{
				$query_run9 = 0;
				echo ('Error executing query9 of Core: ' . mysqli_errno($link). " - " .mysqli_error($link)."<BR>");
			}

				// Increment line number for next line
				$order_details_line_number++;
			} // End of the while loop
		} // end of empty cart test // if ($row_count > 0)
	}
?>
