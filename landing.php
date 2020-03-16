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
  <?php else : ?>
    <h1> Login or Register </h1>
    <form action="student_registration.php">
      <input type="submit" value="Register a Student"></input><br>
    </form>
    <form action="student_login.php">
      <input type="submit" value="Login as Student"></input><br><br>
    </form>
    <form action="parent_registration.php">
      <input type="submit" value="Register a Parent"></input><br>
    </form>
    <form action="parent_login.php">
      <input type="submit" value="Login as Parent"></input><br><br>
    </form>
  <?php endif; ?>
  <?php if(isset($_SESSION['name'])) : ?>
    <form action="logout.php">
      Logout:
      <input type="submit" value="Logout"></input><br><br>
    </form>
  <?php endif; ?>
    <?php

      $mysqli = new mysqli('localhost', 'root', '', 'db2_project'); //The Blank string is the password

      $query = "SELECT * FROM users"; //You don't need a ; like you do in SQL
      $result = $mysqli->query($query);
      echo "<h1>Users</h1>";
      echo "<table>"; // start a table tag in the HTML

      while($row = mysqli_fetch_array($result)){   //Creates a loop to loop through results
        echo "<tr><td>" . $row['id'] . "</td><td>" . $row['email'] . "</td>
              <td>" . $row['password'] . "</td><td>" . $row['name'] . "</td>
              <td>" . $row['phone'] . "</td></tr>";  //$row['index'] the index here is a field name
      }

      echo "</table>"; //Close the table in HTML
      $query = "SELECT parent_id FROM parents"; //You don't need a ; like you do in SQL
      $result = $mysqli->query($query);
      echo "<h1>Parents</h1>";
      echo "<table>"; // start a table tag in the HTML

      while($row = mysqli_fetch_array($result)){   //Creates a loop to loop through results
        echo "<tr><td>" . $row['parent_id'] . "</td></tr>";  //$row['index'] the index here is a field name
      }

      echo "</table>"; //Close the table in HTML
      $query = "SELECT * FROM students"; //You don't need a ; like you do in SQL
      $result = $mysqli->query($query);
      echo "<h1>Students</h1>";
      echo "<table>"; // start a table tag in the HTML

      while($row = mysqli_fetch_array($result)){   //Creates a loop to loop through results
        echo "<tr><td>" . $row['student_id'] . "</td><td>" . $row['grade'] . "</td>
              <td>" . $row['parent_id'] . "</td></tr>";  //$row['index'] the index here is a field name
      }

      echo "</table>"; //Close the table in HTML

      $mysqli->close();

    ?>
</body>
</html>
