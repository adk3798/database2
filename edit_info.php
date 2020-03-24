<!DOCTYPE html>
<html lang="en">
<head>
    <title>Database 2</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
  <?php
    session_start();
  ?>
  <?php if(isset($_SESSION['name'])) : ?>
    <?php
      echo '<h1>Welcome, ' . $_SESSION['name'] . '</h1>';
      if (isset($_POST['parent_edit'])) { // submit being set means mentee
        $sid = $_POST['parent_edit'];
        $_SESSION['sid'] = $sid;
        unset($_SERVER["REQUEST_METHOD"]);
      }
    ?>

    <?php
      if(isset($_SESSION['sid'])) {
        $mysqli = new mysqli('localhost', 'root', '', 'db2_project'); //The Blank string is the password
        $sid = $_SESSION['sid'];
        $query = "SELECT * FROM users WHERE id='$sid'"; //You don't need a ; like you do in SQL
        $result = $mysqli->query($query);
        $row = mysqli_fetch_array($result);
        $student_name = $row['name'];
        echo "<h2>Currently editing info of <span style=\"color:red\"> $student_name</span></h2>";
      }
      else if(isset($_SESSION['pid'])) {
        $mysqli = new mysqli('localhost', 'root', '', 'db2_project'); //The Blank string is the password
        $pid = $_SESSION['pid'];
        $query = "SELECT * FROM users WHERE id='$pid'"; //You don't need a ; like you do in SQL
        $result = $mysqli->query($query);
        $row = mysqli_fetch_array($result);
        $student_name = $row['name'];
        echo "<h2>Currently editing info of <span style=\"color:red\"> $student_name</span></h2>";
      }
    ?>
  <?php endif; ?>
  <h1> Register a Parent Account </h1>

  <?php
    // define variables and set to empty values
    $email = $password = $name = $phone = "";
    $valid = false;

    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
      $email = scrub_input($_POST["email"]);
      $password = scrub_input($_POST["pwd"]);
      $name = scrub_input($_POST["name"]);
      $phone = scrub_input($_POST["phone"]);
      $grade = "";
      if(isset($_POST['grade'])) {
        $grade = scrub_input($_POST["grade"]);
      }

      $id=-1;
      if(isset($_SESSION['sid'])) {
        $id = $_SESSION['sid'];
      }
      else if(isset($_SESSION['pid'])) {
        $id = $_SESSION['pid'];
      }


      $mysqli = new mysqli('localhost', 'root', '', 'db2_project'); //The Blank string is the password

      $email_query = "SELECT * FROM users WHERE email='$email'"; //You don't need a ; like you do in SQL
      $email_result = $mysqli->query($email_query);

      if(isset($_POST['change_email']) && $email_result->num_rows !== 0) {
        echo "The email address: \"<span style=\"color:red\"> $email </span>\"is already taken. Please try again with a different email.";
      }
      else if(isset($_POST['change_password']) && empty($password)) {
        echo "Attempted to change password but field was left blank. Please try again.";
      }
      else if(isset($_POST['change_name']) && empty($name)) {
        echo "Attempted to change name but field was left blank. Please try again.";
      }
      else if(isset($_POST['change_phone']) && empty($phone)) {
        echo "Attempted to change phone # but field was left blank. Please try again.";
      }
      else if(isset($_POST['change_grade']) && empty($grade)) {
        echo "Attempted to change grade but no grade was selected. Please try again.";
      }
      else {
        if(!isset($_POST['change_email']) && !isset($_POST['change_name']) && !isset($_POST['change_password'])
            && !isset($_POST['change_phone']) && !isset($_POST['change_grade'])) {
              echo "No fields were selected for update.";
        }
        else {
          $query = "SELECT * FROM users WHERE id=$id"; //You don't need a ; like you do in SQL
          $result = $mysqli->query($query);
          $row = mysqli_fetch_array($result);
          $update_query = "UPDATE users SET";
          if(isset($_POST['change_email'])) {
            $update_query = $update_query . " email = '$email',";
            if($id == $_SESSION['id']) {
              $_SESSION['email'] = $email;
            }
          }
          if(isset($_POST['change_password'])) {
            $update_query = $update_query . " password = '$password',";
          }
          if(isset($_POST['change_name'])) {
            $update_query = $update_query . " name = '$name',";
            if($id == $_SESSION['id']) {
              $_SESSION['name'] = $name;
            }
          }
          if(isset($_POST['change_phone'])) {
            $update_query = $update_query . " phone = '$phone',";
            if($id == $_SESSION['id']) {
              $_SESSION['phone'] = $phone;
            }
          }
          $update_query = substr($update_query, 0, -1) . " WHERE id=$id";
          $result = $mysqli->query($update_query);

          if(isset($_POST['change_grade'])) {
            $update_query = "UPDATE students SET grade = $grade WHERE student_id=$id";
            $result = $mysqli->query($update_query);
          }

          echo "<span style=\"color:red\"> Information Updated </span>";
        }
      }

      $mysqli->close();
    }

    function scrub_input($data) {
      $data = trim($data);
      $data = stripslashes($data);
      $data = htmlspecialchars($data);
      return $data;
    }
  ?>
<?php if(!$valid) : ?>
  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
    <label for="email">Email:</label>
    <input type="email" id="email" name="email">
    <input type="checkbox" id="change_email" name="change_email" value="change_email"><br>
    <label for="pwd">Password:</label>
    <input type="password" id="pwd" name="pwd">
    <input type="checkbox" id="change_password" name="change_password" value="change_password"><br>
    <label for="name">Name:</label>
    <input type="text" id="name" name="name">
    <input type="checkbox" id="change_name" name="change_name" value="change_name"><br>
    <label for="phone">Phone #:</label>
    <input type="tel" id="phone" name="phone"
       pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}">
       <small>Format: 123-456-7890</small>
    <input type="checkbox" id="change_phone" name="change_phone" value="change_phone"><br>
    <?php if(isset($_SESSION['sid'])) : ?>
      <label for="grade">Choose your grade level:</label>
      <select id="grade" name="grade">
           <option value=0 selected>Choose Grade</option>
           <option value=6>6</option>
           <option value=7>7</option>
           <option value=8>8</option>
           <option value=9>9</option>
           <option value=10>10</option>
           <option value=11>11</option>
           <option value=12>12</option>
      </select>
      <input type="checkbox" id="change_grade" name="change_grade" value="change_grade"><br>
    <?php endif; ?>
    <input type="submit" value="Change Information"></input><br><br>
  </form>
<?php else : ?>
    <form action="parent_login.php">
      <input type="submit" value="Proceed to Login"></input><br><br>
    </form>
<?php endif; ?>

<?php if(isset($_SESSION['name'])) : ?>
  <form action="logout.php">
    Logout:
    <input type="submit" value="Logout"></input><br><br>
  </form>
<?php endif; ?>


  <form action="landing.php">
    Return to main page:
    <input type="submit" value="Main Page"></input><br><br>
  </form>

</body>
</html>
