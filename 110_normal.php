<?
foreach ($argv as $arg) {
	if(preg_match("/\=/", $arg)){
		$e=explode("=",$arg);
	${$e[0]} = $e[1];
	};
};

if(!isset($debug)) $debug = 0;
if($debug) echo "\nDEBUG MODE \n\n";

// filename :110_normal.php
// created by momo, Feb. 10. 2012
include_once "lib.php";

//default values
if($debug) echo "bulkfile=$bulkfile\n";
if(!isset($bulkfile)) $bulkfile = "0_bulk.dat";
if($debug) echo "noSPC=$noSPC\n";
if(!isset($noSPC)) $noSPC = 0;
if($debug) echo "dofSPC=$dofSPC\n";
if(!isset($dofSPC)) $dofSPC = "123456";

if($debug) echo "InitFreq=$InitFreq\n";
if(!isset($InitFreq)) $InitFreq = "0.1";

if($debug) echo "NoOfMode=$NoOfMode\n";
if(!isset($NoOfMode)) $NoOfMode = "3";

if($debug) echo "MaxFreq=$MaxFreq\n";
if(!isset($MaxFreq)) $MaxFreq = "200.0";

if($debug) echo "ffCondition=$ffCondition\n";
if(!isset($ffCondition)) $ffCondition = "NO";

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

$keyword_eigrl = "";
if($NastranKeyword == "YES") $keyword_eigrl = NastranKeywordPrint("eigrl");

//output file open
if($ffCondition == "YES"){
	$of = "110_normal_ff.dat";
	$noSPC = 0;
}else{
	$of = "110_normal.dat";
};
$filename = $outputpath . $of;
if($debug) echo "filename=$filename\n";

$fp = @fopen("$filename", "w");
if(!$fp){
	echo "file open error : $filename <br>";
}else{
	if($debug) echo "====make inputfile\n";
	$spcline = getSPC();
	$InitFreq = sprintf("%.1f", $InitFreq);
	// $MaxFreq = sprintf("%.1f", $MaxFreq);
$line = "SOL 103
$$ SOL SEMODES : Normal Modes
CEND
$$ Bulk data Echo request -- No bulk data will be printed in f06 file
ECHO = NONE
$$
SUBCASE 1
LABEL = NormalMode
";
if(!($noSPC == 0 or $noSPC == "NONE")) $line .= "SPC = 1\n";
$line .= "METHOD(STRUCTURE) = 2
DISPLACEMENT = ALL
ESE = ALL
$$
include '$bulkfile'
$spcline
$$ $keyword_eigrl
EIGRL,2,$InitFreq,,$NoOfMode,,,,MASS
$$
ENDDATA";
	if($MakeRunSh) MakeRunCode("nast_sub i=$of\n");
	fputs($fp, $line);
	if($fp) fclose($fp);
	if($debug) echo "====end of make inputfile\n";
}; // end of if($fp)

exit;
?>