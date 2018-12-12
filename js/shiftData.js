function getShifts(gameID) {
  var jqxhr = $.getJSON( "https://www.nhl.com/stats/rest/shiftcharts?cayenneExp=gameId="+ gameID,
    function( data ) {
      shiftData = data;
    });
  jqxhr.done(function() {
    console.log(shiftData);
  });
}
