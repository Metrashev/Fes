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
$month = 0;
foreach ($data as $news){
	if($month!=$news['month']){
		$month=$news['month'];
		$tmp = date("n", $news['due_date']['row']);
		$curMonth = $GLOBALS['months'][LNG_CURRENT][$tmp];
		$tmp = date("Y", $news['due_date']['row']);
		echo <<<EOD
<div class="month">$curMonth $tmp
</div>
EOD;
	}
	
	
	if (!empty($news['body'])) {
		$readmore = <<<EOD
<div class="readMore" style="text-align:right; margin-bottom: 10px;"><a href="{$news['href']}" class="readMore">{$translation['read_more']}</a></div>
EOD;
	} else {
		$readmore = '';
	}
	
		 if(isset($news['picture']['pic'])) {
 	
		 $img = <<<EOD
		 <a href="{$news['href']}"><img src="{$news['picture']['pic']}" class="img" alt="{$news['title']}" style="float:left;margin:10px 10px 0 0;" /></a>
EOD;
		} else {
			$img = "";
		}

		
echo <<<EOD
<div style="border-bottom: 1px solid #69c;">
{$img}
<div style="font-weight:bold;padding-top:10px;">{$news['title']}</div>
{$news['subtitle']}

{$readmore}
</div>
EOD;
}

?>