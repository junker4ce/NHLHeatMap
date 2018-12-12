<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>jQuery UI Datepicker - Default functionality</title>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="./css/boxscore.css">
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script>
  $( function() {
    $( "#datepicker" ).datepicker({
      onSelect: function(date) {
        getScores(date);
      },
      dateFormat: "yy-mm-dd",
      maxDate: "0"
    });
    $("#datepicker").datepicker("setDate", "-1");
    getScores($( "#datepicker" ).datepicker("getDate").toISOString().slice(0, 10));
  } );
  var games = [];
  function getScores(date) {
    var jqxhr = $.getJSON( "https://statsapi.web.nhl.com/api/v1/schedule?date="+date, function( data ) {
      games = data.dates[0].games;
    });
    jqxhr.done(function() {
      displayScores();
    })
  }

  function displayScores() {
    wipeGames();
    for (let i = 0; i<games.length; i++){
      showGame(games[i]);
    }
  }

  function showGame(game) {
    let homeTeamID = game.teams.home.team.id;
    let awayTeamID = game.teams.away.team.id;
    console.log(game);
    let boxScore = document.createElement("div");
    boxScore.className = "boxScore";
    let homeScore = document.createElement("div");
    let teamID = game.teams.home.team.id;
    homeScore.innerHTML = game.teams.home.score;
    homeScore.className = "homeScore";
    let homeLogo = "https://www-league.nhlstatic.com/nhl.com/builds/site-core/3358ed0ede20e7e33cb07ec6be0713405ff6b894_1539029735/images/logos/team/current/team-" + homeTeamID + "-dark.svg";
    homeScore.style.backgroundImage = "url('"+ homeLogo +"')";
    boxScore.appendChild(homeScore);
    let awayScore = document.createElement("div");
    awayScore.innerHTML = game.teams.away.score;
    awayScore.className = "awayScore";
    let awayLogo = "https://www-league.nhlstatic.com/nhl.com/builds/site-core/3358ed0ede20e7e33cb07ec6be0713405ff6b894_1539029735/images/logos/team/current/team-" + awayTeamID + "-dark.svg";
    awayScore.style.backgroundImage = "url('"+ awayLogo +"')";
    boxScore.appendChild(awayScore);
    $( "#boxScores" ).append( boxScore );
  }

  function wipeGames() {
    $(".boxScore").remove();
  }
  </script>
</head>
<body>

  <p>Date: <input type="text" id="datepicker"></p>
  <div id="boxScores">
  </div>
</body>
</html>
