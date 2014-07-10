<?php
$customCids = $GLOBALS['CONFIG']['customCids'][LNG_CURRENT];
$bo = new CBONews();
$db = getdb();
$archiveId = $customCids['publications'];
if (empty($_GET['NewsId'])) {
	//$list_titles = getdb()->getAssoc("SELECT id,value FROM categories WHERE pid =".$customCids['programme']." AND type_id=2 ORDER BY l");
	$years = $db->getAssoc("SELECT DISTINCT YEAR(due_date) as year, YEAR(due_date) as year FROM news_pages");
	
	//$archiveId = (int)$_GET['archiveId'];
	//if(!array_key_exists($archiveId, $list_titles)) $archiveId = key($list_titles);
		
	$year = (int)$_GET['year'];
	if(!array_key_exists($year, $years)) $year = key($years);

?>
<div id="arhiveForm">
<form method="GET" action=""> 
	<input type="hidden" name="cid" value="<?=$customCids['publicationsarchive']?>" />
	<select name="year" onchange="this.form.submit()">
		<?=draw_listbox_options($years, $year)?>
	</select>
</form>
</div>
<?php
}
echo "<h1>".$data['node']['value']."</h1>";
if($_GET['NewsId']){

	$news_data = $bo->getRow('*', 'id = '.(int)$_GET['NewsId']);

	echo ob_include(dirname(__FILE__).'/../News/FullNews.php', $news_data);
	
	return ;
}
?>
<?php
$bo = new CBONews();


$news_data = $bo->getPagedList("/?cid=".$_GET['cid'], 20, 'id,title,subtitle,picture,due_date,href,'.$_GET['cid'].' AS cid', 'YEAR(due_date) = '.$year.' AND cid ='.$archiveId, "due_date DESC");

echo ob_include(dirname(__FILE__).'/../News/NewsList.php', $news_data);


?>
