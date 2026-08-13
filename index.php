<html>
<head>
<title> Grimms Märchen </title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="content-language" content="de" />
<meta http-equiv="content-script-type" content="text/javascript" />

<?php
//   ZU TUN:     die 5 Zeilen oben  nach   dieSeite.php


##############################################################
##
##   index.php
##        neu.gutes-deutsch.de
##        grimms-maerchen.de
##
##############################################################

//**/  phpinfo(); 
require_once "Verfahren.php";        //   ???   >>>
//  require_once "Verweise.php";

##  Projektdaten aus  Projektdaten.dat  lesen und als globale Variablen bereitstellen.
##  Sie stehen danach in  dieSeite.htm  als  $Projektname,  $Projektname2,  $ProjektURL  zur Verfügung.
$_pd        = leseProjektdaten();
$Projektname  = $_pd['Projektname']  ?? 'Mein Projekt';
$Projektname2 = $_pd['Projektname2'] ?? '';
$ProjektURL   = $_pd['ProjektURL']   ?? '';


$Sendgroeszen = preg_split(",\?,", $_SERVER["REQUEST_URI"]);

$berichten = false;
//**/$berichten = true;

function Echon($nr, $was)  {
  global $berichten, $$was;
  if ($berichten)
    echo "($nr)  $was: <b>" . $$was . "</b><br>\n"; }

function Echonn($was)  {
  global $berichten;
  if ($berichten)
    echo "$was<br>\n";   }
                         
function z($zeichen, $wieoft)  {
	$zz = "";
	for ($i=0; $i<=$wieoft; $i++)
	  $zz .= $zeichen;
	return $zz;    }

function Print_rr($nr, $zeichen, $wasName, $was)  {
  global $berichten;
	if ($berichten) {
    echo "\n<br>($nr) "  . z($zeichen, 20) . "  $wasName  "  . z($zeichen, 20) . "<br>\n";
		print_r($was); 
    echo "\n<br>(/$nr) " . z($zeichen, 20) . "  /$wasName  " . z($zeichen, 20) . "<br>\n";
  }  }



foreach (preg_split(",&,", $Sendgroeszen[1]) as $Paar)  {
	list($schluessel, $wert) = preg_split(",=,", $Paar);
	$wert = preg_replace(',%2C,', ',', $wert);
	$$schluessel = preg_replace(',\,,', '.', preg_replace(',\.,', '', $wert));
	//  echo $schluessel .": <b> ".  $$schluessel ."</b><br>";
	}




//if ( .match(/josephspfennig/i) {
//  $holDatei = "josephspfennig";
//  eval("gibAus(\"".holeDatei($holDatei)."\");");   //    Dateien/dieSeite.htm   enthält   die Seite
//  exit; }




$wasalles = strtolower($_SERVER["REQUEST_URI"]);          //       ==>    wasalles
      Echon(0.01, "wasalles");
#,,,$wasalles = $_SERVER["REQUEST_URI"];        //*****************
#,,,$wasallesse = explode("?", $wasalles);
#,,,$wasalles = strtolower($wasallesse[0]);
#,,,$Sendgroeszen = $wasallesse[1];
     /**/   Echon(0.01, "Sendgroeszen");
     /**/   Echon(0.02, "wasalles");
$wasalles = trim($wasalles, "/ ");
     /**/   Echon(0.02, "wasalles");
