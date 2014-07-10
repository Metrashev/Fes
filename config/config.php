<?php

define('ITTI_VERSION', '1');

define('ITTI_VERSION_MIN', '2');



define('DATE_FORMAT','%d.%m.%Y');

define('TIME_FORMAT','%H:%i:%s');



define('LNG_BG','bg');	

define('LNG_EN','en');

define('LNG_DE','de');



define('DEFAULT_LANGUAGE','bg');	









define('DEBUG_MODE', strpos($_SERVER['HTTP_HOST'], 'itti.bg')>0);



if(DEBUG_MODE){

	$CONFIG['DSN']="mysql://root:kustendil@localhost/fes";

} else {

	$CONFIG['DSN']="mysql://fesbg_web:web@localhost/fesbg_web";

}



$CONFIG['NAMES_CHARACTERS_SET']='UTF8';

$CONFIG['SITE_CHARSET']='UTF-8';

mb_internal_encoding($CONFIG['SITE_CHARSET']);



$CONFIG['ApplicationState']= DEBUG_MODE ? 'Debug' : '';

$CONFIG['ErrorLevel'] = E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED & ~E_WARNING;





$CONFIG['SiteName'][LNG_BG] = 'Фондация "Фридрих Еберт"';  // наименование на сайта - browzer title

$CONFIG['SiteName'][LNG_EN] = 'Friedrich-Ebert-Stiftung';

$CONFIG['SiteName'][LNG_DE] = 'Friedrich-Ebert-Stiftung';



$CONFIG['SiteLanguages'] = array(LNG_BG=>'Bulgarian', LNG_EN=>'English',LNG_DE=>'Deustch');



$CONFIG['DefautlCID'] = 5;





$CONFIG['Skins'] = array();

$CONFIG['Skins'][1] = array('name'=>'Основен', 'file'=>'templates/Core/template.php');



$CONFIG['PrintSkin'] = 'templates/Core/printSkin.php';

$CONFIG['Error404Skin'] = 'Error404.php';





$CONFIG['FEPageTypes'] = array();

$CONFIG['FEPageTypes'][1] = array('name'=>'Статична страница', 'class'=>'CFEStaticPage');

$CONFIG['FEPageTypes'][2] = array('name'=>'Новини', 'class'=>'CFENewsPage');

$CONFIG['FEPageTypes'][3] = array('name'=>'Препратка', 'class'=>'CFERedirectPage');

$CONFIG['FEPageTypes'][4] = array('name'=>'Галерия', 'class'=>'CFEGallery');



$CONFIG['FEPageTypes'][255] = array('name'=>'Специфична', 'class'=>'CFECustomPage');







$CONFIG['CFERedirectPage']['be']['tree'] = '/be/categories/redirect.php';

$CONFIG['CFENewsPage']['be']['tree'] = '/be/categories/news.php';

$CONFIG['CFEGallery']['be']['tree'] = '/be/categories/Custom/gallery.php';





$CONFIG['CFEStaticPage']['be']['menu'] = '/be/static_pages/edit.php?loadDef=1&amp;n_cid=';

$CONFIG['CFENewsPage']['be']['menu'] = '/be/news_pages/?loadDef=1&amp;id=';

$CONFIG['CFEGallery']['be']['menu'] = '/be/gallery/?cid=';





$CONFIG['CFEStaticPage']['templates'][1] = array('name'=>'Основен', 'file'=>'templates/Core/StaticPage.php');

$CONFIG['CFENewsPage']['templates'][1] = array('name'=>'Основен', 'fileList'=>'templates/News/NewsList.php', 'fileFull'=>'templates/News/FullNews.php');

$CONFIG['CFEGallery']['templates'][1] = array('name'=>'Основен', 'file'=>'templates/Gallery/gallery.php');

$CONFIG['CFEGallery']['templates'][2] = array('name'=>'На страници', 'file'=>'templates/Gallery/gallery2.php', 'ItemsPerPage'=>18);







$CONFIG['CFECustomPage']['templates'][1] = array('name'=>'HomePage', 'file'=>'templates/Core/HomePage.php');

$CONFIG['CFECustomPage']['templates'][2] = array('name'=>'Poll Archive', 'file'=>'templates/Poll/archive.php');

$CONFIG['CFECustomPage']['templates'][3] = array('name'=>'Full Archive', 'file'=>'templates/archives/fullarchive.php');

$CONFIG['CFECustomPage']['templates'][4] = array('name'=>'Years Archive', 'file'=>'templates/archives/yearsarchive.php');

$CONFIG['CFECustomPage']['templates'][5] = array('name'=>'В пресата', 'file'=>'templates/inthepress.php');

$CONFIG['CFECustomPage']['templates'][6] = array('name'=>'Събития', 'file'=>'templates/Events/index.php');

$CONFIG['CFECustomPage']['templates'][7] = array('name'=>'Издания', 'file'=>'templates/Publications.php');

$CONFIG['CFECustomPage']['templates'][8] = array('name'=>'Search', 'file'=>'templates/search/index.php');





$CONFIG['CFEStaticPage']['be']['tree'][1] = '/be/categories/Custom/sp.php';



