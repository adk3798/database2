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
    ?>
  <?php endif; ?>
  <h1> Login as a Parent </h1>

  <?php
    // define variables and set to empty values
    $email = $password = $name = $pid = $phone = "";
    $valid = false;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $email = scrub_input($_POST["email"]);
      $password = scrub_input($_POST["pwd"]);

      $mysqli = new mysqli('localhost', 'root', '', 'DB2'); //The Blank string is the password

      $query = "SELECT * FROM users WHERE email='$email' AND password='$password'"; //You don't need a ; like you do in SQL
      $result = $mysqli->query($query);

      if($result->num_rows != 0) {
        $row = mysqli_fetch_array($result);
        $aid = $row['id'];
        $name = $row['name'];
        $phone = $row['phone'];

        $query2 = "SELECT * FROM admins WHERE admin_id='$aid'";
        $result2 = $mysqli->query($query2);
      }

      if($result->num_rows == 0) {
        echo 'No account found with that email and password. Please Try Again';
      }
      else if($result2->num_rows == 0) {
        echo 'The given email and password account is not associated with an admin account.';
      }
      else {
        $_SESSION['name'] = $name;
        $_SESSION['id'] = $aid;
        $_SESSION['aid'] = $aid;
        $_SESSION['email'] = $email;
        $_SESSION['phone'] = $phone;
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
  <?php if(!isset($_SESSION['name'])) : ?>
  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required><br>
    <label for="pwd">Password:</label>
    <input type="password" id="pwd" name="pwd" required><br>
    <input type="submit" value="Login"></input><br><br>
  </form>
<?php else : ?>
  <?php
    echo 'You are currently logged in as: ' . $_SESSION['name'] . '.</br>';
  ?>

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
