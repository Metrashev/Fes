<?php

$GLOBALS['SKIP_CTX']=true;

if(!isset($_GET['q'])) {
	header("Location: /");
	exit;
}

require_once(dirname(__FILE__)."/../../lib/search_utils.php");

$q=trim($_GET['q']);

$cids=array(
	'bg'=>9,
	'en'=>21,
);

$translation=array(
	'bg'=>array(
		'no'=>"Няма намерени резултати!",
	),
	'en'=>array(
		'no'=>"No matches found!",
	),

);

$translation=$translation[LNG_CURRENT];

if(empty($q)) {
	echo $translation['no'];
	return;	
}


$pagesize=20;


$p=(int)$_GET['p'];

$lng=LNG_CURRENT==DEFAULT_LANGUAGE?"":"_".LNG_CURRENT;

$a=array(
	0=>array(
		"label"=>array('bg'=>"Страници","en"=>"Pages"),
		'sql'=>"select SQL_CALC_FOUND_ROWS static_pages.id,title,cid from static_pages inner join categories on cid=categories.id where use_in_search=1 and visible=1 and language_id='".LNG_CURRENT."'",
		'cnt'=>"select count(*) from static_pages inner join categories on cid=categories.id where use_in_search=1 and visible=1 and language_id='".LNG_CURRENT."'",
		'fields'=>array("title","body"),
		'href'=>"/?cid=_#CID#_&amp;spid=_#ID#_",
		
	),
	1=>array(
		"label"=>array('bg'=>"Новини","en"=>"News"),
		'sql'=>"select SQL_CALC_FOUND_ROWS news_pages.id,title,subtitle,cid from news_pages inner join categories on cid=categories.id where use_in_search=1 and visible=1 and is_visible=1 and due_date<now() and language_id='".LNG_CURRENT."'",
		'cnt'=>"select count(*) from news_pages inner join categories on cid=categories.id where use_in_search=1 and visible=1 and is_visible=1 and due_date<now() and language_id='".LNG_CURRENT."'",
		'order'=>"due_date desc",
		'fields'=>array("title","subtitle","body"),
		'href'=>"/?cid=_#CID#_&amp;NewsId=_#ID#_",
	),
	2=>array(
		"label"=>array('bg'=>"Меню","en"=>"Menu"),
		'sql'=>"select SQL_CALC_FOUND_ROWS id,value as title,id as cid from categories where use_in_search=1 and visible=1 and language_id='".LNG_CURRENT."'",
		'cnt'=>"select  count(*) from categories where use_in_search=1 and visible=1 and language_id='".LNG_CURRENT."'",
		'order'=>'l',
		'fields'=>array("value"),
		'href'=>"/?cid=_#CID#_",
	),
/*	3=>array(
		"label"=>array('bg'=>"Категории продукти","en"=>"Product categories"),
		'sql'=>"select SQL_CALC_FOUND_ROWS id,value{$lng} as title,id as cid from product_categories where use_in_search=1 and visible=1",
		'cnt'=>"select  count(*) from product_categories where use_in_search=1 and visible=1",
		'order'=>'l',
		'fields'=>array("value{$lng}"),
		'href'=>"/?cid={$cids[LNG_CURRENT]}&amp;cat=_#CID#_",
	),
	4=>array(
		"label"=>array('bg'=>"Продуктови групи","en"=>"Products groups"),
		'sql'=>"select SQL_CALC_FOUND_ROWS id,name{$lng} as title,cid from products where 1",		
		'cnt'=>"select count(*) from products where 1",		
		'fields'=>array("name{$lng}","description{$lng}"),
		'href'=>"/?cid={$cids[LNG_CURRENT]}&amp;cat=_#CID#_&amp;pid=_#ID#_",
	),
	5=>array(
		"label"=>array('bg'=>"Техническа документация","en"=>"Technical Documentation"),
		'sql'=>"select SQL_CALC_FOUND_ROWS product_files.id,file_name{$lng} as title,product_id,cid from product_files inner join products on product_id=products.id where 1",
		'cnt'=>"select count(*) from product_files inner join products on product_id=products.id where 1",
		'fields'=>array("file_name{$lng}"),
		'order'=>'order_field',
		'href'=>"/?cid={$cids[LNG_CURRENT]}&amp;cat=_#CID#_&amp;pid=_#PRODUCT_ID#_&amp;tab=1",
	),
	6=>array(
		"label"=>array('bg'=>"Видео","en"=>"Video"),
		'sql'=>"select SQL_CALC_FOUND_ROWS product_video.id,title{$lng} as title,product_id,cid from product_video inner join products on product_id=products.id where 1",
		'cnt'=>"select  count(*) from product_video inner join products on product_id=products.id where 1",
		'fields'=>array("title{$lng}","product_video.description{$lng}"),
		'order'=>'order_field',
		'href'=>"/?cid={$cids[LNG_CURRENT]}&amp;cat=_#CID#_&amp;pid=_#PRODUCT_ID#_&amp;tab=2&amp;videoId=_#ID#_",
	),*/
/*	
	7=>array(
		"label"=>array('bg'=>"Производители","en"=>"Manifacturers"),
		'sql'=>"select SQL_CALC_FOUND_ROWS id,name{$lng} as title from manifacturer where 1",
		'cnt'=>"select count(*) from manifacturer where 1",
		'fields'=>array("name{$lng}","description{$lng}","url"),
		'href'=>""	
	),
*/	
/*	8=>array(
		"label"=>array('bg'=>"Продукти","en"=>"Products"),
		'sql'=>"select SQL_CALC_FOUND_ROWS store_xls.web_cat AS product_id, CONCAT( products.name{$lng}, ' - ', store_xls.name{$lng}) as title,cid FROM store_xls INNER JOIN products ON store_xls.web_cat=products.id WHERE 1",
		'cnt'=>"select count(*) from store_xls inner join products on store_xls.web_cat=products.id where 1",
		'fields'=>array("store_xls.name{$lng}"),
		'order'=>'web_cat',
		'href'=>"/?cid={$cids[LNG_CURRENT]}&amp;cat=_#CID#_&amp;pid=_#PRODUCT_ID#_",
	),
	*/
);

