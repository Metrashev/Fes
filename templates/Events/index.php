<?php

$translation = array(
	'bg'=> array(
		'activities'=>'Дейности',
		'archive'=>'Архив',
	),
	'en'=> array(
		'activities'=> 'Activities',
		'archive'=>'Archive',		
	),
	'de'=> array(
		'activities'=> 'Aktivitäten',
		'archive'=>'Archiv',
	),
);

$translation = $translation[LNG_CURRENT];
$customCids = $GLOBALS['CONFIG']['customCids'][LNG_CURRENT];
$bo = new CBONewsCusom();
$db = getdb();

if (empty($_GET['NewsId'])) {
	
	$years = $db->getAssoc("SELECT DISTINCT YEAR(due_date) as year, YEAR(due_date) as year FROM news_pages WHERE cid=? ORDER by year DESC", array($_GET['cid']));
	
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

	$news = $bo->getRow('*,picture', 'id = '.(int)$_GET['NewsId']);
		echo ob_include(dirname(__FILE__).'/FullView.php', $news);
	return ;
}

$tmp_body = $db->getOne("SELECT body FROM static_pages WHERE cid=? AND def=1", array($_GET['cid']));
 if (!empty($tmp_body)) {
	echo $tmp_body;
	echo "<h3>{$translation['activities']}</h3>";
}

$bo = new CBONewsCusom();

$data = $bo->getPagedList("/?cid={$_GET['cid']}".($_GET['year'] ? "&amp;year={$_GET['year']}" : ""),5,'id,title,subtitle,picture,due_date,href,body,cid,MONTH(due_date) as month', 'YEAR(due_date) = '.$year.' AND cid ='.(int)$_GET['cid'], "due_date DESC");
echo ob_include(dirname(__FILE__).'/listEvents.php', $data['data_list']);

include(dirname(__FILE__).'/../Core/PageBar.php');
?>
