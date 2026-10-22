use LWP::Simple;



for ($kapitel=1; $kapitel < 28; $kapitel++)  {
# for ($kapitel=1; $kapitel < 3; $kapitel++)  {
  print "\n======= $kapitel";
  $ernte = get("http://gutenberg.spiegel.de/buch/das-gold-von-caxamalca-3047/" . $kapitel);
##	print $ernte;
	$ernte =~ m,(<div id="gutenb">.*?</div>),s;

	## print $1;
  $Ernte .= $1;

  }
 
open (D_A, ">Caxamalca.html") or die "Das geht nicht.";
print D_A $Ernte;


