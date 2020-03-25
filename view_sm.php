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

  <h1> View Study Materials </h1>

  <?php
    if (isset($_POST['submit'])) { // submit being set means mentee
      $mysqli = new mysqli('localhost', 'root', '', 'db2_project'); //The Blank string is the password

      $meet_id = $_POST['submit'];

      $query = "SELECT * FROM assign WHERE meet_id='$meet_id'";
      $result = $mysqli->query($query);

      echo "<table>";

      echo "<tr><td>" . "title" . "</td><td>" . 'author' . "</td>
            <td>" . 'type' . "</td><td>" . 'url' . "</td>
            <td>" . 'assigned date' . "</td><td>" . 'notes' . "</td></tr>";

      while($row = mysqli_fetch_array($result)){
        $material_id = $row['material_id'];
        $material_query = "SELECT * FROM material WHERE material_id=$material_id";
        $material_result = $mysqli->query($material_query);
        $material_row = mysqli_fetch_array($material_result);

        echo "<tr><td>" . $material_row['title'] . "</td><td>" . $material_row['author'] . "</td>
              <td>" . $material_row['type'] . "</td><td>" . $material_row['url'] . "</td>
              <td>" . $material_row['assigned_date'] . "</td><td>" . $material_row['notes'] . "</td></tr>";

      }
      echo "</table><br><br>";
      $mysqli->close();
    }
  ?>

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
