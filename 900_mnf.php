<?
foreach ($argv as $arg) {
	if(preg_match("/\=/", $arg)){
		$e=explode("=",$arg);
	${$e[0]} = $e[1];
	};
};

if(!isset($debug)) $debug = 0;
if($debug) echo "\nDEBUG MODE \n\n";

// filename :900_mnf.php
// created by momo, Mar. 26. 2012
// mnf : modal neutral file
include_once "lib.php";

//default values
if($debug) echo "bulkfile=$bulkfile\n";
if(!isset($bulkfile)) $bulkfile = "0_bulk.dat";
if($debug) echo "noMode=$noMode\n";
if(!isset($noMode)) $noMode = "10";
if($debug) echo "extSet=$extSet\n";
if(!isset($extSet)) $extSet = "11,12,13";
// $extSet = "11,THRU,13";

$spts_base = 99900000;

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

if($NastranKeyword == "YES") $keyword_dti = NastranKeywordPrint("DTI");
if($NastranKeyword == "YES") $keyword_spint = NastranKeywordPrint("SPOINT");
if($NastranKeyword == "YES") $keyword_qset1 = NastranKeywordPrint("QSET1");
if($NastranKeyword == "YES") $keyword_aset1 = NastranKeywordPrint("ASET1");
if($NastranKeyword == "YES") $keyword_tabdmp1 = NastranKeywordPrint("TABDMP1");
if($NastranKeyword == "YES") $keyword_eigrl = NastranKeywordPrint("EIGRL");

//output file open
$of = "900_mnf.dat";
$filename = $outputpath . $of;
if($debug) echo "filename=$filename\n";

$fp = @fopen("$filename", "w");
if(!$fp){
	echo "file open error : $filename <br>";
}else{
	if($debug) echo "====make inputfile\n";
	$spts_start = $spts_base +1;
	$spts_end = $spts_base +$noMode;

$line = "SOL 103
$$ SOL SEMODES : Normal Modes
CEND
$$ Bulk data Echo request -- No bulk data will be printed in f06 file
ECHO = NONE
$$
TITLE = ADAMS_MNF
$$ required for interface run
ADAMSMNF FLEXBODY=YES
$$
SUBCASE 1
LABEL = ADAMS_NMF
METHOD(STRUCTURE) =2
DISPLACEMENT(PLOT) = ALL
$$---------------------------------------------------------------------
$$ Parameter cards below should be included in bulk file.
$$ PARAM,POST,-1                      $ for making OP2
$$ PARAM,GRDPNT,0
$$---------------------------------------------------------------------
include '$bulkfile'
$$---------------------------------------------------------------------
$$ $keyword_dti
DTI, UNITS, 1, MGG, NEWTON, MM, SECOND
$$ $keyword_spoint
SPOINT,$spts_start,THRU,$spts_end
$$ $keyword_qset1
QSET1,0,$spts_start,THRU,$spts_end
$$ $keyword_aset1
ASET1,123456,$extSet
$$ $keyword_eigrl
EIGRL,2,,,$noMode
$$
ENDDATA";
	if($MakeRunSh) MakeRunCode("nast_sub i=$of\n");
	fputs($fp, $line);
	if($fp) fclose($fp);
	if($debug) echo "====end of make inputfile\n";
}; // end of if($fp)

exit;
?>