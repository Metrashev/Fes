<?php
require_once(dirname(__FILE__)."/conf.php");

$tmp_name=".tmp";

$img=$_GET['img'];
if(empty($img)) {
	die("Invalid Image");
}

$dir=dirname(__FILE__)."/../../../..";



function isValidImage($img) {
	list($width, $height,$info,$attr) = getimagesize($img);
	if($info==-1) {
		return "";
	}
	switch ($info) {
		case IMG_GIF: {
			return '.gif';
		}
		case IMG_JPG:
		case IMG_JPEG: {
			return '.jpg';
		}
		case 3:
		case IMG_PNG: {
			return '.png';
		}
	}
	return "";
}

function getLink($cmd="") {
	if($cmd=="") {
		return "#";
	}
	$bkp=urlencode($_GET['bkp']);
	$img=urlencode($_GET['img']);
	$r=mt_rand();	
	return <<<EOD
	?img={$img}&bkp={$bkp}&cmd={$cmd}&amp;r={$r}
EOD;
}

list($width, $height,$info,$attr) = getimagesize($dir.$img);
if($info==-1) {
	die("Invalid Image");
}

$img_type=isValidImage($dir.$img);

if(!$img_type) {
	die("Invalid Image");
}

if($_GET['cmd']=="7") {
	unset($_GET['cmd']);
}

if($_SERVER['REQUEST_METHOD']=='GET'&&!isset($_GET['cmd'])) {
	copy($dir.$img,$dir.$GLOBALS['FMAN_IMAGES_URL_PATH']."/{$tmp_name}");
	chmod($dir.$GLOBALS['FMAN_IMAGES_URL_PATH']."/{$tmp_name}",0777);
}

$work_img=$GLOBALS['FMAN_IMAGES_URL_PATH']."/{$tmp_name}";

$func_to_save=array(".gif"=>"imagegif",".png"=>"imagepng",".jpg"=>"imagejpeg");

list($width, $height,$info,$attr) = getimagesize($dir.$GLOBALS['FMAN_IMAGES_URL_PATH']."/{$tmp_name}");

if($_SERVER['REQUEST_METHOD']=="GET") {
	$dst="";
	switch ((int)$_GET['cmd']) {
		case 4: {
			$src=imagecreatefromstring(file_get_contents($dir.$GLOBALS['FMAN_IMAGES_URL_PATH']."/{$tmp_name}"));
			$dst=imagerotate($src,270,0);			
			break;
		}
		case 3: {
			$src=imagecreatefromstring(file_get_contents($dir.$GLOBALS['FMAN_IMAGES_URL_PATH']."/{$tmp_name}"));
			$dst=imagecreatetruecolor($width,$height);
			for($x=0;$x<$width;$x++) {
				for($y=0;$y<$height;$y++) {
					imagesetpixel($dst,$x,$y,imagecolorat($src,$width-$x,$y));
				}
			}
			break;
		}
		case 5: {
			$src=imagecreatefromstring(file_get_contents($dir.$GLOBALS['FMAN_IMAGES_URL_PATH']."/{$tmp_name}"));
			$dst=imagecreatetruecolor($width,$height);
			for($x=0;$x<$width;$x++) {
				for($y=0;$y<$height;$y++) {
					imagesetpixel($dst,$x,$y,imagecolorat($src,$x,$height-$y));
				}
			}
			break;
		}
	}
	
	if($dst&&isset($func_to_save[$img_type])) {
		$t=$func_to_save[$img_type];
		$t($dst,$dir.$GLOBALS['FMAN_IMAGES_URL_PATH']."/{$tmp_name}");
	}
	
	list($width, $height,$info,$attr) = getimagesize($dir.$GLOBALS['FMAN_IMAGES_URL_PATH']."/{$tmp_name}");
}



