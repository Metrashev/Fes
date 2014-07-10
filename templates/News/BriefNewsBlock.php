<?php
$translation = array(
	'bg'=>array(
		'read_more'=>'Прочети повече',
	),
	'en'=>array(
		'read_more'=>'Read More',
	),
	'de'=> array(
		'read_more'=>'Mehr',
	),	
);

$translation = $translation[LNG_CURRENT];

$cid = (int)$_GET['cid'];

$db = getdb();
$body = $db->getOne("SELECT body FROM news_pages WHERE cid={$cid} AND id={$news['id']} ");
if (empty($body))
{
	// скриваме read more линкът
	$translation['read_more'] = '';
}

 if($news['picture']['pic']) {
 	
 $img = <<<EOD
 <a href="{$news['href']}"><img src="{$news['picture']['pic']}" class="img" alt="{$news['title']}" /></a>
EOD;
} else {
	$img = "";
}
 echo <<<EOD
 <div class="news">
		<div class="subInfo">{$news['due_date']['date']}</div>				  
		 <h1><a href="{$news['href']}">{$news['title']}</a></h1>
		 	{$img}
		 <div style="padding:10px 0">{$news['subtitle']} </div>
		<div class="readMore" style="text-align:right; margin-bottom: 10px;"><a href="{$news['href']}" class="readMore">{$translation['read_more']}</a></div>
		<div class="clear"></div>
</div>
EOD;
?>