<?
foreach ($argv as $arg) {
	if(preg_match("/\=/", $arg)){
		$e=explode("=",$arg);
	${$e[0]} = $e[1];
	};
};

if(!isset($debug)) $debug = 0;
if($debug) echo "\nDEBUG MODE \n\n";

// filename :310_static_unitLoad.php
// created by momo, Feb. 2. 2013
include_once "lib.php";

//default values
if($debug) echo "bulkfile=$bulkfile\n";
if(!isset($bulkfile)) $bulkfile = "0_bulk.dat";
if($debug) echo "noSPC=$noSPC\n";
// if(!isset($noSPC)) $noSPC = 0;
if(!isset($noSPC)) $noSPC = "NONE";
if($debug) echo "dofSPC=$dofSPC\n";
if(!isset($dofSPC)) $dofSPC = "123456";


if($debug) echo "StartNodeNo=$StartNodeNo\n";
if(!isset($StartNodeNo)) $StartNodeNo = "11";
if($debug) echo "NoNode=$NoNode\n";
if(!isset($NoNode)) $NoNode = "1";
if($debug) echo "Force=$Force\n";
if(!isset($Force)) $Force = "1.0";

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

$of = "310_staticUnitLoad.dat";
$filename = $outputpath . $of;
if($debug) echo "filename=$filename\n";

$fp = @fopen("$filename", "w");
if(!$fp){
	echo "file open error : $filename <br>";
}else{
	if($debug) echo "====make inputfile\n";
	$spcline = getSPC();
	$Force = sprintf("%.2f", $Force);
$line = "SOL 101
$$ SOL SESTAIC : Statics
CEND
$$ Bulk data Echo request -- No bulk data will be printed in f06 file
ECHO = NONE
$$ static load : $Force N
DISPLACEMENT(PLOT) = ALL
STRESS(PLOT) = ALL
";

if(!($noSPC == 0 or $noSPC == "NONE")) $line .= "SPC = 1\n";

for($i=$StartNodeNo; $i<=$StartNodeNo+$NoNode-1; $i++){
$line .= "
$$$$$$$ node : $i
SUBCASE " . $i . "1
LABEL = " . $i . "_x
LOAD = " . $i . "1
$$$$
SUBCASE " . $i . "2
LABEL = " . $i . "_y
LOAD - " . $i . "2
$$$$
SUBCASE " . $i . "3
LABEL = " . $i . "_z
LOAD - " . $i . "3
$$$$";
};
}; // end of for i

$line .= "
include '$bulkfile'
$spcline
$$ Load";

for($i=$StartNodeNo; $i<=$StartNodeNo+$NoNode-1; $i++){
$line .= "
FORCE," . $i. "1,". $i. ",0,$Force,1.0,0.0,0.0
FORCE," . $i. "2,". $i. ",0,$Force,0.0,1.0,0.0
FORCE," . $i. "3,". $i. ",0,$Force,0.0,0.0,1.0";
}; // end of for i

$line .= "
$$
ENDDATA";
	if($MakeRunSh) MakeRunCode("nast_sub i=$of\n");
	fputs($fp, $line);
	if($fp) fclose($fp);
	if($debug) echo "====end of make inputfile\n";
}; // end of if($fp)

exit;
?>