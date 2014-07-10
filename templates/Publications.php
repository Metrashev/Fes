<?php
$translation = array(
	'bg'=>array(
		'read_more'=>'Прочети повече',
		'archive'=>'Архив',
	),
	'en'=>array(
		'read_more'=>'Read More',
		'archive'=>'Archive',		
	),
	'de'=> array(
		'read_more'=>'Mehr',
		'archive'=>'Archiv',
	),
);
$translation = $translation[LNG_CURRENT];
$customCids = $GLOBALS['CONFIG']['customCids'][LNG_CURRENT];
$bo = new CBONews();
$db = getdb();

$cid = (int)$_GET['cid'];
//$_GET['cid'] = 13;
if (in_array($cid,array(13,21,35))) {
	$cid = 13;
}

if (in_array($cid,array(78,79,80))) {
	$cid = 78;
}

if (empty($_GET['NewsId'])) {
	
	$years = $db->getAssoc("SELECT DISTINCT YEAR(due_date) as year, YEAR(due_date) as year FROM news_pages WHERE cid=? ORDER BY year desc", array($cid));
	
	$year = (int)$_GET['year'];
	if(!array_key_exists($year, $years)) $year = key($years);

?>
<table cellpadding="0" cellspacing="0" style="width:100%;border-bottom:1px solid #c00;margin-bottom:20px;">
<tr>
	<td>
		<h3 style="border:none;"><?=$data['node']['value']?></h3>	
	</td>
	<td>
		<div id="arhiveForm">
		<form method="GET" action="">
			<input type="hidden" name="cid" value="<?=$_GET['cid']?>" />
			<?=$translation['archive']?> <select name="year" onchange="this.form.submit()">
				<?=draw_listbox_options($years, $year)?>
			</select>
		</form>
		</div>	
	</td>
</tr>
</table>
<?php
}

if($_GET['NewsId']){

	$news = $bo->getRow('*,picture', 'id = '.(int)$_GET['NewsId']);
if($news['picture']['pic']) {	
		 $img = <<<EOD
		 <img src="{$news['picture']['pic2']}" class="img" alt="{$news['title']}" />
EOD;
		} else {
			$img = "";
		}
echo <<<EOD
<div style="font-weight:bold;padding:5px 0;font-size:13px;">{$news['title']}</div>
<table>
<tr>
<td>{$img}</td>
<td style="padding-left:20px;">{$news['body']}
</td>
</tr>
</table>
EOD;
	
	return ;
}
?>
<?php
$bo = new CBONews();



$month = 0;
$data = $bo->getPagedList("/?cid={$_GET['cid']}".($_GET['year'] ? "&amp;year={$_GET['year']}" : ""),5,'id,title,subtitle,body,picture,due_date,href,cid,MONTH(due_date) as month', 'YEAR(due_date) = "'.$year.'" AND cid ="'.$cid.'"', "due_date DESC");

foreach ($data['data_list'] as $news){
	
	if($month!=$news['month']){
		$month=$news['month'];
		$tmp = date("F Y", $news['due_date']['row']);
		echo <<<EOD
<!--<div class="month">$tmp</div>-->
EOD;
	}
	
	
	$news['href'] = "/?cid={$_GET['cid']}&amp;NewsId={$news['id']}";
	 if($news['picture']['pic']) {
 	
		 $img = <<<EOD
		 <a href="{$news['href']}"><img src="{$news['picture']['pic']}" class="img" alt="{$news['title']}" /></a>
EOD;
		} else {
			$img = "";
		}
		
	if (!empty($news['body'])) {
		$readmore = <<<EOD
<div class="readMore" style="text-align:right; margin-bottom: 10px;"><a href="{$news['href']}" class="readMore">{$translation['read_more']}</a></div>
EOD;
	} else {
		$readmore = '';
	}
		
echo <<<EOD
<table style="width: 100%;">
<tr>
<td style="width: 90px; padding-bottom: 10px">{$img}</td>
<td>
	<div style="font-weight:bold;padding:5px 0;font-size:13px;">{$news['title']}</div>
	{$news['subtitle']}
	{$readmore}
</td>
</tr>
</table>
EOD;
}

include(dirname(__FILE__).'/Core/PageBar.php');
?>

