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
  <h1> Register a Student Account </h1>

  <?php
    // define variables and set to empty values
    $email = $password = $name = $phone = $grade = $pemail = "";
    $valid = false;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $email = scrub_input($_POST["email"]);
      $password = scrub_input($_POST["pwd"]);
      $name = scrub_input($_POST["name"]);
      $phone = scrub_input($_POST["phone"]);
      $grade = scrub_input($_POST["grade"]);
      $pemail = scrub_input($_POST["pemail"]);

      $mysqli = new mysqli('localhost', 'root', '', 'db2_project'); //The Blank string is the password

      $query = "SELECT * FROM users WHERE email='$email'";
      $result = $mysqli->query($query);

      $query2 = "SELECT * FROM users WHERE email='$pemail' AND id IN(SELECT parent_id FROM parents)";
      $result2 = $mysqli->query($query2);

      if($result->num_rows !== 0) {
        echo "The email address: \"<span style=\"color:red\"> $email </span>\"is already taken. Please try again with a different email.";
      }
      else if($grade == 0) {
        echo "<span style=\"color:red\"> No valid grade was selected</span>";
      }
      else if($result2->num_rows == 0) {
        echo "The given parent email address: <span style=\"color:red\"> $pemail </span>does not belong to any parent account.";
      }
      else {
        // If the email isn't taken, actually register the parent

        $register = "INSERT INTO users (email, password, name, phone) VALUES ('{$email}', '{$password}', '{$name}', '{$phone}')";
        $insert_result = $mysqli->query($register);

        $query = "SELECT * FROM users WHERE email='$email'"; //You don't need a ; like you do in SQL
        $result = $mysqli->query($query);

        $row = mysqli_fetch_array($result);
        $sid = $row['id'];

        $query = "SELECT * FROM users WHERE email='$pemail'"; //You don't need a ; like you do in SQL
        $result = $mysqli->query($query);

        $row = mysqli_fetch_array($result);
        $pid = $row['id'];

        $registers = "INSERT INTO students (student_id, grade, parent_id) VALUES ('{$sid}', '{$grade}', '{$pid}')";
        $insert_result2 = $mysqli->query($registers);

        echo "Successfully Registered!";
        $valid = true;
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
    <input type="email" id="email" name="email" required><br>
    <label for="pwd">Password:</label>
    <input type="password" id="pwd" name="pwd" required><br>
    <label for="name">Name:</label>
    <input type="text" id="name" name="name" required><br>
    <label for="phone">Phone #:</label>
    <input type="tel" id="phone" name="phone"
       pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}"
       required><small>Format: 123-456-7890</small><br>
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
    </select required><br>
    <label for="pemail">Parent Email:</label>
    <input type="email" id="pemail" name="pemail" required><br>
    <input type="submit" value="Register"></input><br><br>
  </form>
<?php else : ?>
    <form action="student_login.php">
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