/*

$CONFIG['CFECustomPage']['templates'][2] = array('name'=>'Gallery', 'file'=>'templates/Gallery/gallery.php');

$CONFIG['CFECustomPage']['be']['menu'][2] = '/be/gallery/?cid=_#CID#_';

$CONFIG['CFECustomPage']['be']['tree'][2] = '/be/categories/Custom/gallery.php';

*/



$CONFIG['CFECustomPage']['be']['menu'][5] = '/be/news_pages/?loadDef=1&amp;id=_#CID#_';

$CONFIG['CFECustomPage']['be']['menu'][6] = '/be/news_pages/?loadDef=1&amp;id=_#CID#_';

$CONFIG['CFECustomPage']['be']['menu'][7] = '/be/news_pages/?loadDef=1&amp;id=_#CID#_';

$CONFIG['CFECustomPage']['be']['menu'][3] = '/be/static_pages/edit.php?loadDef=1&amp;n_cid=_#CID#_';

$CONFIG['CFECustomPage']['be']['menu'][1] = '/be/static_pages/edit.php?loadDef=1&amp;n_cid=_#CID#_';







$CONFIG['CFEStaticPage']['be']['delete'] = array('file'=>'/be/categories/del_functions.php','functions'=>array('getMessage'=>array('sp_class','getMessage'),'process_delete'=>array('sp_class','processDelete'))); 

$CONFIG['CFENewsPage']['be']['delete'] = array('file'=>'/be/categories/del_functions.php','functions'=>array('getMessage'=>array('news_class','getMessage'),'process_delete'=>array('news_class','processDelete'))); 





$CONFIG['AutoLoad']=array(

	

	'CBONews'=>'lib/fe/news.php',

	'CBONewsCusom'=>'lib/fe/news.php',

	'CFENewsPage'=>'lib/fe/news.php',

	'CFEGallery'=>'lib/fe/CFEGallery.php',



);



$CONFIG['CID_1_SIZES']=array(

	's'=>array(100,100),

	'm'=>array(320,240),

	'l'=>array(0,0),

);



$CONFIG['customCids'] = array(

	'bg'=> array(

		'home'=>5,

		'programme'=>53,

		'programmes'=>'12,58,59,60,61,62',

		'publications'=>13,

		'publicationsarchive'=>72,

		'ActivitiesArchive'=>73,

		'contact'=>14,

		'imprint'=>15,

		'address-box'=>1,

	),

	'en'=> array(

		'home'=>17,

		'programme'=>19,	

		'programmes'=>'27,52,47,48,49,50',	

		'publications'=>21,

		'publicationsarchive'=>51,

		'ActivitiesArchive'=>28,

		'contact'=>23,

		'imprint'=>24,

		'address-box'=>2,		

	),

	'de'=> array(

		'home'=>29,

		'programme'=>36,	

		'programmes'=>'67,75,68,69,70,71',	

		'publications'=>35,

		'publicationsarchive'=>51,

		'ActivitiesArchive'=>76,

		'contact'=>14,

		'imprint'=>15,	

		'address-box'=>3,

	),

);





$GLOBALS['CONFIG']=$CONFIG;





$GLOBALS['ADVERT_POSITIONS']=array(0=>'',1=>'Център',2=>'Лява колона 1',3=>'Лява колона 2',4=>'Дясна колона 1',5=>'Дясна колона 2');





$GLOBALS['AdsPositionsSize'] = array(

	1=>array(100,100),

);



$GLOBALS['AdsTypes']=array(

	1=>"Картинка",

	2=>"Флаш",

);



$GLOBALS['MANAGED_FILE_DIR']=dirname(__FILE__)."/../files/mf/";

$GLOBALS['MANAGED_FILE_DIR_IMG']="/files/mf/";





/* Just for BE  */

$GLOBALS['YES_NO']=array(

		0=>"НЕ",

		1=>"ДА"

);



$GLOBALS['VALID_IMAGE_EXTENSIONS']=array(

	'.jpg',

	'.png',

	'.gif',

);




$GLOBALS['months'] = array(
	'en'=>array(
		1=>'January',
		2=>'February',
		3=>'March',
		4=>'April',
		5=>'May',
		6=>'June',
		7=>'July',
		8=>'August',
		9=>'September',
		10=>'October',
		11=>'November',
		12=>'Decembe',
	),
	'bg'=>array(
		1=>'Януари',
		2=>'Февруари',
		3=>'Март',
		4=>'Април',
		5=>'Май',
		6=>'Юни',
		7=>'Юли',
		8=>'Август',
		9=>'Септември',
		10=>'Октомври',
		11=>'Ноември',
		12=>'Декември',	
	),
	'de'=>array(
		1=>'Januar',
		2=>'Februar',
		3=>'März',
		4=>'April',
		5=>'Mai',
		6=>'Juni',
		7=>'Juli',
		8=>'August',
		9=>'September',
		10=>'Oktober',
		11=>'November',
		12=>'Dezember',
	),
);




define('JS_VALIDATION',true);

define('JS_ERROR_COLOR','#FFDBA6');



define("RIGHTS_DELETE",1);

define("RIGHTS_INSERT",2);

define("RIGHTS_UPDATE",4);

define("RIGHTS_READ",8);

?>