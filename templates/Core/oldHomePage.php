<?php

$translation = array(
	'bg'=>array(
		'read_more'=>'Прочети повече',
		'publications'=> 'Последни публикации'
	),
	'en'=>array(
		'read_more'=>'Read More',
		'publications'=>'Latest Publications'
	),
	'de'=>array(
		'read_more'=>'Mehr',
		'publications'=>'Letzte Publikationen'
	),
);
$translation = $translation[LNG_CURRENT];

$customCids = $GLOBALS['CONFIG']['customCids'][LNG_CURRENT];
?>
<div class="article" id="customarticle1">
<table width="100%" cellpadding="0" cellspacing="0" style="padding: 0 10px;">
<tr>
<td style="width: 150px;">
<img src="/files/custom/Home/FriedrichEbert.png" alt="Friedrich Ebert" /> 
<b>"No freedom
without democracy."</b>
<h2>Friedrich Ebert</h2><br />
</td>
<td style="padding: 0 10px;">
<?php
	$curr_cid = (int)$_GET['cid'] ? (int)$_GET['cid'] : $GLOBALS['CONFIG']['DefautlCID'];
	$head_content = getdb()->getRow("SELECT title,body FROM static_pages WHERE cid = ".(int)$curr_cid);
	echo "<pre>";
	//var_dump($head_content);
	echo "</pre>";
	echo "<h1>".$head_content['title']."</h1>";
	echo $head_content['body'];
?>
<!--
<h1>
	Welcome to the Sofia Office<br />
	of the Friedrich-Ebert-Stiftung!
</h1>
<p>Here you will find broad information about the Friedrich-
Ebert-Stiftung in general, and about its office in Sofia in 
particular. </p>
<p>We keep you up to date about our events and publications on 
topics of current political debates in Bulgaria, 
Germany and the European Union.</p>
<p>Note that you can download all our publications for free.
</p>
<p>If you have suggestions or comments, or if you want to 
contact us, please do not hesitate to email us (Contact Us).
</p>
<p>Information about the activities and publications of the FES 
International is available <a href="#">here</a></p>
-->
</td>
<td style="width: 150px">
<img src="/files/custom/Home/WillyBrandt.png" alt="Willy Brandt" /> 
<b>“International co-
operationis far too 
important to beleft to 
governments alone."
</b>
<h2>Willy Brandt</h2>
</td>
</tr>
</table>
		</div>
<h3 style="width:550px; margin: 0 20px;">Highlights</h3>			
<table cellpadding="0" cellspacing="0" style="width: 100%;">
<tr>
<td style="padding: 10px 20px 0 20px;">				
<?php
$events_per_page = 2;
$last_events = getdb()->getAll("SELECT id,cid,title,subtitle,picture FROM news_pages WHERE cid IN (".$customCids['programmes'].") AND is_visible=1 ORDER BY due_date DESC LIMIT ".$events_per_page);

$last_events_html = '';
foreach ($last_events as $event) {
	$last_events_img = (!empty($event['picture'])) ? '<img src="../../files/mf/news_pages/'.$event['id'].'_picture_pic'.$event['picture'].'" alt="" class="img" />' : '';
	$last_events_href = '/?cid='.$event['cid'].'&amp;NewsId='.$event['id'];
	$last_events_html .= <<<EOD
		<div class="article">	
			<h1><a href="{$last_events_href}">{$event['title']}</a></h1>
			{$last_events_img}			
			<div class="text">
				{$event['subtitle']}
			</div>
<div class="readMore" style="text-align:right; margin-bottom: 10px;"><a href="{$last_events_href}" class="readMore">{$translation['read_more']}</a></div>			
			<div class="clear"></div>
		</div>
			
EOD;
}

echo  $last_events_html;
?>
</td>
<td id="sidebar">
<div style="text-align:center; color:#fff; font-size:13px; padding:4px; background-color:#036; font-weight:bold;"><?=$translation['publications']?></div>
<?php
$events_per_page = 1;
$last_events = getdb()->getAll("SELECT id,cid,title,subtitle,picture FROM news_pages WHERE cid IN (".$customCids['publications'].") AND is_visible=1 ORDER BY due_date DESC LIMIT ".$events_per_page);
$last_events_html = '';
$isFirst = true;
foreach ($last_events as $event) {
	//$last_events_img = (!empty($event['picture'])) ? '<img src="../../files/mf/news_pages/'.$event['id'].'_picture_pic'.$event['picture'].'" alt="" class="img" />' : '';
	$last_events_href = '/?cid='.$event['cid'].'&amp;NewsId='.$event['id'];
	$tmp_borderflag = $isFirst ? '' : ' style="border-top: 1px solid #036;"';
	$last_events_html .= <<<EOD
		<div class="article"{$tmp_borderflag}>	
			<h1><a href="{$last_events_href}">{$event['title']}</a></h1>
			<div class="text">
				{$event['subtitle']}
			</div>
<div class="readMore" style="text-align:right; margin-bottom: 10px;"><a href="{$last_events_href}" class="readMore">{$translation['read_more']}</a></div>						
			<div class="clear"></div>
		</div>		
EOD;
$isFirst = false;
}
?> 
<?= $last_events_html ?>
</td>
</tr>
</table>
		
