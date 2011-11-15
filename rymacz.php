#!/usr/bin/php
<?php
// Rymacz - wersja buforująca dane źródłowe (szybsza)
// + poprawka unicode
//
// Rymacz oczekuje istnienia w bieżącym katalogu podkatalogu 'txt-liryka'
// z takimi sobie wierszami w formacie txt, utf-8.
// Na ich podstawie tworzy wiersze dobre, składne i udane. 
// Na każdą okazję.
//
// michal.szota@gmail.com
// 15.10.2011

/*
    Copyright (C) 2011 Michał Szota

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as
    published by the Free Software Foundation, either version 3 of the
    License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

// WSTAJEMY

mb_internal_encoding("UTF-8");
mb_regex_encoding("UTF-8");
$poezja=array();
$wielkapoezja=array();

// SZUKAMY INSPIRACJI

@$korpus=unserialize(file_get_contents('wersy.db'));

if (!$korpus) {
	$pliki=getFilesFromDir('txt-liryka');
	foreach ($pliki as $p) {
		if (filesize($p)<8193) { // bierzemy tylko krótkie teksty
			$k=preg_replace('/---.*/s','',file_get_contents($p));
			$korpus.=samelitery($k)."\n";
			$k=preg_replace('/[ \t]{2,100}/','',$k);
			$k=preg_replace('/\n\n/',"\n",$k);
			$k = implode("\n", array_slice(explode("\n", $k), 4));
		}
	}

	$korpus=array_unique(explode("\n",$korpus));
	@file_put_contents('wersy.db',serialize($korpus));
}


// NAMYŚLAMY SIĘ, MARSZCZYMY CZOŁO

$dlugosc=mt_rand(3,5);
for ($j=0;$j<=$dlugosc;$j++) {
	$seed=$korpus[array_rand($korpus)];
		
	$wersow=mt_rand(2,4);
	for ($i=0;$i<=$wersow;$i++) {
		$wers=array();
		$linia=explode(' ',rymuj($seed,mt_rand(3,4)));
		$poz=mt_rand(0,count($linia)-1);
		while ($poz<=count($linia)-1) {
			array_push($wers,$linia[$poz]);
			$poz++;
		}
		
		array_push($poezja,implode(" ",$wers));
	}
}

foreach ($poezja as $wers) {
	if (trim($wers)) array_push($wielkapoezja,$wers);
}

// DEKLAMUJEMY Z DUMNYM SPOJRZENIEM SKIEROWANYM W DAL

//@system("clear"); 
$wers=$wielkapoezja[0];
$wers=",,".ladnie($wers)."''";
echo $wers;
echo "\n-----------------------------------------------------------\n\n"; 

foreach ($wielkapoezja as $k=>$wers) {
	if (!$k) continue;
	
	echo trim($wers) ? ladnie($wers)."\n" : '';
	if (!($k % 5)) echo "\n";	
}

echo "\n";
echo "Koniec.\n";
echo "\n";

// KLEPIEMY SIĘ PO KIESZENIACH MARYNARKI, CHRZĄKAMY, UŚMIECHAMY

function ladnie($txt) {
	$bizuteria=array('...',',','.','!','?');
	if (mt_rand(1,6)==1) $txt.=$bizuteria[array_rand($bizuteria)];
	
	$txt=preg_replace('/  +/',' ',$txt);
	return trim(ucfirst($txt));
}


function rymuj($co,$dokladnosc=3) {
	$rym=substr($co,-$dokladnosc);
	
	global $korpus;
	$rymy=array();
	
	foreach ($korpus as $k) {
		if (substr($k,-$dokladnosc)==$rym) array_push($rymy,$k);
	}
	
	if ($rymy) return $rymy[array_rand($rymy)];
}


function samelitery($txt) {
	return trim(preg_replace("/[^\-a-zéłąćżńóśćęźŁĄĆŻŃÓŚĆĘŹ\ \n']/i",'',$txt));
}

function nl() {
global $poezja;
array_push($poezja,"\n");
}

function getFilesFromDir($dir) { 

  $files = array(); 
  if ($handle = opendir($dir)) { 
    while (false !== ($file = readdir($handle))) { 
        if ($file != "." && $file != "..") { 
            if(is_dir($dir.'/'.$file)) { 
                $dir2 = $dir.'/'.$file; 
                $files[] = getFilesFromDir($dir2); 
            } 
            else { 
              $files[] = $dir.'/'.$file; 
            } 
        } 
    } 
    closedir($handle); 
  } 

  return array_flat($files); 
} 

function array_flat($array) { 

  foreach($array as $a) { 
    if(is_array($a)) { 
      $tmp = array_merge($tmp, array_flat($a)); 
    } 
    else { 
      $tmp[] = $a; 
    } 
  } 

  return $tmp; 
} 

?>