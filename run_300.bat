@ECHO OFF
SET PHP="C:\php7\php.exe"
SET PROGRAM="D:\work\makeNastranInput\300_static.php"

REM SET bulkfile="0_bulk.dat"
REM SET noSPC=0
REM SET dofSPC="123456"
REM SET InitFreq="0.1"
REM SET MaxFreq="200.0"
REM SET NoOfMod="3"
REM SET ffCondition="NO"

REM SET outputdir="./output/"

REM %PHP% %PROGRAM% bulkfile=%bulkfile% noSPC=%noSPC% dofSPC=%dofSPC% InitFreq=%InitFreq% NoOfMod=%NoOfMod% ffCondition=%ffCondition% outputdir=%outputdir% 

%PHP% %PROGRAM%