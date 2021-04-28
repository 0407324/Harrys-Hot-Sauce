<?php
    include 'includes/core.php';
    include 'includes/connection.php';

date_default_timezone_set('America/New_York');
error_reporting(E_ERROR | E_PARSE);

if(loggedin()) {

    $_SESSION['Login_username'] = getuserfield('Login_username');
    $LoginID = $_SESSION['Login_id'];

    $query = "SELECT * FROM `Accounts`, Login WHERE `Account_id` = Login_account_id and Login_id = '".$_SESSION['Login_id']."'";

    if (!$result = mysqli_query($link, $query))
    {
        $result = 0;
        echo ('Error executing query in Checkout: '. mysqli_errno($link)." - ".mysqli_error($link)."<BR>");
    }
    else{
        $row = mysqli_fetch_array($result, MYSQLI_BOTH);
    }
        //Putting account table columns in variables
        $firstName = $row[ 'Account_firstname'];
        $lastName = $row['Account_lastname'];
        $address = $row['Account_address1'];
        $city = $row[ 'Account_city'];
        $state = $row['Account_state_region'];
        $zip = $row['Account_postal_code'];
        $email = $row['Account_email_address'];
        $accountID = $row['Account_id'];
    ?>

    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="utf-8">
            <title>Harry's Hot Sauce</title>
            <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
            <link href="css/layout.css" rel="stylesheet" type="text/css">
        </head>
    <body>

    <div id="wrapper">

    <?php include 'includes/header.php'; ?>

    <div id="body-wrapper">

    <p class="product-name">Shipping/Billing information</p>

    <br /><br /><br /><br />

    <form action="" method="post">
        <div style="width:310px; height: auto; float:left;">First Name:
            <input type="text" name="FirstName" value="<?php print $firstName; ?>" />
        </div>

        <div style="width:310px; height: auto; float:left;">Last Name:
            <input type="text" name="LastName" value="<?php print $lastName; ?>"/>
        </div>

        <div style="width:310px; height: auto; float:left;"> Email Address:
            <input type="text" name="emailAddress" value="<?php print $email; ?>"/>
        </div>
        <br />
        <div style="width:310px; height: auto; float:left;"> Address:
            <input type="text" name="address" value="<?php print $address; ?>"/>
        </div>
        <div style="width: 310px; height:auto; float:left;"> City:
            <input type="text" name="city" value="<?php print $city; ?>"/>
        </div>
        <div style="width:310px; height: auto; float:left;"> State:
            <input type="text" name="state" value="<?php print $state; ?>"/>
        </div>
        <br />
        <div style="width:310px; height: auto; float:left;">Zip Code:
            <input type="text" name="zipcode" value="<?php print $zip; ?>"/>
        </div>
        <div style="clear: both"></div>
        <br/>
        <hr>
        <br />
        <p class="product-name">Credit Card information</p>
        <br /><br /><br /><br />
        <div style="width:310px; height:auto; float:left;">
            Payment Method:
            <select name="payment Method">
                <option value="mastercard">Mastercard</option>
                <option value="visa">Visa</option>
            </select>
        </div>

        <br/><br/><br/>

        <div style="width: 340px; height: auto; float: left;">
            Credit Card Number:
            <input type="text" name="creditcard" />
        </div>
        <div style="width: 300px; height:auto; float:left;">
            Exp Date (mm/yy):
            <input type="text" name="expDate"/>
        </div>
        <div style="width:300px; height: auto; float:left;"> Cvv:
            <input type="text" name="cvv"/>
        </div>
        <br/><br/><br/><br/><br/><br/>
        <p>Shipping Method is USPS Priority Mail $17.90 </p>
        <br /><br />
        <input type="submit" name="submitCheckout" value="Pay" class="submit-reset">
    </form>
    <?php
    // If the submit checkout button was clicked all HTML form names change into php variables

    if(isset($_POST['submitCheckout']))  {

      $fName = $_POST['FirstName'];
      $lName = $_POST[ 'LastName' ];
      $emailA = $_POST['emailAddress'];
      $address1 = $_POST['address'];
      $city1 = $_POST['city'];
      $state1 = $_POST['state'];
      $zip1 =$_POST['zipCode'];
      $payment = $_POST['paymentMethod'];
      $cCN = $_POST['creditcard'];
      $expDate = $_POST['expDate'];
      $cVV = $_POST['cvv'];
      $status = 2;

      function validateCC($payment, $type) {

    if($type == "mastercard") {
    $denum = "Master Card";
  } elseif($type == "visa") {
    $denum = "Visa";
  } elseif($type == "mastercard") {
    $pattern = "/^([51|52|53|54|55]{2})([0-9]{14})$/";//Mastercard
    if (preg_match($pattern,$payment)) {
    $verified = true;
    } else {
    $verified = false;
  } elseif($type == "visa") {
    $pattern = "/^([4]{1})([0-9]{12,15})$/";//Visa
    if (preg_match($pattern,$payment)) {
    $verified = true;
    } else {
    $verified = false;}
  }
      }
    }

    if($verified == false) {
    //State if card in invalid
    echo "Credit card invalid. Please make sure that you entered a valid <em>" . $denum . "</em> credit card ";

    } else {
    echo "Your <em>" . $denum . "</em> credit card is valid";
    }


    //Build the order.
    build_order($accountID, $link);

    //order number from build order function
    $Order_Number = $_SESSION['OrderNumber'];

    //Update the account with the current infomation
    $query1 = "UPDATE `Accounts` SET `Account_lastname` = '".$lName."',
                                    `Account_firstname` = '".$fName."',
                                    `Account_address1` = '".$address1."',
                                    `Account_city` = '".$city1."',
                                    `Account_state_region` = '".$state1."',
                                    `Account_postal_code` = '".$zip1."',
                                    `Account_email_address` = '".$emailA."',
                                    `Account_last_update_date`= now(),
                                    `Account_last_update_by` = 'Check Out'
                                    WHERE `Account_id` = '".$accountID."' ";

    if (!$result = mysqli_query($link, $query1)) {
        $result = 0;

    echo ('Error executing query1 of Checkout: '. mysqli_errno($link)." - ".mysqli_error($link)."<BR>");
    }

    //Insert payment in to payment method table then retrieve the PM_id number
    $query2 = "INSERT INTO `Payment_method` (`PM_type`, `PM_number`, `PM_card_security_code`, `PM_exp_date`, `PM_account_id`, `PM_created_date`, `PM_created_by`, `PM_last_updated_date`, `PM_last_updated_by`)
            VALUES ('$payment', '$cCN', '$cVV', STR_TO_DATE('$expDate','%m/%Y'), '$accountID', now(), 'Check Out', now(), 'Check Out')";

    if (!$query_run2 = mysqli_query($link, $query2))
    {
        $result = 0;
        echo ('Error executing query2 of Checkout:'. mysqli_errno($link)." - ".mysqli_error($link)."<BR>");
    }

    $query3 = "SELECT Max(PM_id) FROM `Payment_method` WHERE `PM_account_id` = $accountID ";
    if (!$result3 = mysqli_query($link, $query3)){
        $result = 0;
        echo ('Error executing query3 of Checkout: '. mysqli_errno($link)." - ".mysqli_error($link)."<BR>");
    }
    else
    {
        $row3 = mysqli_fetch_array($results, MYSQLI_BOTH);
        $PM_id = $row3[0];
    }

        //Update the order headers with status 2 which is processed
        $query4 = "UPDATE `Order_headers` SET `Order_header_status_id` = '".$status."', `Order_header_pm_id` = '".$PM_id."' , `Order_header_last_updated_date` = now(), `Order_header_last_updated_by` = 'Check Out' WHERE `Order_header_account_id` = $accountID and Order_header_number = $Order_Number";

        if (!$query_run4 = mysqli_query($link, $query4)){
            $result = 0;
            echo ('Error executing query of Checkout: '. mysqli_errno($link)." - ".mysqli_error($link)."<BR>");
        }

        //Link to receipt page
        echo "<script type='text/javascript'> document.location = 'receipt.php';</script>";

    } ?>

    </div>
    <?php include 'includes/footer.php'; ?>
    </div>
    <?php include 'includes/logout.php'; ?>
    </body>
    </html>
<?php


}else {
    header('Location: login.php');
}

?>
