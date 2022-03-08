<?
foreach ($argv as $arg) {
	if(preg_match("/\=/", $arg)){
		$e=explode("=",$arg);
	${$e[0]} = $e[1];
	};
};

if(!isset($debug)) $debug = 0;
if($debug) echo "\nDEBUG MODE \n\n";

// filename :600_transient.php
// created by momo, Apr. 9. 2012
include_once "lib.php";

//default values
if($debug) echo "bulkfile=$bulkfile\n";
if(!isset($bulkfile)) $bulkfile = "0_bulk.dat";
if($debug) echo "noSPC=$noSPC\n";
// if(!isset($noSPC)) $noSPC = 0;
if(!isset($noSPC)) $noSPC = "NONE";
if($debug) echo "dofSPC=$dofSPC\n";
if(!isset($dofSPC)) $dofSPC = "123456";

if($debug) echo "nSet=$nSet\n";
if(!isset($nSet)) $nSet = "";

if($debug) echo "InitFreq=$InitFreq\n";
if(!isset($InitFreq)) $InitFreq = "0.1";

if($debug) echo "NoOfMode=$NoOfMode\n";
if(!isset($NoOfMode)) $NoOfMode = "10";


if($debug) echo "NoTimeStep=$NoTimeStep\n";
if(!isset($NoTimeStep)) $NoTimeStep = "10";
if($debug) echo "TimeIncr=$TimeIncr\n";
if(!isset($TimeIncr)) $TimeIncr = "0.001";

if($debug) echo "CritDamping=$CritDamping\n";
if(!isset($CritDamping)) $CritDamping = "0.02";

if($debug) echo "TimeHistoryFile=$TimeHistoryFile\n";
if(!isset($TimeHistoryFile)) $TimeHistoryFile = "600_Trans_disp_table.dat";

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
if($NastranKeyword = "YES") $keyword_resvec = NastranKeywordPrint("RESVEC")
if($NastranKeyword = "YES") $keyword_tstep = NastranKeywordPrint("TSTEP")
if($NastranKeyword = "YES") $keyword_dload = NastranKeywordPrint("DLOAD")
if($NastranKeyword = "YES") $keyword_tload1 = NastranKeywordPrint("TLOAD1")
if($NastranKeyword = "YES") $keyword_tabled1 = NastranKeywordPrint("TABLED1")
if($NastranKeyword = "YES") $keyword_tstep = NastranKeywordPrint("TSTEP")

$of = "600_transient.dat";
$filename = $outputpath . $of;
if($debug) echo "filename=$filename\n";

$fp = @fopen("$filename", "w");
if(!$fp){
	echo "file open error : $filename <br>";
}else{
	if($debug) echo "====make inputfile\n";
	$spcline = getSPC();
	$spcdline = getSPCD();
	
	$InitFreq = sprint("%.1f", $InitFreq);
	$TimeIncr = sprint("%.4f", $TimeIncr);
	$critDamping = sprint("%.4f", $critDamping);
	
$line = "SOL 112
$$ SOL SEMTRANS : Modal Transient Response
CEND
$$ Bulk data Echo request -- No bulk data will be printed in f06 file
ECHO = NONE
$$
SET 1=$nSet
$$ $keyword_resvec
RESVEC = YES
$$
";
if(!($noSPC == 0 or $noSPC == "NONE")) $line .= "SPC = 1\n";

$line .= "METHOD(STRUCTURE) = 2
TSTEP = 3
SDAMPING(STRUCTURE) = 4
$$ DISPLACEMENT(SORT2, PUNCH, PHASE) = 1
DISPLACEMENT(SORT1, PUNCH) = 1
$$
SUBCASE 1
LABEL = Transient_x
DLOAD = 11
$$
SUBCASE 2
LABEL = Transient_y
DLOAD = 12
$$
SUBCASE 3
LABEL = Transient_z
DLOAD = 13
$$
include '$bulkfile'
$spcline
$$ Method
EIGRL,2,$InitFreq,,$NoOfMode,,,,MASS
$$ $keyword_tstep
TSTEP,3,$NoTimeStep, $TimeIncr
$$
$$ SDamping
$$ $critDamping critical damping
TABDMP1,4,CRIT
+,0.0,$critDamping,1.0,$critDamping,ENDT
$$
$$
$spcdline
$$ $keyword_dload
$$ for x-direction
DLOAD,11,1.0, 1.0, 21
$$ for y-direction
DLOAD,12,1.0, 1.0, 22
$$ for z-direction
DLOAD,13,1.0, 1.0, 23

$$ $keyword_tload1
$$ for x-direction
TLOAD1,21,6,0,DISP, 31
$$ for y-direction
TLOAD1,22,7,0,DISP, 32
$$ for z-direction
TLOAD1,23,8,0,DISP, 33
$$ $keyword_tabled1
$$ include Timehistory
include '$TimeHistoryFile'
$$
ENDDATA";

	if($MakeRunSh) MakeRunCode("nast_sub i=$of\n");
	fputs($fp, $line);
	if($fp) fclose($fp);
	if($debug) echo "====end of make inputfile\n";
}; // end of if($fp)

exit;
?>