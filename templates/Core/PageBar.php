<?php

$translation = array(
	'bg'=>array(
		'page'=>'стр.',
		'from'=>' от ',
	),
	'en'=>array(
		'page'=>'str. ',
		'from'=>' from ',
	),
	'de'=>array(
		'page'=>'page ',
		'from'=>' from ',		
	),
);
$translation = $translation[LNG_CURRENT];

if($data['PageBar']['total']<2) return ;

echo <<<EOD
<div class="PageBar">
<div style="float:right">{$translation['page']} {$data['PageBar']['current']} {$translation['from']} {$data['PageBar']['total']} &nbsp;</div>
EOD;

$res = Array();

foreach($data['PageBar']['pages'] as $pg=>$href){
	if($href){
		$res[] = <<<EOD
<a href="{$href}">{$pg}</a>
EOD;
	}	else {
		$res[] = <<<EOD
<b>{$pg}</b>
EOD;
	}
}

echo implode("&nbsp;", $res);

?>
</div>