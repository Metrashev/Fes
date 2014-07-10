<?php

$translation = array(
	'bg'=>array(
		'back'=>'Назад',
		'print'=>'Печат',
		'top'=>'Нагоре',
		'bg'=>'Български',
		'en'=>'English',
		'de'=>'Deutsch',
		'home_link'=>'/?cid=5',
		'last_update'=>'Последно обновяване',
		'contact'=>'Контакти',
		'imprint'=>'Визитка'
	),
	'en'=>array(
		'back'=>'Back',
		'print'=>'Print',
		'top'=>'Top',		
		'bg'=>'Български',
		'en'=>'English',
		'de'=>'Deutsch',	
		'home_link'=>'/?cid=17',
		'last_update'=>'Last update',
		'contact'=>'Contact',
		'imprint'=>'Imprint'
	),
	'de'=>array(
		'back'=>'Zurück',
		'print'=>'Drucken',
		'top'=>'Zum Anfang',
		'bg'=>'Български',
		'en'=>'English',
		'de'=>'Deutsch',		
		'home_link'=>'/?cid=29',
		'last_update'=>'Letztes Update',
		'contact'=>'Kontakt',
		'imprint'=>'Impressum'
	),
);
$translation = $translation[LNG_CURRENT];
$customCids = $GLOBALS['CONFIG']['customCids'][LNG_CURRENT];

function getTopImg(){
/*	
	$nodesPath = $GLOBALS['fc']->nodesPath;
	for($i=count($nodesPath)-1; $i>=0; $i--)
    {
      $row = $nodesPath[$i];	
      if($row['img']){
        return '/files/mf/categories/'.$row['id'].'_img_1'.$row['img'];
        
      }
    }
	return '/i/top_img.png';
*/

  $img = getdb()->getRow("SELECT * FROM gallery WHERE cid = 1 AND page_id = 1 ORDER BY RAND() LIMIT 1");
  return "/files/mf/gallery/{$img['id']}_img_1{$img['img']}";
}
?>
<!DOCTYPE html
     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=LNG_CURRENT?>" lang="<?=LNG_CURRENT?>">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title><?php echo $data['PageTitle'];?></title>
	<link rel="stylesheet" type="text/css"  href="/lib.css" />

<?php if($data['isIE4']){ ?>
	<link rel="stylesheet" type="text/css"  href="/lib_ie4.css" />
<?php } ?>
	<script language="JavaScript" type="text/javascript" src="/js/swfobject.js"></script>
	<script language="JavaScript" type="text/javascript" src="/js/lib.js"></script>
	
	<?php echo  $data['Header'] ?>
</head>



<body<?php 
	if ((in_array($_GET['cid'],array($customCids['home']))) || empty($_GET['cid'])) {
		echo ' id="homepage"';
	}
 ?>>
 <div class="wrapHeader">
 	<div class="headerContent">
		<div id="header">
			<!--<a href="<?=$translation['home_link'];?>"><img style="padding:16px;" src="/i/fes-logo_<?=LNG_CURRENT?>.gif" alt="Friedrich-Ebert-Stiftung" /></a>-->
			<!--<img src="/i/top_img_l.png" alt="" /><img src="<?=getTopImg()?>" alt="" /><img src="/i/top_img_r.png" alt="" />-->	
			<a target="_blank" href="http://www.fes.de"><img style="padding:16px;" src="/i/fes-logo_<?=LNG_CURRENT?>.gif" alt="Friedrich-Ebert-Stiftung" /></a>
		</div>
		<div id="languages">
			<?php 	
				$languagesLink = array();
				foreach ($GLOBALS['CONFIG']['SiteLanguages'] as $k => $v) {
					$translation[$k];
					$GLOBALS['CONFIG']['customCids'][$k]['home'];
					
					$activity = LNG_CURRENT == $k ? ' class="activeMenuLink"' : '';
					$languagesLink[] = <<<EOD
					<a href="/?cid={$GLOBALS['CONFIG']['customCids'][$k]['home']}"{$activity}>{$translation[$k]}</a>
EOD;
				}
				echo implode(" | ", $languagesLink);
				?>
		</div>
		<div id="searchForm">
			<form method="get">
				<input type="hidden" name="cid" value="74" />
				<input type="text" name="q" style="height:16px;" /><input type="submit" value="Search" />
			</form>
		</div>
	</div>
</div>
<div class="wrapMain">
	<div>
		<div>
			<div class="wrapContent">
				<div id="main">
				<table cellpadding="0" cellspacing="0" id="maintable">
				<tr>
					<td id="leftnav" width="180">
						<? include('templates/Nav/LeftNav.php');?>
					</td>
					<td style="background-color:#fff;vertical-align:top;" width="780">
						<table cellpadding="0" cellspacing="0" style="width:100%;">
						<tr>
							<td style="vertical-align:top;"><img src="<?=getTopImg()?>" style=";border:none;padding:0; margin-left: 5px;" /></td>
							<td style="width:200px; background: url(i/address_background.png) no-repeat; padding-left: 17px;">
								<?php
									$res = getdb()->getOne("SELECT body FROM boxes WHERE id = ".$customCids['address-box']);
									echo $res;
								?>
							</td>
						</tr>	
						</table>
						<div id="content">
							<?php 
								if ($GLOBALS['fc']->node['is_crumb_visible']) {
									echo "<div id='crumbsPath'>".$data['CrumbsPath']."</div>";
								}
							?>
							<?php echo $data['body']; ?>
						</div>
						<div id="content_footer">
							<div class="right">
								<a class="back" href="JavaScript:history.go(-1)"><?=$translation['back']?></a>
								<a class="top" href="#"><?=$translation['top']?></a>
								<a class="printPage" href="<?=$data['PrintLinkHref']?>"  target='_blank' onClick='this.href = getPrintLink();'><?=$translation['print']?></a>
							</div>
							<div class="clear"></div>
						</div>							
					</td>
				</tr>
				</table>
				<!-- <div id="copyright">© 2001 <a href="/" style="color:#ffffff; font-size: 10px;">Friedrich-Ebert-Stiftung</a></div> -->
				</div>
			</div>
			
			<div style="background: url(i/footer.png) repeat-x; height:84px; width: 980px; margin:0 auto;">
				<div style="text-align: center;">
					<span style="line-height: 38px;"><?=$translation['last_update']?> <?=getdb()->getOne("SELECT data FROM php_data WHERE id='last_updated'");?>| <a class="mail" href="mailto:emilia.burgaslieva@fes.bg">emilia.burgaslieva@fes.bg</a>
					</span>
					<span style="line-height: 38px; margin-left: 25px;">
					<a href="/?cid=<?=$customCids['contact']?>"><?=$translation['contact']?></a> | <a title="Link to Imprint" href="/?cid=<?=$customCids['imprint']?>"><?=$translation['imprint']?></a>
					</span>
				</div>
				<div style="margin-top: 15px; color: #6A8BAF; text-align: center;">
					&copy; 2014 Friedrich-Ebert-Stiftung
				</div>
				</div>
				</div>
			</div>
		</div>
</body>

</html>