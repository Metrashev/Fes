<table cellpadding="0" cellspacing="0" border="0"><tr>
<?php

$css = array(

	12=>'Catalogue',
	5=>'News',
	6=>'Shops',
	9=>'AboutUs',
	8=>'Careers',
	7=>'Partners',
	
	
	20=>'News',
	21=>'Shops',
	22=>'AboutUs',
	25=>'Partners',
	
	27=>'News',
	28=>'Shops',
	29=>'AboutUs',
	32=>'Partners',	

);


$nodes = $GLOBALS['FESkinPage']->MenuItems;
	
foreach($nodes as $k=>$node){
	if($node['level']!=2) continue;
	if($node['visible']!=1) continue;
	$classes = array();
	
	if($node['selected']){
			$classes[] = 'down';
	}
	
	if($css[$node['id']])
		$classes[] = $css[$node['id']];
	

	$classes = empty($classes) ? '' : ' class="'.implode(' ',$classes).'"';
	
	echo "<td nowrap><a{$classes} href=\"{$node['href']}\"{$node['target']}>{$node['value']}</a></td>";
	

}
?>
</tr></table>