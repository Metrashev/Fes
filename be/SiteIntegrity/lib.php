<?php

  require_once('../../lib/SysUtils.php');
  enc_require_once('../../config/config.php');
  enc_require_once('../../lib/db.php');
  enc_require_once('../../lib/ErrorHandling.php');
  $db = getdb();
  
/*  
  // Vsichki statichni stranici koito sa kam nesashtestvuvashta kategoria
  $res = $db->getAll("SELECT static_pages.* FROM static_pages LEFT JOIN categories ON static_pages.cid=categories.id WHERE categories.id IS NULL");
  $table = arrayToTable($res);
  echo <<<EOD

  <table>
  <caption>Vsichki statichni stranici koito sa kam nesashtestvuvashta kategoria</caption>
  $table
</table>  
EOD;
  

  $res = $db->getAll("SELECT news_pages.* FROM news_pages LEFT JOIN categories ON news_pages.cid=categories.id WHERE categories.id IS NULL");
  $table = arrayToTable($res);
  echo <<<EOD

  <table border=1>
  <caption>Vsichki news_pages  koito sa kam nesashtestvuvashta kategoria</caption>
  $table
</table>  
EOD;
*/

$fields = array('subtitle');
//$fields = array('title', 'subtitle', 'body');
//$searchWords = array('src="http://www.fes.bg', 'href="http://www.fes.bg');
$searchWords = array('href="http://www.fes.bg/library');

// REPLACE(subtitle,'href="http://www.fes.bg/library','href="http://www.fes.bg/library') 



UPDATE news_pages SET
subtitle = REPLACE(subtitle,'href="http://www.fes.bg/library/','href="/files/custom/library/'),
body = REPLACE(body,'href="http://www.fes.bg/library/','href="/files/custom/library/')


function buildWhere($fields, $searchWords){
	$where = array();
	
	foreach ($fields as $field){
		foreach ($searchWords as $word){
			$word = mysql_real_escape_string($word);
			$where[] = "($field LIKE '%$word%')";
		}
	}
	
	return $where = implode(' OR ', $where);
}

echo $where = buildWhere($fields, $searchWords);
$res = $db->getAll("SELECT * FROM news_pages WHERE $where");


$cnt = count($res);
 $table = arrayToTable($res);
echo <<<EOD

  <table border=1>
  <caption>($cnt) Vsichki static_pages sas href </caption>
  $table
</table>  
EOD;
?>