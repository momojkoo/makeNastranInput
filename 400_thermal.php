<?
foreach ($argv as $arg) {
	if(preg_match("/\=/", $arg)){
		$e=explode("=",$arg);
	${$e[0]} = $e[1];
	};
};

if(!isset($debug)) $debug = 0;
if($debug) echo "\nDEBUG MODE \n\n";

// filename :400_thermal.php
// created by momo, Feb. 10. 2012
include_once "lib.php";

//default values
if($debug) echo "bulkfile=$bulkfile\n";
if(!isset($bulkfile)) $bulkfile = "0_bulk.dat";
if($debug) echo "noSPC=$noSPC\n";
// if(!isset($noSPC)) $noSPC = 0;
if(!isset($noSPC)) $noSPC = "NONE";
if($debug) echo "dofSPC=$dofSPC\n";
if(!isset($dofSPC)) $dofSPC = "123456";

if($debug) echo "InitialTemp=$InitialTemp\n";
if(!isset($InitialTemp)) $InitialTemp = "25.0";
if($debug) echo "FinalTemp1=$FinalTemp1\n";
// if(!isset($FinalTemp1)) $FinalTemp1 = "90.0";
if($debug) echo "StartNodeNo=$StartNodeNo\n";
// if(!isset($StartNodeNo)) $StartNodeNo = "-40.0";

if($FinalTemp1 == -1000 and $FinalTemp2 == -1000) exit;

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

$of = "400_thermal.dat";
$filename = $outputpath . $of;
if($debug) echo "filename=$filename\n";

$fp = @fopen("$filename", "w");
if(!$fp){
	echo "file open error : $filename <br>";
}else{
	if($debug) echo "====make inputfile\n";
	$spcline = getSPC();
	$InitialTemp = sprintf("%.1f", $InitialTemp);
$line = "SOL 101
$$ SOL SESTAIC : Statics
CEND
$$ Bulk data Echo request -- No bulk data will be printed in f06 file
ECHO = NONE
$$
TEMP(INIT) = 2
$$
DISPLACEMENT(PLOT) = ALL
STRESS(PLOT) = ALL
";

if(!($noSPC == 0 or $noSPC == "NONE")) $line .= "SPC = 1\n";

if($FinalTemp1 != -1000) {
	$FinalTemp1 = sprint("%.1f", $FinalTemp1);
	$line .= "SUBCASE 1
LABEL = Temp($InitialTemp, $FinalTemp1)
TEMP(LOAD) = 3
";
}
if($FinalTemp2 != -1000) {
	$FinalTemp2 = sprint("%.1f", $FinalTemp2);
	$line .= "SUBCASE 2
LABEL = Temp($InitialTemp, $FinalTemp2)
TEMP(LOAD) = 4
";
}

$line .= "
include '$bulkfile'
$spcline
$$ Temperature
TEMPD,2,$InitialTemp
";

if($FinalTemp1 != -1000) {
	$line .= "TEMPD,3,$FinalTemp1\n";
};
if($FinalTemp2 != -1000) {
	$line .= "TEMPD,4,$FinalTemp2\n";
};

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