if (isset($_POST['btApply'])) {
	//$src=imagecreatefromstring(file_get_contents($dir.$img));
	$src=imagecreatefromstring(file_get_contents($dir.$GLOBALS['FMAN_IMAGES_URL_PATH']."/{$tmp_name}"));
	switch ((int)$_GET['cmd']) {
		case 1: {	//crop
			$dst=imagecreatetruecolor((int)$_POST['width'],(int)$_POST['height']);
			imagecopy($dst,$src,0,0,(int)$_POST['x1'],(int)$_POST['y1'],(int)$_POST['width'],(int)$_POST['height']);
			break;
		}
		case 2: {	//resize
			$dst=imagecreatetruecolor((int)$_POST['r_width'],(int)$_POST['r_height']);
			imagecopyresized($dst,$src,0,0,0,0,(int)$_POST['r_width'],(int)$_POST['r_height'],$width,$height);
			break;
		}		
	}
	
	if(isset($func_to_save[$img_type])) {
		$t=$func_to_save[$img_type];
			$t($dst,$dir.$GLOBALS['FMAN_IMAGES_URL_PATH']."/{$tmp_name}");		
	}
	
	header("Location: ?img=".urlencode($_GET['img'])."&bkp=".urlencode($_GET['bkp'])."&cmd=0&r=".mt_rand());
	exit;
	list($width, $height,$info,$attr) = getimagesize($dir.$GLOBALS['FMAN_IMAGES_URL_PATH']."/{$tmp_name}");
//	header("Location: ".urldecode($_GET['bkp']));
//	exit;
}

if(isset($_POST['btSave'])) {
	copy($dir.$GLOBALS['FMAN_IMAGES_URL_PATH']."/{$tmp_name}",$dir.$img);
	chmod($dir.$img,0777);
	header("Location: ".urldecode($_GET['bkp']));
	exit;

}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<meta http-equiv="Content-Language" content="en-us" />
	<title></title>
	<script src="js/prototype.js" type="text/javascript"></script>	
 	<script src="js/scriptaculous.js?load=builder,dragdrop" type="text/javascript"></script>
	<script src="js/cropper.js" type="text/javascript"></script>
	
	
	<script type="text/javascript" charset="utf-8">
	
<? if($_GET['cmd']==2) { //resize ?>

var _img_w=<?=$width;?>;
var _img_h=<?=$height;?>;

function resizeImg(byWidth) {
	var width=parseInt(document.getElementById('r_width').value);
	var height=parseInt(document.getElementById('r_height').value);
	var ch=document.getElementById('proportional').checked;
	if(isNaN(width)||isNaN(height)) {
		return;
	}
	var w=width;
	var h=height;
	
	if (ch) {
		if (byWidth) {
			w=width;
			h = Math.round(_img_w * (width / _img_w));
		}
		else {
			h=height;
			w = Math.round(_img_w * (height / _img_h));
		}
	}
	var i=document.getElementById('testImage');
	
	i.style.height=h+"px";
	i.style.width=w+"px";
	
	document.getElementById('r_width').value=w;
	document.getElementById('r_height').value=h;
}
<? } ?>
<? if($_GET['cmd']==1) { ?>		
var croper=null;
		function onEndCrop( coords, dimensions ) {
			$( 'x1' ).value = coords.x1;
			$( 'y1' ).value = coords.y1;
			$( 'x2' ).value = coords.x2;
			$( 'y2' ).value = coords.y2;
			$( 'width' ).value = dimensions.width;
			$( 'height' ).value = dimensions.height;
		}
		
		// example with a preview of crop results, must have minimumm dimensions
		Event.observe( 
			window, 
			'load', 
			function() { 
				//croper=new Cropper.ImgWithPreview( 
				croper=new Cropper.Img( 
					'testImage',
					{ 
					//	minWidth: 220, 
					//	minHeight: 220,
					//	ratioDim: { x: 20, y: 20 },
						displayOnInit: true, 
						onEndCrop: onEndCrop
					//	previewWrap: 'previewArea'
					} 
				) 
			} 
		);		
		
		/*
		if( typeof(dump) != 'function' ) {
			Debug.init(true, '/');
			
			function dump( msg ) {
				// Debug.raise( msg );
			};
		} else dump( '---------------------------------------\n' );
		*/
		
		function setCropPos() {
			var _1=['x1','y1','x2','y2'];
			var _1e=[];
			var k;
			for(k=0;k<_1.length;k++) {
				var t=parseInt(document.getElementById(_1[k]).value);
				if(isNaN(t)) {
					alert("Invalid value: "+document.getElementById(_1[k]).value);
					return;
				}
				_1e.push(t);
			}
			if(_1e[0]<0||_1e[0]>=_1e[2]||
				_1e[1]<0||_1e[1]>=_1e[3]||
				_1e[2]>=<?=$width;?>||_1e[3]>=<?=$height;?>) {
				onEndCrop(croper.areaCoords,{width:croper.calcW(),height:croper.calcH()});
				return;
			}
			croper.setAreaCoords({x1:_1e[0],y1:_1e[1],x2:_1e[2],y2:_1e[3]},true,false);
			croper.drawArea();
			//croper.moveArea([0, 0]);
		}
		
<? } ?>		
		
	</script>
	<link rel="stylesheet" type="text/css" href="debug.css" media="all" />
	<style type="text/css">

		img {
			border:none;
		}
		
		label {
			font-size:12px;
		}
	
		#testWrap {
			width: <?=$width;?>px;
			float: left;
			margin: 20px 0 0 50px; /* Just while testing, to make sure we return the correct positions for the image & not the window */
		}
		
		#previewArea {
			margin: 20px; 0 0 20px;
			float: left;
		}
		
		#results {
			clear: both;
		}
		
		.header {
			padding:10px;
			border-top:1px solid #cccccc;
			border-bottom:1px solid #cccccc;
			height:25px;
		}
		
		.header a {
			display:block;
			float:left;
			/*width:100px;*/
			padding:5px;
			color:#cc0000;
			text-decoration:none;
		}
		
		.header a:hover {
			background:#F0F8FF;
		/*	border:2px outset #ffffff;			*/
		}
		
		.input {
			border:1px solid #cccccc;
			font-size:10px;
		}
				
	</style>
