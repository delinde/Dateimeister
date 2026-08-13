function sz(was) { return was.replace(/ss/g, 'ß'); } 
function uc(was) { return was.toUpperCase(); }  
function lc(was) { return was.toLowerCase(); }  

function filtereRechtschreibung(was)  {
  was = was.replace(/\bAss\b/g, 'As');
  was = was.replace(/\bAlbtraum\b/g, 'Alptraum');
  was = was.replace(/sodass\b/g, 'so dass');
  was = was.replace(/(S|s)tattdessen/g, '$1tatt dessen');
  was = was.replace(/(A|a)ufw.ndig/g, '$1ufwendig');
  was = was.replace(/elbstständig/g, 'elbständig');
  was = was.replace(/([Nn])ummerier/g, '$1umerier');
  was = was.replace(/([Pp])latzier/g, '$1lazier');
  was = was.replace(/([Pp])otenziell/g, '$1otentiell');
  was = was.replace(/([Pp])otenzial/g, '$1otential');
  was = was.replace(/(E|e)in jedes Mal/g, '$1in jedes# Mal');   //  schützen
  was = was.replace(/(B|b)isschen/g, '$1ißchen');
  was = was.replace(/(J|j)edes Mal/g, '$1edesmal');
  was = was.replace(/(V|v)oran treiben/g, '$1orantreiben');     
  was = was.replace(/ssch/g, 's#sch');                 //  Seltenes schützen
  was = was.replace(/Ausstieg/g, 'Aus#stieg');         //  Seltenes schützen
  was = was.replace(/Bussteig/g, 'Bus#steig');         //  Seltenes schützen
  was = was.replace(/undesstaat/g, 'undes#staat');     //  Seltenes schützen
  was = was.replace(/(au)ss([aeä])/gi, '$1s#s$2');     //  Seltenes schützen
  was = was.replace(/sszene/gi, 's#szene');            //  Seltenes schützen
  was = was.replace(/sstunde/gi, 's#stunde');          //  Seltenes schützen
  was = was.replace(/(s|S)trasse/g, '$1traße');
  was = was.replace(/(h|H)asserf/g, '$1aßerf');
  nix = was.replace(/(Fassentnahme)/gm,   sz(RegExp.$1));
  was = was.replace(/(Fassentnahme)/gm,   sz(RegExp.$1));
  NIX = was.replace(/(Messergebnis)/gm,   sz(RegExp.$1));
  was = was.replace(/(Messergebnis)/gm,   sz(RegExp.$1));
  NIX = was.replace(/(Passersatz)/gm,     sz(RegExp.$1));
  was = was.replace(/(Passersatz)/gm,     sz(RegExp.$1));
  NIX = was.replace(/(schlussendlich)/gm, sz(RegExp.$1));
  was = was.replace(/(schlussendlich)/gm, sz(RegExp.$1));
  was = was.replace(/reformierter/g, 'herkömmlicher = moderner');   //  Diese Zeile nur für diese Zeige-Fassung!
  was = was.replace(/ss\b/g, 'ß');                      //  ss am Wortende  ->  ß
  was = was.replace(/sss/g, 'ßs');                      //  sss -> ßs
  was = was.replace(/ussa/ig, 'ußa');                    //  essta  -> eßta
  was = was.replace(/([mM])issacht/g, '$1ißacht');                      //  ss am Wortende  ->  ß
  NIX = was.replace(/((?:[^GB]|\A)[bcdfghjklmnpqrstvwxyz][aeiouäöü])ss([bcdfghjklmnpqrstvwxyz])/ig, '$1ß$2');  // ss >  ß ;  Glasstück, Grasstück
  was = was.replace(/((?:[^GB]|\A)[bcdfghjklmnpqrstvwxyz][aeiouäöü])ss([bcdfghjklmnpqrstvwxyz])/ig, '$1ß$2');  // ss >  ß ;  Glasstück, Grasstück
  NIX = was.replace(/([eio])\1\1/g, '$1$1-' + uc(RegExp.$1));            //  Teeei ->Tee-Ei
  was = was.replace(/([eio])\1\1/g, '$1$1-' + uc(RegExp.$1));            //  Teeei ->Tee-Ei
  was = was.replace(/(.)\1\1([aeiouyäöü])/g, '$1$1$2'); //  mmme -> mme
  NIX = was.replace(/(\d+)-(jähr|mal|fach|er)/g, lc(RegExp.$1+RegExp.$2));
  was = was.replace(/(\d+)-(jähr|mal|fach|er)/g, lc(RegExp.$1+RegExp.$2));
  was = was.replace(/so genannt/g, 'sogenannt')
  was = was.replace(/(S|s)elbstständig/, '$1elbständig');
  was = was.replace(/rgendetwas/g, 'rgend etwas');
  was = was.replace(/rgendeiner/g, 'rgend einer');
  was = was.replace(/(z)urzeit/g, '$1ur Zeit');  //zurzeit, Zurzeit
  was = was.replace(/(G|g)räulich/g, '$1reulich');
  was = was.replace(/Tipp\b/g, 'Tip');
  NIX = was.replace(/(lles beim Alten)/g,            lc(RegExp.$1));
  was = was.replace(/(lles beim Alten)/g,            lc(RegExp.$1));
  NIX = was.replace(/([Ii])(m Allgemeinen)/g,  '$1' +lc(RegExp.$2));
  was = was.replace(/([Ii])(m Allgemeinen)/g,  '$1' +lc(RegExp.$2));
  NIX = was.replace(/([Ii])(m Wesentlichen)/g, '$1' +lc(RegExp.$2));
  was = was.replace(/([Ii])(m Wesentlichen)/g, '$1' +lc(RegExp.$2));
  was = was.replace(/#/g, '');    //  # ist zeitweiliger ss-Schützer
  return (was);
  }

function erneuereRechtschreibung()  {
  nix = filtereRechtschreibung(document.getElementById('Reformschrieb').value);  //    zum Aufwärmen, damit RegExp sich selbst kennt, seufz ....
  document.getElementById('Rechtschrieb').value = filtereRechtschreibung(document.getElementById('Reformschrieb').value);
  }
