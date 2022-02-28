<?
foreach ($argv as $arg) {
	if(preg_match("/\=/", $arg)){
		$e=explode("=",$arg);
	${$e[0]} = $e[1];
	};
};

if(!isset($debug)) $debug = 0;
if($debug) echo "\nDEBUG MODE \n\n";

// filename :200_gravity.php
// created by momo, Feb. 10. 2012
include_once "lib.php";

//default values
if($debug) echo "bulkfile=$bulkfile\n";
if(!isset($bulkfile)) $bulkfile = "0_bulk.dat";
if($debug) echo "noSPC=$noSPC\n";
if(!isset($noSPC)) $noSPC = 0;
if($debug) echo "dofSPC=$dofSPC\n";
if(!isset($dofSPC)) $dofSPC = "123456";

if($debug) echo "LoadingDirection=$LoadingDirection\n";
if(!isset($LoadingDirection)) $LoadingDirection = "XYZ";
if($debug) echo "gravity=$gravity\n";
if(!isset($gravity)) $gravity = "10.0";  // g

if(isset($outputdir)){ $outputpath = $outputdir; }
else{$outputpath = "";	
}
if($debug) echo "outputpath=$outputpath\n";

// if TRUE, make run.sh
// default : make run.sh
$MakeRunSh = "TRUE";
if($debug) echo "MakeRunSh=$MakeRunSh\n";

if($debug) echo "NastranKeyword=$NastranKeyword\n";
if(!isset($NastranKeyword)) $NastranKeyword = "NO"; //YES or NO
$keyword_grav = NastranKeywordPrint("GRAV");

$of = "200_gravity.dat";
$filename = $outputpath . $of;
if($debug) echo "filename=$filename\n";

$fp = @fopen("$filename", "w");
if(!$fp){
	echo "file open error : $filename <br>";
}else{
	if($debug) echo "====make inputfile\n";
	$spcline = getSPC();
	$g = sprintf("%.2f", $gravity*9810.0); //gravity*unitconversion(1000)*g(9.81)
$line = "SOL 101
$$ SOL SESTAIC : Statics
CEND
$$ Bulk data Echo request -- No bulk data will be printed in f06 file
ECHO = NONE
$$ gravity : $gravity g
DISPLACEMENT(PLOT) = ALL
STRESS(PLOT) = ALL
";

$line .= getSPCHeading();

if(strtoupper($LoadingDirection) == "X" OR strtoupper($LoadingDirection) == "XYZ"){
$line .= "
SUBCASE 1
LABEL = Grav_x
LOAD = 2
$$$$";
};
if(strtoupper($LoadingDirection) == "Y" OR strtoupper($LoadingDirection) == "XYZ"){
$line .= "
SUBCASE 2
LABEL = Grav_y
LOAD = 3
$$$$";
};
if(strtoupper($LoadingDirection) == "Z" OR strtoupper($LoadingDirection) == "XYZ"){
$line .= "
SUBCASE 3
LABEL = Grav_z
LOAD = 4
$$$$";
};

$line .= "include '$bulkfile'
$spcline
$$ $keyword_grav
$$ Load : $gravity g * 9.81 m/s2 * 1000 mm/m = $g mm/s2
";

if(strtoupper($LoadingDirection) == "X" OR strtoupper($LoadingDirection) == "XYZ"){
$line .= "GRAV,2,,$g,1.0,0.0,0.0\n";
};
if(strtoupper($LoadingDirection) == "Y" OR strtoupper($LoadingDirection) == "XYZ"){
$line .= "GRAV,3,,$g,0.0,1.0,0.0\n";
};
if(strtoupper($LoadingDirection) == "Z" OR strtoupper($LoadingDirection) == "XYZ"){
$line .= "GRAV,4,,$g,0.0,0.0,1.0\n";
};

$line .= "$$
ENDDATA";
	if($MakeRunSh) MakeRunCode("nast_sub i=$of\n");
	fputs($fp, $line);
	if($fp) fclose($fp);
	if($debug) echo "====end of make inputfile\n";
}; // end of if($fp)

exit;
?>