##  Pfad-Präfix abschneiden (z.B. "Sachen/" aus "Sachen/Sache1"):
##  Der Präfix wird nur beim Dateiladen wieder benötigt; der Wegweiser-Lookup
##  arbeitet ausschließlich mit dem letzten Segment (dem Dateinamen).
$was_pfadpraefix = "";
if (strpos($wasalles, "/") !== false) {
    $pfadteile = explode("/", $wasalles);
    $wasalles = array_pop($pfadteile);        // letzter Teil = Dateiname
    $was_pfadpraefix = implode("/", $pfadteile) . "/";
}
#))))))))))))))))))))))) if (preg_match(",^(\w\w)\b\/(.*),",  $wasalles, $Funde) )  {        //  z.B.  de, en, fr, no ...
#)))))))))))))))))))))))   //  echo "<br>preg_match \\w\\w\/<br>";
if (preg_match(",\?,",  $wasalles, $Funde) )  {
  list($wasalles, $Sendpaare) = explode("?", $wasalles);
	/**/   Echon(0.2, "wasalles");
	/**/   Echon(0.3, "Sendpaare");
  $Sendpaare = explode("&", $Sendpaare);
	Print_rr("(0.4)", "=", "Sendpaare", $Sendpaare);
	foreach ($Sendpaare as $Sendpaar)  {
	  list ($SendgroeSze, $Sendwert) = explode("=", $Sendpaar);
		$$SendgroeSze = $Sendwert;
	  /**/   Echonn("(0.3) Sendgröße:<b> $" . $SendgroeSze . "</b>» » Sendwert:<b> " . $Sendwert . "</b>");
	} }


//     S p r a c h e  herausfinden:
if ($Sprache) {     }                            //   ==>  $Sprache
elseif (preg_match(",^(\w\w)\b\/(.*),",  $wasalles, $Funde) )  {       //  2 führende Buchstaben, dann Schrägstrich
  //  Echon("preg_match \\w\\w\/";
  $Sprache = $Funde[1];
  $wasalles = $Funde[2];
		//**/     Echon("(0.2) wasalles:<b> " . $wasalles . "</b>";
  }
else
  $Sprache = "de";
$Spracheteil = "<input type='hidden' name='Sprache' value='$Sprache'> ";
/**/   Echon(0.3, "Sprache");
$Suchkette = $_REQUEST["Suchkette"];
/**/   Echon(0.31, "Suchkette");
$Suchenteil1 .=        ##    das Ding mit der  L u p e :
  "  <input type='hidden' value='SEARCH' name='action'>\n" .
  "  <input type='hidden' value='999'    name='limit' >\n" .
	"  <input type='text' name='Suchkette' class='text' size='14' maxlength='30' value='$Suchkette' style='background:#f0f8ff;'>&nbsp;\n".
  "  <input type='image' title='Suche'  name='search' src='Bilder/Lupe.gif' alt='Suche' border='0' style='margin-bottom:-8px'/>";

 
//    S u c h e :
       Echon(0.41,  "wasalles");
//     Echon(0.42,  "suchkette");
       Echon(0.5,  "action");
if (($action == "search" || $wasalles == "suche") && $suchkette != "")  {       ##      suchkette gibs nich!!  Alles fraglich!!
  require("suche.php");
	}                      //  Suche durchführen
