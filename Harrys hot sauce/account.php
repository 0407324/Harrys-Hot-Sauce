<?php
include 'includes/core.php';
include 'includes/connection.php';
date_default_timezone_set('America/New_York');
error_reporting(E_ERROR | E_PARSE);
if(loggedin()) {

	$_SESSION['Login_username'] = getuserfield('Login_username');
	$accountID = $_SESSION['Login_account_id'];

	$query = "SELECT * FROM `Accounts` WHERE `Account_id` =
'".$accountID."'";
	if (!$result = mysqli_query($link, $query))
	{
		$result = 0;
		echo ('Error executing query: ' . mysqli_errno($link)." -
".mysqli_error($link)."<BR>");
	}
	else
	{
		if ($row = mysqli_fetch_assoc($result))
		{
		// Putting account table columns in variables
		$firstName = $row['Account_firstname'];
		$lastName = $row['Account_lastname'];
		$email = $row['Account_email_address'];
		$address = $row['Account_address1'];
		$city = $row['Account_city'];
		$state = $row['Account_state_region'];
		$zip = $row['Account_postal_code'];
		 }
		 else
		{
			$row = 0;
			echo ('Error executing query: ' . mysqli_errno($link)." -
".mysqli_error($link)."<BR>");
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Harry's Hot Sauce</title>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<link href="css/layout.css" rel="stylesheet" type="text/css">
	</head>

	<body>
		<div id="wrapper">
			<?php include 'includes/header.php'; ?>

			<div id="body-wrapper">

				<?php /*Display First Name of customer */ print '<p class="product-name">Welcome, ' .$firstName. '</p>' ?>
				<br />
				<br />

				<form action="" method="post">
					<div style="width:310px; height:auto; float:left;">First Name: <input
						type="text" name="FirstName" value="<?php print $firstName; ?>" /> </div>
					<div style="width:310px; height:auto; float:left;">Last Name: <input
						type="text" name="LastName" value="<?php print $lastName; ?>" /></div>
					<div style="width:310px; height:auto; float:left;">Email Address: <input
						type="text" name="emailAddress" value="<?php print $email; ?>" /></div>
					<br />
				<div style="width:310px; height:auto; float:left;">Address: <input
					type="text" name="address" value="<?php print $address; ?>" /></div>
				<div style="width:310px; height:auto; float:left;">City: <input
					type="text" name="city" value="<?php print $city; ?>" /></div>
				<div style="width:310px; height:auto; float:left;">State: <input
					type="text" name="state" value="<?php print $state; ?>" /></div>
				<br />
				<div style="width:310px; height:auto; float:left;">Zip Code: <input
					type="text" name="zipCode" value="<?php print $zip; ?>"/></div>
				<br />
				<br />
				<div style="clear:both"></div>
				<br />
				<div style="width:310px; height:auto; float:left;">
				<input type="submit" name="submitAccount" value="Update" class="submit-reset">
				</div>
				<div style="width:310px; height:auto; float:right;">
				<input type="submit" name="bypassAccount" value="No update Required" class="submit-reset">
				</form>
				<?php
				if(isset($_POST['submitAccount']))
				 {
								// If the submit account button was clicked all HTML form names change into php variables
								$fName = $_POST['FirstName'];
								$lName = $_POST['LastName'];
								$emailA = $_POST['emailAddress'];
								$address1 = $_POST['address'];
								$city1 = $_POST['city'];
								$state1 = $_POST['state'];
								$zip1 = $_POST['zipCode'];

								// update accounts and set all variables where the account ID is equal to account ID
								$query = "UPDATE `Accounts` SET `Account_lastname` =
								'".$lName."', `Account_firstname` = '".$fName."', `Account_address1` =
								'".$address1."', `Account_city` = '".$city1."', `Acccount_state_region` =
								'".$state1."', `Account_postal_code` = '".$zip1."', `Account_email_address` =
								'".$emailA."' WHERE `Account_id` = '".$accountID."'";
										if (!$result = mysqli_query($link, $query))
										{
											$result = 0;
											echo ('Error executing query: ' . mysqli_errno($link)." -
								".mysqli_error($link)."<BR>");
										}

										// Link to customer account page
											echo "<script type='text/javascript'> document.location = 'productsli.php'; </script>";
													}
										if(isset($_POST['bypassAccount']))
													{
										// No update required
										// Link to Products page
										echo "<script type='text/javascript'> document.location = 'productsli.php'; </script>";
													}
?>
	</div>
	<?php include 'includes/footer.php'; ?>
	</div>
  <?php include 'includes/signout.php'; ?>
  </body>
	</html>
				<?php
					} else {
						echo "<script type='text/javascript'> document.location = 'login.php'; </script>";
					}
				?>
