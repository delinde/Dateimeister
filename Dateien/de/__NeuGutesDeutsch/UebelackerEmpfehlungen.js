//////////////////////////////////////////////////////////////////////////
//
//
//
//
//
//
//////////////////////////////////////////////////////////////////////////



var StileNamenliste = {};
var Stilelisten = document.styleSheets;
for (var j=0; j<Stilelisten.length; j++)   {
  var Stileliste = document.styleSheets[j].rules || document.styleSheets[j].cssRules;
  for (var i=0; i<Stileliste.length; i++)
    StileNamenliste[Stileliste[i].selectorText] = Stileliste[i].style;
  }
//    zum Beispiel:
//    StileNamenliste[".Seite"].color = "#ff0000";


function Anmerkungen() { 
  StileNamenliste[".Anm"].display="block"; } 
// alert(333); 

function mitAnmerkungen() { 
  StileNamenliste[".Anm"].display="inline"; 
	StileNamenliste[".uAnm"].textDecoration = "underline"; 
	StileNamenliste[".ftAnm"].fontWeight="bold";
	document.getElementById("AnmerkungenSchalter").innerHTML = "<a href=javascript:ohneAnmerkungen()>ohne Anmerkungen</a>"; } 

function ohneAnmerkungen() { 
  StileNamenliste[".Anm"].display="none"; 
	StileNamenliste[".uAnm"].textDecoration = "none"; 
	StileNamenliste[".ftAnm"].fontWeight="normal";
	document.getElementById("AnmerkungenSchalter").innerHTML = "<a href=javascript:mitAnmerkungen()>mit Anmerkungen</a>"; } 


ohneAnmerkungen();