$db=getdb();

$results=array();

$tab=(int)$_GET['tab'];

if(!isset($a[$tab])) {
	$tab=0;
}




	if($p) {
		$p--;
	}
	
	

	
foreach ($a as $k=>$v) {
	$keywords=SearchUtils::getKeyWords($q,$v['fields']);
	
	$where=" and ".$keywords.' ';
	$sql=$v['sql'].$where;
	$cnt=$v['cnt'].$where;
	/*-----------------*/

	if(isset($v['order'])) {
		$sql .= ' order by '.$v['order'].' ';		
	}

	if($k==$tab) {
		$start=$p*$pagesize;
		$sql .= " limit {$start},{$pagesize}";
		$r=$db->Query($sql);
	
		$c=$db->getOne("select FOUND_ROWS()");
	} else {
		$c=(int)$db->getone($cnt);
	}
	if(!$c) {
		continue;
	}
	
	if($tab<$k&&!isset($results[$tab])) {
		$tab=$k;
		$r = $db->Query($sql. "LIMIT $pagesize");
	}
	
	$results[$k]['cnt']=$c;
	if($k!=$tab) {
		continue;
	}
	$results[$k]['rows']=$r;
	/*$order="";
	if(isset($v['order'])) {
		$order=' order by '.$v['order'].' ';		
	}
	
	if($p) {
		$p--;
	}
	if($results[$k]['cnt']<$p*$pagesize) {
		$p=0;
	}
	$start=$p*$pagesize;
	$results[$k]['rows']=$db->getAssoc($sql.$order." limit {$start},{$pagesize}");*/
	
}

$selected[$tab]=" class=\"current\"";

?>
<div style="padding:0px">
	<h3>Search</h3>
	<div style="padding:10px 0;font-size:12px;color:#6380BC;font-weight:bold;">
		Searching for: "<?=$_GET['q'];?>"
	</div>
	<div class="listTabs" style="margin:0px">
		<div id="tab_header">
			<ul>
			<?php
			//$href=str_replace(array("_#CID#_","_#PRODUCT_ID#_","_#ID#_"),array($v['cid'],$v['product_id'],$v['id']),$a[$k]['href']);
				$g=$_GET;			
				unset($g['p']);
				foreach ($results as $k=>$v) {				
					$g['tab']=$k;
					$g_href=http_build_query($g,null,"&amp;");
					echo <<<EOD
					<li {$selected[$k]}><a style="font-size:10px;" href="/?{$g_href}">{$a[$k]['label'][LNG_CURRENT]}({$v['cnt']})</a></li>
EOD;
				}
			?>
			</ul>
		</div>
	</div>
</div>
<div class="clear"></div>

<?php
if(empty($results[$tab]['rows'])) {
	echo $translation['no'];
	return;
}

$b=array();
foreach ($results[$tab]['rows'] as $v) {
	$href=str_replace(array("_#CID#_","_#PRODUCT_ID#_","_#ID#_"),array($v['cid'],$v['product_id'],$v['id']),$a[$tab]['href']);
	$b[]=<<<EOD
	<a href="{$href}">{$v['title']}</a>
EOD;
}
?>
<div class="listTable_div" style="margin-bottom:5px;">
	<table class="listTable" width="100%" cellpadding="5" cellspacing="0">
	<?=arrayToTableStyle(convert_1d_to_2d_array($b,"Horizontal",1));?>
	</table>
</div>

<? if ($results[$tab]['cnt']>$pagesize) {
	
	$pb=new CFEPageBar($pagesize,$results[$tab]['cnt']);
	$href=$_GET;
	unset($href['p']);
	$href=http_build_query($href);
	$data['PageBar']=$pb->getData("/?{$href}");
	
	include(dirname(__FILE__)."/../Core/PageBar.php");
}
?>