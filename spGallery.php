<gallery>
<?php

  require_once('config/config.php');
  require_once('lib/db.php');
  require_once('lib/SysUtils.php');
  require_once('lib/ErrorHandling.php');
  require_once('lib/fe/lib.php');

$cid = (int)$_GET['cid'];
$pid = (int)$_GET['pid'];

$db=getdb();
 $l=$db->getAll("select id,cid,img from gallery where cid='{$cid}' and img!=''");
 foreach ($l as $img_id=>$img){
 	echo "<img src='/files/mf/gallery/{$img['id']}_img_s{$img['img']}'/>";
 }
?>
</gallery>