else  {                  //  Seite laden

    Echon(1, "Sprache");
    Echon(1.1, "was");
    Echon(1.2, "wasalles");
    Echon("2.0000)", "wasalles"); 
    Echon("2.0000)", "wasalles"); 

//  suche Länderkennung   de, no, fr, en, ...

if ($wasalles == "")     ////   wenn nix angegeben ist, dann "Willkommen"
  $wasalles = "Willkommen";

## # # # # # #    
## Hier an dieser Stelle gilt es nun, den 
##             W e g w e i s e r   t e i l w e i s e  zu bauen, damit    nicedit, Nicedit und NicEdit   auseinandergehalten werden können.
## # # # # # # 
require("Wegweisermeister.php");     //    Dort wird   Wegweisermeister.dat   eingelesen.                                                          


/**/   Echonn("(2.1) Sprache: <b>$Sprache</b>; " . /*  was: <b>" . $was .*/  "</b> wasalles:<b> " . $wasalles . "</b>");
$wasallesKl =         strtolower($wasalles);                                       //   dieseite  =>  Dieseite
$wasalles1G = ucfirst(strtolower($wasalles));                                       //   dieseite  =>  Dieseite
//\\//   $wasalles   = ucfirst(strtolower($wasalles));                                       //   dieseite  =>  Dieseite
        Echon("2.1111)", "wasalles1G"); 
        Echon("2.1111)", "wasalles1G"); 
//  Abfrage, ob diese Seite überhaupt im Wegweisermeister.dat genannt wird:
if(!array_key_exists($wasallesKl, $VerweiseKleinNummern))  {
  Echonn("wasalles <b>AUSGEFALLEN</b>");
  Echonn("wasalles <b>AUSGEFALLEN</b>");
  $wasallesAusgefallen = $wasalles;   }     //    Der Wegweiser wird dann nur mit 'Willkommen' gebildet.   Besser:   >>> >>> SeiteNichtGefunden >>> >>>

if ( $wasalles1G != $Verweise[$VerweiseNummern[$wasallesKl]]["Forumname"]) {  //   also bei Binnen-Großbuchstaben, z.B. jQuery und JsUndCss und CSS
  // Print_rr("(2.22222222)", "*", "Verweise", $Verweise);
   Print_rr("(2.22222222)", "*", "VerweiseNummern", $VerweiseNummern);
   Print_rr("(2.33333333)", "#'", "array_column (\$Verweise, ForumnameKlein')", array_column($Verweise, "ForumnameKlein"));
  Echon("2.4444,iffffff)", "wasallesKl"); 
  Echon("2.4444,iffffff)", "wasallesKl"); 
	Echonn("2.5555, ifffff) VerweiseKleinNummern['$wasallesKl']]                  :  " . $VerweiseKleinNummern[$wasallesKl]);
	Echonn("2.5555, ifffff) VerweiseKleinNummern['$wasallesKl']]                  :  " . $VerweiseKleinNummern[$wasallesKl]);
	Echonn("2.6666, ifffff) Verweise [$VerweiseKleinNummern[wasallesKl]]['Forumname']  <b>YYYYY</b> " .$Verweise [$VerweiseKleinNummern[$wasallesKl]]['Forumname']);
	Echonn("2.6666, ifffff) Verweise [$VerweiseKleinNummern[wasallesKl]]['Forumname']  <b>YYYYY</b> " .$Verweise [$VerweiseKleinNummern[$wasallesKl]]['Forumname']);
	$wasalles2 = $Verweise[$VerweiseKleinNummern[$wasallesKl]]["Forumname"];        //             => dieSeite
  Echon("2.8888,iffffff)", "wasalles2");      
  Echon("2.8888,iffffff)", "wasalles2");  
	$wasalles = isset($wasalles2) && $wasalles2 ? $wasalles2 : "NichtGefunden";
  }
        Echon(2.2, "Sprache");
        Echon(2.3, "wasalles");
//    wasalles_Liste   kommt hier doppelt vor: bei 3.111  und bei   111
$wasalles_Liste = array_reverse(preg_split(";[,\;];", $wasalles));  //  war mit Punkt:  preg_split(";[,.\;/];", $wasalles)
#wasalles_Liste = array_reverse(preg_split(";[,.\;];", $wasalles));
$was = $wasalles_Liste[0];
       //          --------- wasallesListe  ... ist kein so schöner Name ... die Liste wird gebaut in Wwm.php, xxxx ------------");
       Print_rr(3.111, "-", "wasalles_Liste", $wasalles_Liste);
       Echon(4, "Sprache");
       Echon(4.1, "was");
       Echon(4.2, "wasalles");

$was = $wasalles_Liste[0];
       Echon(4.3, "was");
$was = $Sprache . "/" . $was;
       Echon(5.1, "was");
       Echon(5.2, "was_drueber");
list($was_Land, $was_DName) = explode("/", $was);      // Land, Dateiname
       Echon(6, "was_Land");

//////  .................................................   php ... Josephspfennig ....
// //    $wasStil   = "Stil".$was_DName;           
$wasStil   = "Stil" . preg_replace(",\.php,", "", $was_DName);  
$$wasStil  = " class='jetztDick'";    //  z.B. StilBilderrahmen
       Echon(6.1, "was");
       Echonn ("(8)    \$wasStil: <b> $wasStil </b>    >>>         \$\$wasStil: <b>" . $$wasStil . "</b>" );
$was_holDatei = $was_pfadpraefix ? $Sprache . "/" . $was_pfadpraefix . $was_DName : $was;
## Mehrfach-Treffer ohne Pfad-Präfix → Auswahlseite anzeigen
if (!$was_pfadpraefix) {
    $wasKlein    = strtolower($was_DName);
    $doppelNamen = [];
    foreach ($Verweise as $_i => $_e) {
        if (strtolower($_e["Forumname"]) === $wasKlein) {
            $_elternNr   = AhnNrVonNr($_i);
            $_elternName = ($_elternNr >= 0 && $Verweise[$_elternNr]["Tiefe"] >= 1)
                           ? $Verweise[$_elternNr]["Forumname"] : null;
            $doppelNamen[] = ["name" => $_e["Forumname"], "eltern" => $_elternName];
        }
    }
    if (count($doppelNamen) > 1) {
        $auswahl = "<p><b>Bitte wählen Sie:</b></p>\n<ul style=line-height:2em>\n";
        foreach ($doppelNamen as $_t) {
            $_url     = $_t["eltern"] ? "/" . $_t["eltern"] . "/" . $_t["name"] : "/" . $_t["name"];
            $auswahl .= "<li><a href=$_url>https://dm.fl.de$_url</a></li>\n";
        }
        $auswahl .= "</ul>\n";
        $was_Datei = $auswahl;
    } else {
        $was_Datei = holeDatei($was_holDatei);
    }
} else {
    $was_Datei = holeDatei($was_holDatei);
}
       Echonn("(9) \$was_Datei <i>(Inhalt):</i>  <hr>$was_Datei<hr>");

## Editor-Script: nur einblenden, wenn eine echte Inhaltsdatei geladen wurde
## (keine Fehlerseite, keine Auswahlseite, keine .php-Datei).
$DM_EditorScript = '';
if ($was_holDatei
    && !preg_match(',\.php$,i', $was_holDatei)
    && strpos((string)$was_Datei, 'Datei nicht gefunden:') === false
    && strpos((string)$was_Datei, 'Bitte wählen Sie')      === false) {
    ## Keine doppelten Anführungszeichen im JS nötig → kein Escaping für eval
    $pfadJS = str_replace("'", "\\'", $was_holDatei);
    $DM_EditorScript = "<script>var DM_Pfad='$pfadJS';</script>"
                     . "<script src='/editor.js'></script>";
}

$wasTitel  = $was_DName;

## Bestimme den numerischen Index von $was_DName im Wegweiser-Baum.
## Bei gleichnamigen Einträgen (z.B. Sache1 unter SachenA und SachenB) wird
## über $was_pfadpraefix der richtige Ast gesucht.
$was_DName_Nr = -1;
if ($was_pfadpraefix) {
    $pfadteile = array_filter(explode("/", rtrim($was_pfadpraefix, "/")));
    if ($pfadteile) {
        $elternKl = strtolower(end($pfadteile));
        $elternNr = $VerweiseKleinNummern[$elternKl] ?? -1;
        if ($elternNr >= 0) {
            $eTiefe = $Verweise[$elternNr]["Tiefe"];
            $wasKl  = strtolower($was_DName);
            for ($i = $elternNr + 1; isset($Verweise[$i]) && $Verweise[$i]["Tiefe"] > $eTiefe; $i++) {
                if ($Verweise[$i]["Tiefe"] == $eTiefe + 1
                    && strtolower($Verweise[$i]["Forumname"]) == $wasKl) {
                    $was_DName_Nr = $i;
                    break;
                }
            }
        }
    }
}
Echonn("(9.1) \$was_DName_Nr: <b>$was_DName_Nr</b>");

## Wenn "Datei nicht gefunden" – aber der Wegweiser-Knoten hat Kinder:
## statt Fehlermeldung eine freundliche Auswahlseite mit den direkten Kindern zeigen.
if (strpos($was_Datei, "Datei nicht gefunden:") !== false) {
    $nodeNr = $was_DName_Nr >= 0 ? $was_DName_Nr : ($VerweiseNummern[$was_DName] ?? -1);
    if ($nodeNr >= 0 && isset($Verweise[$nodeNr + 1])
        && $Verweise[$nodeNr + 1]["Tiefe"] > $Verweise[$nodeNr]["Tiefe"]) {
        $nodeTiefe = $Verweise[$nodeNr]["Tiefe"];
        $auswahl   = "<p><b>Bitte wählen Sie:</b></p>\n<ul style=line-height:2em>\n";
        for ($k = $nodeNr + 1; isset($Verweise[$k]) && $Verweise[$k]["Tiefe"] > $nodeTiefe; $k++) {
            if ($Verweise[$k]["Tiefe"] == $nodeTiefe + 1) {
                $kindLang = $Verweise[$k]["ForumnameLang"] ?: $Verweise[$k]["Forumname"];
                $url      = "/" . vollerPfad($k);
                $auswahl .= "<li><a href=$url>$kindLang</a></li>\n";
            }
        }
        $auswahl  .= "</ul>\n";
        $was_Datei = $auswahl;
    }
}

$ii = 0;


### # # # # # # # #
   // *******************   Ww    *******************************
   // *******************   Wegweweiserbau     ******************
//   require("Wegweisermeister.php");     //    Dort wird   Wegweisermeister.dat   eingelesen.

   $wasalles_Liste = $was_DName_Nr >= 0 ? AhnenVonNr($was_DName_Nr) : Ahnen($was_DName);
 
   Print_rr(111.1, ".", "wasalles_Liste", $wasalles_Liste);
   $was_Wegweisername = $was_DName;
   Print_rr(112, ".", "VerweiseKleinNummern", $VerweiseKleinNummern);
                 //\\//\\//.....................    if(!array_key_exists($wasallesKl, $VerweiseKleinNummern)) 
                 //\\//\\//.....................  //  $was_Wegweisername = "willkommen";    // >>> >>> besser: SeiteNichtGefunden 
                 //\\//\\//.....................      $was_Wegweisername = "geschenke";    // >>> >>> besser: SeiteNichtGefunden 
    // $wasallesAusgefallen = $wasalles;   }     //    Der Wegweiser wird dann nur mit 'Willkommen' gebildet.   Besser:   >>> >>> SeiteNichtGefunden >>> >>>

   /**/ Echonn("\n<br> (113) ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::");
   /**/ Echonn("\n  (113) ::::::::::::  baueWegweiserFuerAhnen('$was_DName') :::::::::::::::<br>\n");
                //\\//\\//.....................   baueWegweiserFuerAhnen($was_Wegweisername);
                baueWegweiserFuerAhnen($was_DName, $was_DName_Nr);
         //     baueWegweiserFuerAhnen("willkommen");
   /**/ Echonn("\n  (/113) ::::::::::::  /baueWegweiserFuerAhnen('$was_DName') :::::::::::::::\n");
   /**/ Echonn("\n  (/113) :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::<br>\n");
### # # # # # # # #



   Print_rr(111.2, ".", "wasalles_Liste", $wasalles_Liste);



while(($was_drueber = array_shift($wasalles_Liste)) !== null) {
#hile($was_drueber = array_pop($wasalles_Liste)) {
  $was_drueberWw  = "Ww".$was_drueber;  //  z.B. WwWerkstatt   für  WwWerkstatt.htm
/***********************************************/    
/***********************************************/    
	Echonn("10 was_drüber: $was_drueber");
  Echonn("11 was_drueberWw: $was_drueberWw");
  Echonn("12 Sprache: $Sprache");
  Echonn("\n");

  #:  eval("\$\$was_drueberWw = \"" .holeDatei($Sprache.'/'.$was_drueberWw) . "\";");  // uui uui
  Echonn("<br>\n>>> $$was_drueberWw =                           \"\$was_drueberWw\"     ;");  // wie  gelernt bei Woltlab
  //     ------------------   eval    ("\$\$was_drueberWw =                           \"\$was_drueberWw\"     ;");  // wie  gelernt bei Woltlab
  Echonn("(174) ---------------------------------------------------------------");
  Echonn("(174)  \$\$was_drueberWw:  " .  $$was_drueberWw);
  Echonn("(/174) ---------------------------------------------------------------");
//  eval    ("\$WwWerkstatt     =                           \"$WwWerkstatt\"        ;");
/**/
//  eval    ("\$WwWerkstatt     =                           \"$WwWerkstatt\"        ;");
  eval    ("\$WwWegweiser     =                           \"$WwWegweiser\"        ;");
  global $WwWegweiser; 
/*
  Echonn("(176) ------------------      WwWegweiser  --------------------------------------------");
  Echonn("(176)  \$WwWegweiser: $WwWegweiser");
  Echonn("(/176) ---------------------------------------------------------------");
*/

	} }

