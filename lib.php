<?
// filename : lib.php
// created by momo, Feb. 10. 2012

$outputpath = "./";

function getSPC(){
	global $noSPC;
	global $dofSPC;
	$line = "$$ SPC\n";
	if($noSPC == 0 or $noSPC == "NONE"){
		$line .= "$$ There are no SPCs (free-free condition)\n";
	}elseif($noSPC == -1){
		$line .= "$$ SPCs are included in bulk data file\n";
	}elseif($noSPC <= -2){
		$line .= "$$ ERROR when making SPC constraints (No of SPC should be greater than -2)\n";
	}else{
		for ($i=1; $i<=$noSPC; $i++){
			$line .= "SPC,1,$i,$dofSPC,0.0\n";
		}; // end of for
	};
	$line .= "$$";
	return $line;
}

function getSPCHeading(){
	global $noSPC;
	$line = "$$ SPC\n";
	if($noSPC == 0 or $noSPC == "NONE"){
		$line .= "$$ There are no SPCs (free-free condition)\n";
	}elseif($noSPC == -1){
		$line .= "$$ SPCs are included in bulk data file\n";
	}elseif($noSPC <= -2){
		$line .= "$$ ERROR when making SPC constraints (No of SPC should be greater than -2)\n";
	}else{
		$line .= "SPC=1\n";
	};
	$line .= "$$\n";
	return $line;
}

function getSPCD($v=1.0){
	global $noSPC;
	$line = "$$ Dload - direction $ value (value : $v)\n";
	if($noSPC == "NONE"){
		$line .= "$$ There are no SPCs - Check your input\n";
	}elseif($noSPC == "InBulk"){
		$line .= "$$ SPCs are included in bulk data file - Check your input\n";
	}elseif($noSPC <= 0){
		$line .= "$$ ERROR when making SPC constraints - Check your input\n";
	}else{
		for ($i=1; $i<=3; $i++){
			for ($j=1; $j<=$noSPC; $j++){
				$lc = $i+5;
				$line .= "SPCD,$lc,$j,$i,$v\n";
			}; // end of for j
		}; // end of for i
	};
	return $line;
}

function MakeRunCode($cmd){
	global $outputpath;

	$filename = $outputpath . "run.sh";
	if($cmd == ""){
		$fp = @fopen("$filename", "w");
	}else{
		$fp = @fopen("$filename", "a");
	};
	if(!$fp){ echo "file open error : $filename <br>";
	}else{
		fputs($fp,$cmd);
		if($fp) fclose($fp);
	};
}

function NastranKeywordPrint($keyword){
	switch(strtoupper($keyword)){
		case "EIGRL":
$line="
$$ EIGRL : ";
			break;
		case "FREQ1":
$line="
$$ FREQ1 : ";
			break;
		case "RLOAD1":
$line="
$$ RLOAD1 : ";
			break;
		case "TABDMP1":
$line="
$$ TABDMP1 : ";
			break;
		case "DTI":
$line="
$$ DTI : ";
			break;
		case "SPOINT":
$line="
$$ SPOINT : ";
			break;
		case "QSET1":
$line="
$$ QSET1 : ";
			break;
		case "ASET1":
$line="
$$ ASET1 : ";
			break;
		case "TABDMP1":
$line="
$$ TABDMP1 : ";
			break;
		case "RESVEC":
$line="
$$ RESVEC : ";
			break;
		case "TSTEP":
$line="
$$ TSTEP : ";
			break;
		case "DLOAD":
$line="
$$ DLOAD : ";
			break;
		case "TLOAD1":
$line="
$$ TLOAD1 : ";
			break;
		case "TABLED1":
$line="
$$ TABLED1 : ";
			break;
		case "GRAV":
$line="
$$ GRAV : ";
			break;
		case "INREL":
$line="
$$ INREL : ";
			break;
	};
	return $line;
}

function print_r1($r){
	echo "<pre>";
	print_r($r);
	echo "</pre>";
}

?>