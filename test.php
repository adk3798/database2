<!DOCTYPE html>
<html lang="en">
<!--
File: test page for database 2 project
Adam M. King, UMass Lowell Computer Science Student
adam_king@student.uml.edu
uml.cs username: aking
Updated February 24th, 2020
-->
<head>
    <title>Database 2</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <?php

      $mysqli = new mysqli('localhost', 'root', '', 'db2_project'); //The Blank string is the password

      $query = "SELECT * FROM users"; //You don't need a ; like you do in SQL
      $result = $mysqli->query($query);

      echo "<table>"; // start a table tag in the HTML

      while($row = mysqli_fetch_array($result)){   //Creates a loop to loop through results
        echo "<tr><td>" . $row['id'] . "</td><td>" . $row['email'] . "</td>
              <td>" . $row['password'] . "</td><td>" . $row['name'] . "</td>
              <td>" . $row['phone'] . "</td></tr>";  //$row['index'] the index here is a field name
      }

      echo "</table>"; //Close the table in HTML

      $mysqli->close();

    ?>
</body>
</html>
