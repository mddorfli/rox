<?php
include "lib/dbaccess.php" ;
include "layout/header.php" ;


$lang=$_SESSION['lang'] ; // save session language
$_SESSION['lang']="eng" ;$_SESSION['IdLanguage']=0 ; // force english for menu
echo "<H2>Schema</H2>" ;




$s1="select TABLE_NAME,TABLE_COMMENT from information_schema.TABLES where TABLE_SCHEMA='hcvoltest' order by TABLE_NAME" ;

$qry1=sql_query($s1) ;
while ($r1=mysql_fetch_object($qry1)) {
  echo "<b>",$r1->TABLE_NAME,"</b> <i>",$r1->TABLE_COMMENT,"</i><br>" ;
  $s2="select COLUMN_NAME,COLUMN_COMMENT,DATA_TYPE from information_schema.COLUMNS where COLUMNS.TABLE_NAME='".$r1->TABLE_NAME."' and TABLE_SCHEMA='hcvoltest'" ;
  $qry2=sql_query($s2) ;
  while ($r2=mysql_fetch_object($qry2)) {
    echo "&nbsp;&nbsp;&nbsp;<b>",$r2->COLUMN_NAME,"</b> ",$r2->DATA_TYPE," <i>",$r2->COLUMN_COMMENT,"</i><br>" ;
  }
	echo "<br>" ;
}  


include "layout/footer.php" ;
?>
