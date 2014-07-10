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

if (empty($_GET['NewsId'])) {

	$list_titles = getdb()->getAssoc("SELECT id,value FROM categories WHERE pid =".$customCids['programme']." AND type_id=255 and template_id=6 ORDER BY l");	
	$years = $db->getAssoc("SELECT DISTINCT YEAR(due_date) as year, YEAR(due_date) as year FROM news_pages WHERE cid IN ({$customCids['programmes']}) ORDER BY year DESC");
	

		
	$year = (int)$_GET['year'];
	if(!array_key_exists($year, $years)) {
		$year = key($years);
	}
	
	$tmp = $db->getAssoc("SELECT distinct cid, 1 FROM news_pages WHERE due_date BETWEEN '$year-01-01' AND '$year-12-31'");
	foreach ($list_titles as $k=>$v){
		if(!$tmp[$k]) unset($list_titles[$k]);
	}
	

	reset($list_titles);
		
		
	$archiveId = (int)$_GET['archiveId'];
	if(!array_key_exists($archiveId, $list_titles)) $archiveId = key($list_titles);		
?>
<table cellpadding="0" cellspacing="0" style="width:100%;border-bottom:1px solid #c00;">
<tr>
	<td>
		<h3 style="border:none;"><?=$data['node']['value']?></h3>
	</td>
	<td>
<div id="arhiveForm">
<form method="GET" action=""> 
	<input type="hidden" name="cid" value="<?=$customCids['ActivitiesArchive']?>" />
<?=$translation['archive']?>
	<select name="year" onchange="this.form.submit()">
		<?=draw_listbox_options($years, $year)?>
	</select>

	<select name="archiveId" onchange="this.form.submit()">
		<?=draw_listbox_options($list_titles, $_GET['archiveId'])?>
	</select>
</form>
</div>	
	</td>
</tr>
</table>

<?php
}
if($_GET['NewsId']){

	$news_data = $bo->getRow('*', 'id = '.(int)$_GET['NewsId']);
	
	if (!empty($news_data['picture'])) {
		$pic = $news_data['picture'];
		$news_data['picture'] = array();
		$news_data['picture']['pic'] = '/files/mf/news_pages/'.$news_data['id'].'_picture_pic'.$pic;
		$news_data['picture']['pic2'] = '/files/mf/news_pages/'.$news_data['id'].'_picture_pic2'.$pic;
	}
	
	echo ob_include(dirname(__FILE__).'/../Events/FullView.php', $news_data);
	
	return ;
} else if(!$_GET['year']){
	echo $db->getOne("SELECT body FROM static_pages WHERE cid=? AND def=1", array($_GET['cid']));
	return ;
}


?>
<?php
$bo = new CBONews();


$data = $bo->getPagedList("/?cid={$_GET['cid']}".($_GET['year'] ? "&amp;year={$_GET['year']}&amp;archiveId={$_GET['archiveId']}" : ""),20,'id,title,subtitle,picture,due_date,href,body,cid,MONTH(due_date) as month,'.$_GET['cid'].' AS cid', 'YEAR(due_date) = '.$year.' AND cid ='.$archiveId, "due_date DESC");

echo ob_include(dirname(__FILE__).'/../Events/listEvents.php', $data['data_list']);
include(dirname(__FILE__).'/../Core/PageBar.php');

?>
