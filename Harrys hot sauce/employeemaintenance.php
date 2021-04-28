<?php
session_start();
include 'includes/core.php';
include 'includes/connection.php';
date_default_timezone_set('America/New_York');
error_reporting(E_ERROR | E_PARSE);

if(loggedin()) {
  $_SESSION['Login_username'] = getuserfield('Login_username');
  $accountID = $_SESSION['Login_account_id'];

  $query = "SELECT * FROM `Accounts` WHERE `Account_id` = '".$accountID."'";
  if (!$result = mysqli_query($link, $query))
  {
    $result = 0;
    echo ('Error executingn query in Employeemaintenance: ' . mysqli_errno($link)." - ".mysqli_error($link)."<BR>");
  }
  else
  {
    $row = mysqli_fetch_array($result,MYSQLI_BOTH);
  }
  // Putting account table columns in variables
  $firstName = $row['Account_firstname'];
  $lastName = $row['Account_lastname'];
  $email = $row['Account_email_address'];
  $address = $row['Account_address1'];
  $city = $row['Account_city'];
  $state = $row['Account_state_region'];
  $zip = $row['Account_postal_code'];
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
        <p class="titles">Employee Maintenance</p>
      <?
  // Get the next employee number
      $query2 = "SELECT MAX(Employee_number) FROM `Employee`";
      if (!$query_run2 = mysqli_query($link, $query2))
      {
        $query_run2 = 0;
        $employeeNumber = null;
      }
      else
      {
        $row2 = mysqli_fetch_array($query_run2,MYSQLI_BOTH);
        $employeeNumber = $row2[0];
      }

      if (is_null($employeeNumber))
      {$employeeNumber = 1001;}
      else
      {$employeeNumber++;}

      // Build select combo for names
      $query1 = "SELECT Account_lastname, Account_firstname, Account_middlename,
      Account_id FROM `Accounts` ORDER BY 1, 2, 3, 4";
      if (!$query_run1 = mysqli_query($link, $query1))
      {
        $query_run1 = 0;
        echo ('Error executing query1 in Employeemaintenance: ' . mysqli_errno($link). " - ".mysqli_error($link)."<BR>");
      }
      else
      {
        $row1 = mysqli_fetch_array($query_run1,MYSQLI_BOTH);
        $namesbox='<select name="Names" id="combo">';
        $namesbox.='<option value=0>' .' '. '</option>';
        $namesbox.='<option value="' . $row1['Account_id'] . '">' .
        $row1['Account_lastname'] .', ' .$row1['Account_firstname'] .'
        ('.$row1['Account_id'] .') '. '</option>';
      }
      while ($row = mysqli_fetch_array($query_run1,MYSQLI_BOTH))
      {
        $namesbox.='<option value="' . $row['Account_id'] . '">' .
        $row['Account_lastname'] .', ' .$row['Account_firstname'] .'
        ('.$row['Account_id'] .') ' . '</option>';
      }
      $namesbox.='</select>';
      mysqli_free_result($query_run1);

      // Build select combo for employee positions
      $query1 = "SELECT GL_data FROM `General_lookup` WHERE `GL_type` = 'Employee
      Position' ORDER BY 1";
      if (!$query_run1 = mysqli_query($link, $query))
      {
        $result = 0;
        echo ('Error executing query1 in employeemaintenance: ' . mysqli_errno($link)." - ".mysqli_error($link)."<BR>");
      }
      else
      {
        $row1 = mysqli_fetch_array($query_run1,MYSQLI_BOTH);
        $empposbox='<select name="positionTitle" id="combo">';
        $empposbox.='<option value=0>' .' '. '</option>';
        $empposbox.='<option value="' . $row1['GL_data']
        .'">'.$row1['GL_data'].'</option>';
      }
      while ($row = mysqli_fetch_array($query_run1, MYSQLI_BOTH))
      {
        $empposbox.='<option value="' . $row['GL_data']
        .'">'.$row['GL_data'].'</option>';
      }
      $empposbox.='</select>';
      mysqli_free_result($query_run1);

      // Build select combo for employee status
      $query1 = "SELECT GL_data FROM `General_lookup` WHERE `GL_type` = 'Employee
      Status' ORDER BY 1";
      if (!$query_run1 = mysqli_query($link, $query1))
      {
        $query_run1 = 0;
        echo ('Error executing query in Employeemaintenance: ' . mysqli_errno($link)." - ".mysqli_error($link)."<BR>");
      }
      else
      {
        $row1 = mysqli_fetch_array($query_run1,MYSQLI_BOTH);
        $empstatusbox='<select name="Estatus" id="combo">';
        $empstatusbox.='<option value=0>' .' '. '</option>';
        $empstatusbox.='<option value="' . $row1['GL_data']
        .'">'.$row1['GL_data'].'</option>';
      }
      while ($row = mysqli_fetch_array($query_run1,MYSQLI_BOTH))
      {
        $empstatusbox.='<option value="' . $row['GL_data']
        .'">'.$row['GL_data'].'</option>';
      }
      $empstatusbox.='</select>';
      mysqli_free_result($query_run1);

      // Build select combo for managers
      $query1 = "SELECT Account_lastname, Account_firstname, Employee_account_id
      FROM `Employee`, `Accounts` WHERE  `Employee_position_title` = 'Manager' AND
      `Employee_account_id` = `Account_id` ORDER BY 1";
      if (!$query_run1 = mysqli_query($link, $query1))
      {
        !$query_run1 = 0;
        echo ('Error executing query in Employeemaintenance: ' . mysqli_errno($link)." - ".mysqli_error($link)."<BR>");
      }
      else
      {
        $row1 = mysqli_fetch_array($query_run1,MYSQLI_BOTH);
        $empmgrbox='<select name="managerID" id="combo">';
        $empmgrbox.='<option value=0>' .' '. '</option>';
        $empmgrbox.='<option value="' .$row1['Employee_account_id'] .'">'.
        $row1['Account_lastname'] .', ' .$row1['Account_firstname'] .'</option>';
      }
      while ($row = mysqli_fetch_array($query_run1,MYSQLI_BOTH))
      {
        $empmgrbox.='<option value="'.$row['Employee_account_id'] .'">'.
      $row['Account_lastname'] .', ' .$row['Account_firstname'].'</option>';
      }
      $empmgrbox.='</select>';
      mysqli_free_result($query_run1);
  ?>

    <div id="register-wrapper">
      <form action="" method="post">

        <div class="label-wrapper">
          <p>Name (Acct ID):</p>
          <? echo $namesbox; ?>
        </div>

        <input type="submit" name="submitfind" value="Find" class="submit-reset">
        <input type="reset" name="reset" value="Reset" class="submit-reset">
    </form>
    <?php
    $NameID = 0;
    if(isset($_POST['submitfind']))
    {
      $NameID = $_POST['Names'];
      $_SESSION['NameID'] = $NameID;

      // If the submit find button is clicked, do the following.
      $fquery = "SELECT * FROM `Accounts` WHERE `Account_id` = '".$NameID."'";
      if (!$fresult = mysqli_query($link, $fquery))
      {
        $fresult = 0;
        echo ('Error executing query in Employeemaintenance: ' . mysqli_errno($link)." - ".mysqli_error($link)."<BR>");
      }
      else
      {
        $frow = mysqli_fetch_array($fresult,MYSQLI_BOTH);

        // Putting account table columns in variables
        $ffirstName = $frow['Account_firstname'];
        $flastName = $frow['Account_lastname'];
        $fmiddleName = $frow['Account_middlename'];
        $femail = $frow['Account_email_address'];
        $faddress = $frow['Account_address1'];
        $faddress2 = $frow['Account_address2'];
        $fcity = $frow['Account_city'];
        $fstate = $frow['Account_state_region'];
        $fzip = $frow['Account_postal_code'];
        $facctStatus = $frow['Account_status'];
        $facctTypeID = $frow['Account_AT_id'];
        $facctid = $frow['Account_id'];
      }
      if ($NameID != 0)
      {
        ECHO $flastName.', '.$ffirstName.' '.$fmiddleName.'<BR />';
        ECHO $faddress.'<BR />';
        if (!empty($faddress2)) {ECHO 'Line 2 : '. $faddress2.'<BR />';}
        ECHO $fcity.', '.$fstate.' '.$fzip.'<BR /><BR />';
        ECHO 'Acct Status: '. $facctStatus.'<BR />';
        ECHO 'Acct Type: '.$facctTypeID.'<BR />';
        ECHO 'Email: '. $femail.'<BR />';
      }
    }
?>
</div>
<div id="register-wrapper">
  <?php

  // If the submit find button is clicked do the following
  $equery = "SELECT * FROM `Employee` WHERE `Employee_account_id` =
  '".$_SESSION['NameID']."'";
  if (!$eresult = mysqli_query($link, $equery))
  {
    $eresult = 0;
    echo ('Error executing query in Employeemaintenance: ' . mysqli_errno($link)." - ".mysqli_error($link)."<BR>");
  }
  else
  {
    $erow = mysqli_fetch_array($eresult,MYSQLI_BOTH);
    $eempnumber = $erow['Employee_number'];
    $eempstatus = $erow['Employee_status'];
    $eempmanid = $erow['Employee_manager_id'];
    $eempposition = $erow['Employee_position_title'];
  }
  if(isset($eempnumber)) {$ecurrentemp = 'YES'; $employeeNumber=$eempnumber;
  $smbutton='Update';} else {$ecurrentemp = 'NO'; $smbutton='Add';}

  // Build select combo for employee positions
  $query1 = "SELECT GL_data FROM `General_lookup` WHERE `GL_type` = 'Employee
  Position' ORDER BY 1";
  if (!$query_run1 = mysqli_query($link, $query1))
  {
    $query_run1 = 0;
    echo ('Error executing query in Employeemaintenance: ' . mysqli_errno($link)." - ".mysqli_error($link)."<BR>");
  }
  else
  {
    $row1 = mysqli_fetch_array($query_run1,MYSQLI_BOTH);
    $empposbox='<select name="positionTitle" id="combo">';
    $empposbox.='<option value=0>' .' '. '</option>';
  }
  if ($eempposition == $row1['GL_data'])
     {$empposbox.='<option value="' . $row1['GL_data'] .'"
      selected>'.$row1['GL_data'].'</option>';}
  else
    {$empposbox.='<option value="' . $row1['GL_data']
    .'">'.$row1['GL_data'].'</option>';}
    while ($row = mysqli_fetch_array($query_run1,MYSQLI_BOTH))
      {
        if ($eempposition == $row['GL_data'])
          {$empposbox.='<option value="' . $row['GL_data'] . '"
          selected>'.$row['GL_data'].'</option>';}
      else
        {$empposbox.='<option value="' . $row['GL_data']
        .'">'.$row['GL_data'].'</option>';}
      }
      $empposbox.='</select>';
      mysqli_free_result($query_run1);

      // Build select combo for employee status
      $query1 = "SELECT GL_data FROM `General_lookup` WHERE `GL_type` = 'Employee
      Status' ORDER BY 1";
      $empstatusbox='<select name="Estatus" id="combo">';
      $empstatusbox.='<option value=0>' .' '. '</option>';
      if (!$query_run1 = mysqli_query($link, $query1))
      {
        $query_run1 = 0;
        echo ('Error executing query in Employeemaintenance: ' . mysqli_errno($link)." - ".mysqli_error($link)."<BR>");
      }
      else
      {
        $row1 = mysqli_fetch_array($query_run1,MYSQLI_BOTH);
      }
      if ($eempstatus == $row1['GL_data'])
         {$empstatusbox.='<option value="' .$row1['GL_data'] .'"
           selected>'.$row1['GL_data'].'<option>';}
      else
        {$empmgrbox.='<option value="' . $row1['GL_data']
          .'">'.$row1['GL_data'].'</option>';}
        while ($row = mysqli_fetch_array($query_run1,MYSQLI_BOTH))
        {
          if ($eempstatus == $row['GL_data'])
            {$empstatusbox.='<option value="' .$row['GL_data'] .'"
              selected>'.$row['GL_data'].'</option>';}
          else
          {$empstatusbox.='<option value="' .$row['GL_data']
            .'">'.$row['GL_data'].'<option>';}
        }
    $empstatusbox.='</select>';
    mysqli_free_result($query_run1);

    // Build select combo for managers
    $query1 = "SELECT Account_lastname, Account_firstname, Employee_account_id,
    Employee_position_title FROM `Employee`, `Accounts` WHERE
    `Employee_position_title` = 'Manager' AND `Employee_account_id` =
    `Account_id` ORDER BY 1";
    if (!$query_run1 = mysqli_query($link, $query1))
    {
      $query_run1 = 0;
      echo ('Error executing query in Employeemaintenance: ' . mysqli_errno($link). " - ".mysqli_error($link)."<BR>");
    }
    else
    {
      $row1 = mysqli_fetch_array($query_run1,MYSQLI_BOTH);
      $empmgrbox='<select name="managerID" id="combo">';
      $empmgrbox.='<option value=0>' .' '. '</option>';
    }
    if ($eempmanid == $row1['Employee_account_id'])
      {$empmgrbox.='<option value="' . $row1['Employee_account_id'] .'"
        selected>'. $row1['Account_lastname'] .',' .$row1[Account_firstname]
      .'</option>';}
      else
          {
            $empmgrbox.='<option value="' . $row['Employee_account_id'] .'">'.
            $row['Account_lastname'] .', ' .$row['Account_firstname'] .'</option>';
          }
          while ($row = mysqli_fetch_array($query_run1,MYSQLI_BOTH))
            {
              if ($eempposition == $row['Employee_position_title'])
                {$empmgrbox.='<option value="' . $row['Employee_account_id'] .'"
                  selected>'. $row['Account_lastname'] .', ' .$row['Account_firstname']
                .'</option>';}
          else
          {
            $empmgrbox.='<option value="' . $row['Employee_account_id'] .'">'.
            $row['Account_lastname'] .', ' .$row['Account_firstname'] .'</option>';
          }
      }
      $empmgrbox.='</select>';
      mysqli_free_result($query_run1);
    ?>
    <form action="" method="post">

      <div class="label-wrapper">
        Manager:
        <? echo $empmgrbox; ?>
      </div>

      <div class="label-wrapper">
        Employee Number:
        <input type="text" placeholder=<? echo $employeeNumber ?>
        name="employeeNumber" size="50" recently/>
      </div>

      <div class="label-wrapper">
        Position Title:
        <? echo $empposbox; ?>
      </div>

      <div class="label-wrapper">
        Employee Status:
        <? echo $empstatusbox; ?>
      </div>

      <input type="submit" name="submitmaintenance" value=<?php echo $smbutton; ?>
      class="submit-reset">
      <input type="reset" name="reset" value="Reset" class="submit-reset">
    </form>
    <?php
    if(isset($_POST['submitmaintenance']) and $_SESSION['NameID'] != 0)
    {
      // All html form names turned into php variables
      $dateTime = date("Y-m-d H:i:s");
      $managerID = $_POST['managerID'];
      $positionTitle = $_POST['positionTitle'];
      $estatus = $_POST['Estatus'];
      $eprivid = $_POST['EprivID'];
      $NameID = $_SESSION['NameID'];
      if ($ecurrentemp == 'NO')
      {

        // If the submit maintenance button is clicked do the following
        $query = "INSERT INTO `Employee` (`Employee_number`,
        `Employee_manager_id`, `Employee_account_id`, `Employee_status`,
        `Employee_start_date`, `Employee_position_title`, `Employee_created_date`,
        `Employee_create_by`, `Employee_last_updated`, `Employee_last_updated_by`)
        VALUES ('$employeeNumber', '$managerID', '$NameID', '$estatus', '$dateTime',
          'Employee Registration', '$dateTime', 'Employee Registration')";
      if (!$query_run = mysqli_query($link, $query))
      {
        $query_run = 0;
        echo ('Error executing query in Employeemaintenance: ' . mysqli_errno($link). " - ".mysqli_error($link)."<BR>");
        $estatus = 'Error';
      }
      if ($estatus == 'Active')
      {$astatusid = '2';}
      else
      {$astatusid = '1';}

      // Now update the account_type_id to employee
      $query7 = "UPDATE `Accounts` SET `Account_AT_id` = '".$astatusid."',
      `Account_last_update_date` = '".$dateTime."', `Account_last_updated_by` =
      'Employee Maintenance' WHERE `Account_id` = '".$_SESSION['NameID']."'";
      if (!$query_run7 = mysqli_query($link, $query7))
      {
        $query_run7 = 0;
        echo ('Error executing query in Employeemaintenance: ' . mysqli_errno($link). " - ".mysqli_error($link)."<BR>");
      }
      $_SESSION ['NameID'] = 0;
    }
    else  // if ($ecurrentemp == 'NO')
    {
      $query = "UPDATE `Employee` SET `Employee_manager_id` =
      '".$managerID."', `Employee_status` =
      '".$estatus."', `Employee_position_title` = '".$positionTitle."'
      WHERE `Employee_number` = '".$employeeNumber."'";
      if (!$query_run = mysqli_query($link, $query))
      {
        $query_run = 0;
        echo ('Error executing query in Employeemaintenance: ' . mysqli_errno($link). " - ".mysqli_error($link)."<BR>");
      }
      if ($estatus = 'Active')
        {$astatusid = '2';}
        else
        {$astatusid = '1';}

      // Now update the account_type_id to the employee
      $query7 = "UPDATE `Accounts` SET `Account_AT_id` = '".$astatusid."',
      `Account_last_updated_date` = '".$dateTime."', `Account_last_updated_by` =
      'Employee Maintenance' WHERE `Account_id` = '".$_SESSION['NameID']."'";
      if (!$query_run7 = mysqli_query($link, $query7))
      {
        $query_run7 = 0;
        echo ('Error executing query in Employeemaintenance: ' . mysqli_errno($link). " - ".mysqli_error($link)."<BR>");
      }
      $_SESSION['NameID'] = 0;
    }
    $NameID = 0;
    $_SESSION['NameID'] = 0;
  }
?>

</div>
</div>
<?php include 'includes/footer.php'; ?>
</div>
<?php include 'includes/signout.php'; ?>
</body>
</html>
<?php
} else
{ header('Location: login.php'); }
?>
