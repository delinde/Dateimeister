<?php
      $s_skip = [];      //  überspringen
      $count_hits = 0;
      $limit = 999;
      $my_root = "./";
			$dir = "./";
      $handle = opendir($dir);
      while($file = readdir($handle)) {
// echo " &nbsp; &nbsp; &nbsp; &nbsp;    (1b) file:  $file; keywords: $keywords<br>";
        #/**/ echo "<br><span style=color:#99d> (2a) [ dir / file : <b>$dir / $file</b> ] </span>    ";
				if(strlen($file)==2 and $file != $Sprache)  {
          #/**/ echo "<span style=color:#99d> (2aaa) [ \$file: <b>$file</b> ]; \$Sprache: $Sprache </span>";
          continue;     // keine fremden Sprachen durchsuchen
          }
        if(in_array($file, $s_skip))     continue;     // Alles in $skip auslassen
				if (preg_match("/^Ww/", $file))  continue;     // WwLalala.htm
				elseif($count_hits>=$limit)      break;        // Maximale Trefferzahl erreicht
				elseif(is_dir($my_root.$dir."/".$file)) {      // Unterverzeichnisse durchsuchen
					$s_dirs = array("$dir/$file");               // search_dir() rekursiv auf alle Unterverzeichnisse aufrufen
					search_dir($my_server, $my_root, $s_dirs, $s_files, $s_skip, 
					     $message_1, $message_2, $no_title, $limit_extracts, $byte_size, $_REQUEST);
					}
				elseif(preg_match("/($s_files)$/i", $file)) {  // Alle Dateien gemäß Endungen $s_files
					$fd=fopen($my_root.$dir."/".$file,"r");
					$text=fread($fd, $byte_size);                // 50 KB
          $keyword_html = htmlentities($keyword);
					if($case)                                   // Groß/klein-Schreibung berücksichtigen?
						$do=strstr($text, $keyword)||strstr($text, $keyword_html);
					else 
						$do=stristr($text, $keyword)||stristr($text, $keyword_html);
					if($do)	{
					  // echo "<br><b>gefunden: $keyword;</b> ";
						$nr = 0;       //  Trefferzahl in dieser Datei
						$count_hits++; //  Trefferdateien zählen
						// $echo .=  "count_hits: <b>$count_hits</b><br>";
						if(preg_match_all(",<title[^>]*>(.*)</title>,siU", $text, $titel)) { // Generierung des Link-Textes aus <title>...</title>
							if(!$titel[1][0]) // <title></title> ist leer...
								$link_title=$no_title; // ...also $no_title
							else
								$link_title="&#132;".$titel[1][0]."&#147;";  // <title>...</title> vorhanden...
							}
						else {
							$link_title=$no_title; // ...ansonsten $no_title
							}
						#$echo .=  "<p class=\"result\"><a href=\"" .  /*$my_server$dir/$file\"*/ 
						#  "$Skriptpfad/$dir/$file\" target=lalala \"_self\" class=\"result\">" .
						#  "Datei Nr. <b>$count_hits</b>;  Datei: $dir/<b>$file</b>   ".
						#  "Titel der Netzseite: <b>$link_title</b></a></p><p style=margin-top:0px>"; // Ausgabe des Verweises
 						$DateiOhneEndung = preg_replace(",\.htm,", "", $file); 
						$echo .=  "<p class=\"result\"><a href=\"" .
						  "$DateiOhneEndung\" target=lalala \"_self\" class=\"result\">" .
						  "<br><span style=color:#89b;>Datei Nr. <b>$count_hits</b>,</span>  Datei: $dir/<b>$file</b>   ".
						  "Titel der Netzseite: <b>$link_title</b></a></p><p style=margin-top:0px>"; // Ausgabe des Verweises
						$auszug = strip_tags($text);
						$keyword = preg_quote($keyword); // unescapen
            //  echo "(2) $ keywords: $keywords<br>";
            //  echo "(2a) $ keyword: $keyword <br>";
            $keyword = str_replace("/","\/","$keyword");
						$keyword_html = preg_quote($keyword_html); // unescapen
						$keyword_html = str_replace("/","\/","$keyword_html");
						//   p  ...        $echo .=  "<span class=\"extract\">";
						if(preg_match_all("/((\s\S*){0,3})($keyword|$keyword_html)((\s?\S*){0,4})/i", $auszug, $match, PREG_SET_ORDER)); {
							if(!$limit_extracts)
								$number=count($match);
							else
								$number=$limit_extracts;
							for ($h=0;$h<$number;$h++) { // Kein Limit angegeben, also alle Vorkommen ausgeben
								if (!empty($match[$h][3]))
								  $nr++; //  wie viele Fundstelle in dieser Datei:
									//  Fundstellen-Wiedergabe:
									$echo .= sprintf("<p class=\"Fundstelle\"><span style=color:#778899>Fundstelle $nr: </span>  ... %s<b>%s</b>%s ... &nbsp; &nbsp; ", $match[$h][1], $match[$h][3], $match[$h][4]) . "</p>";
							} }
						$echo .=  "</>";
						flush();
						}
					fclose($fd);
        } }
	  		@closedir($handle);
 
