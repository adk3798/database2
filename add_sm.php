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
  <?php
    if (isset($_POST['submit'])) {
      unset($_SESSION['mid']);
      $_SESSION['mid'] = $_POST['submit'];
      unset($_SERVER["REQUEST_METHOD"]);
    }
  ?>
  <h1> Make Assignment </h1>

  <?php
    // define variables and set to empty values
    $email = $password = $name = $pid = $phone = "";
    $valid = false;

    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
      $title = scrub_input($_POST["title"]);
      $author = scrub_input($_POST["author"]);
      $type = scrub_input($_POST["type"]);
      $url = scrub_input($_POST["url"]);
      $notes = scrub_input($_POST["notes"]);
      $today = date("Y-m-d");
      $mid = $_SESSION['mid'];

      $mysqli = new mysqli('localhost', 'root', '', 'db2_project'); //The Blank string is the password

      $query = "INSERT INTO material (title, author, type, url, assigned_date, notes)
                VALUES ('{$title}', '{$author}', '{$type}', '{$url}', '{$today}', '{$notes}')";
      $result = $mysqli->query($query);

      $material_id = $mysqli->insert_id;

      $query = "INSERT INTO assign (meet_id, material_id)
                VALUES ('{$mid}', '{$material_id}')";
      $result = $mysqli->query($query);

      echo "Materials added";


      $mysqli->close();
    }

    function scrub_input($data) {
      $data = trim($data);
      $data = stripslashes($data);
      $data = htmlspecialchars($data);
      return $data;
    }
  ?>

  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
    <label for="title">Title:</label>
    <input type="text" id="title" name="title" required><br>
    <label for="author">Author:</label>
    <input type="text" id="author" name="author" required><br>
    <label for="type">Type:</label>
    <input type="text" id="type" name="type" required><br>
    <label for="url">URL:</label>
    <input type="text" id="url" name="url" required><br>
    <label for="notes">Notes:</label>
    <input type="text" id="notes" name="notes" required><br><br>
    <input type="submit" value="Submit"></input><br><br>
  </form>

  <form action="study_materials.php">
    Pick Another Meeting:
    <input type="submit" value="Post Study Materials"></input><br><br>
  </form>

  <form action="landing.php">
    Return to main page:
    <input type="submit" value="Main Page"></input><br><br>
  </form>

</body>
</html>
