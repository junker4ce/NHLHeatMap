<!DOCTYPE html>
<html>
<body>
<?php
  $host="localhost";
  $servername = "nhlstats";
  $username = "nhlBuilder";
  $password = "BuildTheStats";

  // Create connection
  $conn = mysqli_connect($host, $username, $password, $servername);

  // Check connection
  if (!$conn) {
      die("Connection failed: " . mysqli_connect_error());
  }

  $delSQL = "DELETE FROM nhlgames";
  $query = $conn->query($delSQL);

  $content = file_get_contents("http://statsapi.web.nhl.com/api/v1/schedule?expand=schedule.linescore&startDate=2018-10-02&endDate=" . date("Y-m-d"));
  $result = json_decode($content);

  foreach ($result->dates as $date) {
      $insertSQL = "INSERT INTO nhlgames (`gameID`, `hTeamID`, `aTeamID`, `date`, `hScore`, `hShots`, `aScore`, `aShots`) VALUES ";
      $count = 0;
      foreach ($date->games as $game) {
          if ($game->gameType == "R") {
              $gameDate = $game->gameDate;
              //$gameDate = str_replace("T", " ", $game->gameDate);
              //$gameDate = str_replace("Z", "", $gameDate);
              if ($count == 0) {
                  $insertSQL .= "('" . $game->gamePk . "', '"
                             . $game->teams->home->team->id . "', '"
                             . $game->teams->away->team->id . "', '"
                             . $gameDate . "', '"
                             . $game->teams->home->score . "', '"
                             . $game->linescore->teams->home->shotsOnGoal . "', '"
                             . $game->teams->away->score . "', '"
                             . $game->linescore->teams->away->shotsOnGoal . "')";
              } else {
                  $insertSQL .= ", ('" . $game->gamePk . "', '"
                               . $game->teams->home->team->id . "', '"
                               . $game->teams->away->team->id . "', '"
                               . $gameDate . "', '"
                               . $game->teams->home->score . "', '"
                               . $game->linescore->teams->home->shotsOnGoal . "', '"
                               . $game->teams->away->score . "', '"
                               . $game->linescore->teams->away->shotsOnGoal . "')";
              }
              $count++;
          }
      }
      echo $insertSQL;
      echo "<br><BR>";
      //$query = $conn->query($insertSQL);
  }

?>

</body>
</html>
