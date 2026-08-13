#!/usr/bin/perl
#########################################################################################################
##    Der Umsetzer  Doofschreib -> Rechtschreib
#########################################################################################################
$/ = chr(10);
#$\ = "\n<br>";

use CGI qw/:standard/;
print "content-type: text/html\n\n";
print '<head></head><body>';

$q = new CGI;
foreach $c($q->param)  {
     $q{$c} = $q->param($c)  }

$_ = $q{Reformschrieb};

### ==
sub sz { $Kt = shift; $Kt =~ s/ss/ß/;  $Kt }  ##  Verfahren: Seltenes umsetzen

s,\bAss\b,As,g;
s,\bAlbtraum\b,Alptraum,g;
s,sodass\b,so dass,g;
s,(S|s)tattdessen,$1tatt dessen,g;
s,(A|a)ufw.ndig,$1ufwendig,g;
s,([Nn])ummerier,$1umerier,g;
s,([Pp])latzier,$1lazier,g;
s,([Pp])otenziell,$1otentiell,g;
s,([Pp])otenzial,$1otential,g;
s((E|e)in jedes Mal)($1in jedes# Mal)g;   ##  schützen
s((B|b)isschen)($1ißchen)g;
s((J|j)edes Mal)($1edesmal)g;
s((V|v)oran treiben)($1orantreiben)g;     
s(ssch)(s#sch)g;                 ##  Seltenes schützen
s(Ausstieg)(Aus#stieg)g;         ##  Seltenes schützen
s(Bussteig)(Bus#steig)g;         ##  Seltenes schützen
s(Bundesstaat)(Bundes#staat)g;   ##  Seltenes schützen
s((s|S)trasse)($1traße)g;
s((h|H)asserf)($1aßerf)g;
s,(Fassentnahme|Messergebnis|Passersatz|schlussendlich),sz($1),ge;    ## Seltenes umsetzen
s/reformierter/herkömmlicher = moderner/;   ##  Diese Zeile nur für diese Zeige-Fassung!
s/ss\b/ß/g;                      ##  ss am Wortende  ->  ß
s/sss/ßs/g;                      ##  sss -> ßs
s/ussa/ußa/g;                    ##  essta  -> eßta
s/([mM])issacht/$1ißacht/g;                      ##  ss am Wortende  ->  ß
s/((?:[^GB]|\A)[bcdfghjklmnpqrstvwxyz][aeiouäöü])ss([bcdfghjklmnpqrstvwxyz])/$1ß$2/gi;  ##  Glasstück, Grasstück
s,([eio])\1\1,"$1$1-".uc($1),ge;            ##  Teeei ->Tee-Ei
s/(.)\1\1([aeiouyäöü])/$1$1$2/g; ##  mmme -> mme
s/(\d+)-(jähr|mal|fach|er)/lc("$1$2")/gei;
s/so genannt/sogenannt/g;
s,(S|s)elbstständig,$1elbständig,g;
s,rgendetwas,rgend etwas,g;
s,rgendeiner,rgend einer,g;
s/zurzeit/zur Zeit/g;
s/(G|g)räulich/$1reulich/g;
s,Tipp\b,Tip,g;
s/(lles beim Alten)/lc($1)/ge;
s/([Ii])(m Allgemeinen)/$1.lc($2)/ge;
s/([Ii])(m Wesentlichen)/$1.lc($2)/ge;
s/#//g;

$Vorlage = "../html/Umsetzer/Umsetzer.html";
open D_E, "$Vorlage" or print "nicht gefunden: $Vorlage ";
$Inhalt = join "", <D_E>;

$Inhalt =~ s,Hier bitte Reformschrieb eintragen .*?Schluss\.,$q{Reformschrieb},s;
$Inhalt =~ s,Hierher kommt dann das Ergebnis\.,$_,;

print $Inhalt;

print "\n<br>
<br>- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - <br>
Die Werte:
<br>- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - <br>";
for (sort keys %q)  {
  print "\n<br>$_ <b>$q{$_}</b>"  }

