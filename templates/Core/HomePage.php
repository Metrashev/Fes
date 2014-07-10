<?php

$translation = array(
	'bg'=>array(
		'read_more'=>'повече',
		'publications'=> 'Нови публикации',
		'highlights'=>'Актуално',
	),
	'en'=>array(
		'read_more'=>'Read More',
		'publications'=>'New Publications',
		'highlights'=>'Events',
	),
	'de'=>array(
		'read_more'=>'Mehr',
		'publications'=>'Neue Publikationen',
		'highlights'=>'Aktuell',
	),
);
$translation = $translation[LNG_CURRENT];

$customCids = $GLOBALS['CONFIG']['customCids'][LNG_CURRENT];
$customCids['publications'] = $GLOBALS['CONFIG']['customCids']['bg']['publications'];

$home_html = getdb()->getRow("SELECT title,body FROM static_pages WHERE cid = ".$customCids['home']);

?>
		
<table cellpadding="0" cellspacing="0" style="width: 100%;">
	<tbody>
		<tr>
			<td id="welcome"><?=<<<EOD
	<div>
		<h1>{$home_html['title']}</h1>
		{$home_html['body']}
	</div>
EOD;
?></td>
		<?php 
			$activityLanguages = array(
				LNG_BG=>53,
				LNG_DE=>36,
				LNG_EN=>19,
			);
			$ActivitiesTitle = getdb()->getOne("SELECT value FROM categories WHERE id=?", array($activityLanguages[LNG_CURRENT]));
			$Activities = getdb()->getAssoc("SELECT id, value FROM categories WHERE pid=?", array($activityLanguages[LNG_CURRENT]));
// 			echo '<pre>'.print_r($Activities, true).'</pre>';
			
		?>
		
			<td id="activity">
			<div><?=$ActivitiesTitle?></div>
			<div>
				<?php 
					foreach ($Activities as $cid=>$value) {
						echo "<ol><li><a href='/?cid={$cid}'>{$value}</a></li></ol>";
					}
				?>
			</div>
		</td>
		</tr>
		<tr>
			<td id="reports">
				<?php 
					$reportAndAnalysis = array(
							LNG_BG=>78,
							LNG_DE=>80,
							LNG_EN=>79,
					);
					$publicationCurrent = array(
							LNG_BG=>13,
							LNG_DE=>35,
							LNG_EN=>21,
					);
					$ReportName=getdb()->getOne("SELECT value FROM categories WHERE id=?", array($reportAndAnalysis[LNG_CURRENT]));
					$PublicationsName = getdb()->getOne(" SELECT value FROM categories WHERE id=?", array($publicationCurrent[LNG_CURRENT]));
					$events_per_page = 2;
					$publications = getdb()->getAll("SELECT id,cid,title,picture FROM news_pages WHERE cid={$publicationCurrent[LNG_CURRENT]} AND is_visible=1 ORDER BY due_date DESC LIMIT ".$events_per_page);
					$reports_and_analilyses = getdb()->getAll("SELECT id,cid,title,picture FROM news_pages WHERE cid={$reportAndAnalysis[LNG_CURRENT]} AND is_visible=1 ORDER BY due_date DESC LIMIT ".$events_per_page);
					$last_events = array();
					if (!empty($publications)) {
						$last_events[] = $publications;
					}
					if (!empty($reports_and_analilyses)) {
						$last_events[] = $reports_and_analilyses;
					}
					$last_events_publications_html = '';
					$last_events_reports_html = '';
					$isFirst = true;
					echo '<div>'.$ReportName.'</div>';
					//echo '<pre>'.print_r($last_events, true).'</pre>';
					foreach ($last_events as $key => $v) {
						foreach ($v as $event) {
							$last_events_img = (!empty($event['picture'])) ? '<img src="../../files/mf/news_pages/'.$event['id'].'_picture_pic'.$event['picture'].'" alt="" class="img" />' : '';
							$last_events_href = '/?cid='.$event['cid'].'&amp;NewsId='.$event['id'];
							$tmp_borderflag = $isFirst ? '' : '';
							if($key < 1) {
								$last_events_publications_html .= <<<EOD
								<a href="{$last_events_href}" style="float:left;"><img src="../../files/mf/news_pages/{$event['id']}_picture_pic{$event['picture']}" alt="" /></a>
								<div class="article">
									<h1><a href="{$last_events_href}">{$event['title']}</a></h1>
									<div class="clear"></div>
								</div>
EOD;
								$isFirst = false;
							}
							else {
								$last_events_reports_html .= <<<EOD
								<a href="{$last_events_href}" style="float:left;"><img src="../../files/mf/news_pages/{$event['id']}_picture_pic{$event['picture']}" alt="" /></a>
								<div class="article">
									<h1><a href="{$last_events_href}">{$event['title']}</a></h1>
									<div class="clear"></div>
								</div>
EOD;
								$isFirst = false;
							}
						}
					}
					echo $last_events_publications_html;
				?>

			</td>
			<td id="publications">
				<div><?=$PublicationsName?></div>
				<?=$last_events_reports_html?>
			</td>
		</tr>
	</tbody>
</table>
		