</head>



<body>	
<form method="post">
	<div class="header">
		<a href="<?=getLink(1);?>"><img src="images/crop.gif" align="crop" /> Crop</a>
		<a href="<?=getLink(2);?>"><img src="images/crop.gif" align="resize" /> Resize</a>
		<a href="<?=getLink(3);?>"><img src="images/flip.gif" align="flip" /> Flip</a>
		<a href="<?=getLink(5);?>"><img src="images/flip_v.gif" align="flip vertical" /> Flip vertical</a>
		<a href="<?=getLink(4);?>"><img src="images/rotate.gif" align="rotate" /> Rotate</a> 
		<a href="#" onclick="return false;"> | </a>
		<a href="<?=getLink(7);?>">&raquo; Revert</a>
		
	</div>
	<div class="buttons"><input type="submit" value="Save" onclick="return window.confirm('Сигурни ли сте, че желаете да замените <?=$img;?> с новополучената?');" name="btSave" />	
	<input type="button" onclick="self.location='<?=htmlspecialchars($_GET['bkp']);?>'" value="Back"  /></div>
	<? if($_GET['cmd']==2) { //resize ?>
		<div class="header">
			<input size="4" class="input" type="text" value="<?=$width;?>" name="r_width" id="r_width" onchange="resizeImg(1);" />x
			<input size="4" class="input" type="text" value="<?=$height;?>" name="r_height" id="r_height" onchange="resizeImg(0);" />&nbsp;
			<input type="checkbox" id="proportional" /> <label for="proportional">Proportional</label>
			<input type="submit" name="btApply" value="Apply" />
		</div>
	<? } ?>
	
	<? if($_GET['cmd']==1) { //crop ?>
		<div class="header">
			left: <input size="4" class="input" type="text" onchange="setCropPos()" name="x1" id="x1" />
			top: <input  size="4" onchange="setCropPos()"  class="input" type="text" name="y1" id="y1" />
			right: <input size="4" onchange="setCropPos()"  class="input" type="text" onchange="setCropPos()"  name="x2" id="x2" />
			bottom: <input size="4"  class="input" type="text" onchange="setCropPos()"  name="y2" id="y2" />
			width: <input size="6" readonly="readonly"  class="input" type="text" onchange="setCropPos()" name="width" id="width" />
			height: <input size="6" readonly="readonly" class="input" type="text" onchange="setCropPos()" name="height" id="height" />
			<input type="submit" name="btApply" value="Apply" />
		</div>
	<? } ?>
	
	
	<div id="testWrap">
		<img src="getImg.php?r=<?=mt_rand();?>" alt="" id="testImage" width="<?=$width;?>" height="<?=$height;?>" />
	</div>
	
	<div id="previewArea"></div>
	<div style="clear:both;">&nbsp;</div>

	
</form>
</body>
</html>