/**xxxx**????**/   $Ww   = "Ww".$was_DName;      // <--   wird auch gebraucht



###################################################################################
//    Datei-Name mit Ww
$DN_Ww= $Sprache ."/Ww".$was_DName;  //  Datei-Name mit Ww,   z.B. de/WwWerkstatt   für  de/WwWerkstatt.htm
Echon(13, "DN_Ww");
Echon(14, "was_DName");

//&      Daten vom Wegweisermeister holen ...
Echonn("15, was:   $was_DName");

$Ww   = "Ww".$was_DName;      // <--   wird auch gebraucht
Echon(16, "was_DName");
Echon(17, "Ww"."was_DName");
Echon(18, "Ww");

/*
Echonn("(177) ---------------------------------------------------------------");
Echonn("(177)  \$WwWerkstatt: $WwWerkstatt");
Echonn("(/177) ---------------------------------------------------------------");


$WwFarbmeister = "YYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYY";
$WwCSS = "YYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYY";
$WwDingsen = "YYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYY";
Echonn("<br><br><br><br><br>\n\n\n(178) ---------------------------------------------------------------");
print("\$WwWerkstatt = \"\$WwWerkstatt\";");
eval("\$WwWerkstatt = \"\$WwWerkstatt\";");
Echonn("<br>\n(178) ---------------------------------------------------------------");
Echonn("<br>\n(178)  \$WwWerkstatt: $WwWerkstatt");
Echonn("<br>\n(/178) ---------------------------------------------------------------");
*/
##########################################################################################################################

//\\//\\//   ?=??????????????????????????????????????         eval("\$\$Ww   = \"" .holeDatei($DN_Ww) . "\";");  // uui uui

$holDatei = $Sprache . "/dieSeite";
Echonn("<br><br> 6665554444444444444444 ******************************************************************");
Echonn("gibAus(\"".holeDatei($holDatei)."\");");   //    Dateien/dieSeite.htm   enthält   die Seite
Echonn("<br><br> 6665554444444444444444 ******************************************************************");
eval("gibAus(\"".holeDatei($holDatei)."\");");   //    Dateien/dieSeite.htm   enthält   die Seite
Echonn("<br><br> 5554444444444444444 ******************************************************************");

/*  F R A G E :
Wie mache ich es, daß der linke Wegweiser aus dem Ordner-Verbund gebildet wird?

Ordnernamen werden gelesen;
Dateinamen .htm werden gelesen;
Ordner HierNicht  wird nicht genommen;
*/


?>


