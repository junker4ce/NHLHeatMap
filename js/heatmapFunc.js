let parsedShots = [];
let points = [];
let heatmapInstance;
let shifts;

function load(){
  let gameID = 2018020454;
  heatmapInstance = h337.create({
  // only container is required, the rest will be defaults
    container: document.querySelector('.heatmap')
  });
  getGame(gameID);
  getShifts(gameID);
}

function getGame(gameID) {
  var jqxhr = $.getJSON( "https://statsapi.web.nhl.com/api/v1/game/"+ gameID + "/feed/live",
    function( data ) {
      longGame = data.liveData;
    });
  jqxhr.done(function() {
    console.log(longGame);
    let homeID = longGame.boxscore.teams.home.team.id;
    let awayID = longGame.boxscore.teams.away.team.id;
    placeLogos(homeID, awayID);
    getShots(longGame, homeID, awayID);
  });
}

function getShots(gameData, homeID, awayID) {
  let parsedShots = [];
  let shotData = gameData.plays.allPlays.filter(p=>{
    return p.result.eventTypeId == "SHOT" || p.result.eventTypeId == "GOAL"})
  console.log(shotData);
  for (let i = 0; i < shotData.length; i++) {
    if (shotData[i].team.id == homeID){
      let x = shotData[i].coordinates.x;
      let y = shotData[i].coordinates.y;
      if (shotData[i].coordinates.x < 0) {
        x = -x;
        y = -y;
      }
      x = (x+100)*5;
      y = (y+42)*5;
      let team = shotData[i].team.triCode;
      let shot = new SHOT(x, y, team);
      parsedShots.push(shot);
      let point = {
        x: x,
        y: y,
        value: 75,
        radius: 50
      };
      points.push(point);
    } else {
      let x = shotData[i].coordinates.x;
      let y = shotData[i].coordinates.y;
      if (shotData[i].coordinates.x > 0) {
        x = -x;
        y = -y;
      }
      x = (x+100)*5;
      y = (y+42)*5;
      let team = shotData[i].team.triCode;
      let shot = new SHOT(x, y, team);
      parsedShots.push(shot);
      let point = {
        x: x,
        y: y,
        value: 75,
        radius: 50
      };
      points.push(point);
    }

  }
  console.log(parsedShots);
  let data = {
    max: 100,
    data: points
  };
  heatmapInstance.setData(data);
}

function SHOT(x, y, team) {
  this.X = x;
  this.Y = y;
  this.team = team;
}

function placeLogos(homeID, awayID) {
  let homeLogo = "https://www-league.nhlstatic.com/nhl.com/builds/site-core/3358ed0ede20e7e33cb07ec6be0713405ff6b894_1539029735/images/logos/team/current/team-" + homeID + "-dark.svg";
  let awayLogo = "https://www-league.nhlstatic.com/nhl.com/builds/site-core/3358ed0ede20e7e33cb07ec6be0713405ff6b894_1539029735/images/logos/team/current/team-" + awayID + "-dark.svg";

  $('#awayLogo').css('background-image', 'url(' + awayLogo + ')');
  $('#homeLogo').css('background-image', 'url(' + homeLogo + ')');

}

function getShifts(gameID) {
  let s = document.createElement("script");
  s.src = "https://www.nhl.com/stats/rest/shiftcharts?cayenneExp=gameId="+ gameID + "?callback=shifts";
  document.getElementsByTagName("head")[0].appendChild(s);
  console.log(s);
  console.log(shifts);
  var jqxhr = $.getJSON( "https://www.nhl.com/stats/rest/shiftcharts?cayenneExp=gameId="+ gameID,
    function( data ) {
      shiftData = data;
    });
  jqxhr.done(function() {
    console.log(shiftData);
  });
}
