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

$bo = new CBONews();
$db = getdb();
//$_GET['cid'] = 55;
$cid = 55;

if (empty($_GET['NewsId'])) {
	
	$years = $db->getAssoc("SELECT DISTINCT YEAR(due_date) as year, YEAR(due_date) as year FROM news_pages WHERE cid=? ORDER BY year DESC", array($cid));
	
	$year = (int)$_GET['year'];
	if(!array_key_exists($year, $years)) $year = key($years);

?>
<table cellpadding="0" cellspacing="0" style="width:100%;border-bottom:1px solid #c00;">
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

	$news = $bo->getRow('*,picture,source,source_href', 'id = '.(int)$_GET['NewsId']);
		if ($news['source']) {
			
		$source = $news['source_href'] ? "<a href=\"{$news['source_href']}\">{$news['source']}</a>" : $news['source'];

	} else {
		$source = '';	
	}	
		
echo <<<EOD
<div style="border-bottom: 1px solid #69c;">
	<div class="subInfo">{$source} {$news['due_date']['date']}</div>
	<h1>{$news['title']}</h1>
	<div class="text">{$news['body']}</div>
</div>
EOD;
	
	return ;
}
?>
<?php
$bo = new CBONews();

$data = $bo->getPagedList("/?cid={$_GET['cid']}".($_GET['year'] ? "&amp;year={$_GET['year']}" : ""),4,'id,title,subtitle,picture,due_date,body,href,source,source_href,cid,MONTH(due_date) as month', 'YEAR(due_date) = '.$year.' AND cid ='.$cid, "due_date DESC");

foreach ($data['data_list'] as $news){
	 if(isset($news['picture']['pic'])) {
 	
		 $img = <<<EOD
		 <a href="{$news['href']}"><img src="{$news['picture']['pic']}" class="img" alt="{$news['title']}" /></a>
EOD;
		} else {
			$img = "";
		}
		
	if ($news['source']) {
		$source = $news['source_href'] ? "<a href=\"{$news['source_href']}\" target=\"_blank\">{$news['source']}</a>" : $news['source'];
	} else {
		$source = '';	
	}
	$news['href'] = "/?cid={$_GET['cid']}&amp;NewsId={$news['id']}";
	if (!empty($news['body'])) {
		$readmore = <<<EOD
<div class="readMore" style="text-align:right; margin-bottom: 10px;"><a href="{$news['href']}" class="readMore">{$translation['read_more']}</a></div>
EOD;
	} else {
		$readmore = '';
	}
		
echo <<<EOD
<div style="border-bottom: 1px solid #69c;padding-bottom:20px;">
		<div class="subInfo" style="padding-bottom:10px;padding-top:6px;">{$source} {$news['due_date']['date']}</div>
		<h1>{$news['title']}</h1>
		<div>{$news['subtitle']}</div>
		{$readmore}
</div>
EOD;
}

include(dirname(__FILE__).'/Core/PageBar.php');
?>