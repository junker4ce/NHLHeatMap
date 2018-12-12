<?php
  $date = $_GET['date'];

  $host="localhost";
  $servername = "nhlstats";
  $username = "nhlReader";
  $password = "ReadTheStats";

  // Create connection
  $conn = mysqli_connect($host, $username, $password, $servername);

  // Check connection
  if (!$conn) {
      die("Connection failed: " . mysqli_connect_error());
  }

  $sql = "call nhlstats.GetGames('" . $date ."');";
  $games = $conn->query($sql);

  if ($games->num_rows > 0) {
      echo '{ "games":[ ';
      $first = true;
      while ($gameRow = $games->fetch_assoc()) {
          if ($first) {
              $first = false;
          } else {
              echo ', ';
          }
          echo json_encode($gameRow);
      }
      echo ']}';
  } else {
      echo "Error: Could not retrieve game data for " . $date;
  }
  $conn->close();
