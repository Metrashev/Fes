<html>
<head>
<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8">
<script src='/UT.js' language="javascript"></script>


<!--  MENU SCRIPTOVE   -->
<style type="text/css">

a, div, td,span {
	font-family : verdana, arial;
}

.menuContainer {
	border:1px solid #999999;
	background:#ffffff;
	padding:1px;
}

body,html {
	height:100%;
}


body,td,p {
	font-size:10px;
	color: #aaa;
}

a {

	color:#000000;	
	font-size:10px;
	font-weight:normal;
	text-decoration:none;
}

.selected {
	color:#D90F0F;
}

.a_level1 {
	font-weight:bold;
}

.a_level1_sel {
	color:#D90F0F;
	font-weight:bold;
}

.a_level2_sel {
	color:#D90F0F;
}

.subContainer {
	padding:5px;
	background:#e6e6e6;
	vertical-align:top;
	width:100%;
}

.subContainer td {
	vertical-align:top;
}

.hr {
	border-top:1px solid #ffffff;
	border-bottom:1px solid #999999;
	margin:5px 0px;
}

.menuOut, img {cursor:pointer; }
.menuOver, img {cursor:pointer; }
.submenu {padding-left:15px;}

</style>

<script>

last_selected=null;

function setMenuColor(obj) {
		
	if(!obj) {
		return;
	}
	
	if(obj.className=="") {
		return;
	}

	if(obj.className.indexOf("a_level1")>-1) {
		obj.className=obj.className.indexOf("_sel")>0?"a_level1":"a_level1_sel";
		
		return;
	}
	if(obj.className.indexOf("a_level2")>-1) {
		obj.className=obj.className.indexOf("_sel")>0?"a_level2":"a_level2_sel";
		return;
	}
	
}

function setSelected(obj) {
	setMenuColor(last_selected);
	setMenuColor(obj);
	last_selected=obj;
}

function SwitchMenu(obj){   
    var el = document.getElementById(obj);    
    var ar = document.getElementById("cont").getElementsByTagName("DIV");
        if(el.style.display == "none"){
    //        for (var i=0; i<ar.length; i++){
    //            ar[i].style.display = "none";
    //        }
            el.style.display = "block";
        }else{
            el.style.display = "none";
      }     
}

function OpenMenu(obj) {
    if(document.getElementById){
        var el = document.getElementById(obj);    
        el.style.display = "block";
    }
}

function OpenImg(img,sub_menu) {
    var obj=document.getElementById(img);
    obj.src="/be/i/design/minus1.png";
}

function ChangeClass(menu, newClass) { 
	return;
     if (document.getElementById) { 
         document.getElementById(menu).className = newClass;
     } 
} 

function switchImg(img,sub_menu) {
    var elm=document.getElementById(sub_menu);
    var obj=document.getElementById(img);
    if(elm.style.display=='block') {
    	
    	if(obj.src.indexOf("plus1.png")>-1) {
        	obj.src="/be/i/design/minus1.png";
    	}
    	else {
    		obj.src="/be/i/design/minus2.png";
    	}
    }
    else {
    	if(obj.src.indexOf("minus1.png")>-1) {
        	obj.src="/be/i/design/plus1.png";
    	}
    	else {
    		obj.src="/be/i/design/plus2.png";
    	}
    }
}

document.onselectstart = new Function("return false");
</script>

<!--  END MENU -->
</head>
<body style="padding:0px;margin:5px;">
<div class="menuContainer">
<table class="subContainer" style="height:95%" height="95%" cellpadding="0" cellspacing="0">
<tr><td>

<div><a onclick="setSelected(this);" class="a_level1" style="color:#D90F0F;" href='categories/' target='main'>Menu</a></div>
<div class="hr"></div>

<div style="display:none;""><img id="imgadmin" src="/be/i/design/plus1.png" onclick="SwitchMenu('subadmin');
switchImg('imgadmin','subadmin');" />
<a id="menuadmin" href="#" class="a_level1" 
onclick="setSelected(this);OpenMenu('subadmin');OpenImg('imgadmin','subadmin');return false;" 
onmouseover="ChangeClass('menuadmin','menuOver')" onmouseout="ChangeClass('menuadmin','menuOut')">Custom menu</a>
<br />
 <div class="submenu" id="subadmin" style="display:none;">
 	<img src='/be/i/z.gif' width='12' /><a onclick="setSelected(this);" class="a_level2" href='/be/adverts/' target='main'>Adverts</a><br />
 	<img src='/be/i/z.gif' width='12' /><a onclick="setSelected(this);" class="a_level2" href='/be/polls/' target='main'>Polls</a><br />
 	<img src='/be/i/z.gif' width='12' /><a onclick="setSelected(this);" class="a_level2" href='/be/gallery_head/' target='main'>Галерии</a><br />
 </div>
</div>


<div>
 <a onclick="setSelected(this);" class="a_level1" href='boxes/edit.php?id=1' target='main'>Адресна Кутия - bg</a><br>
 <a onclick="setSelected(this);" class="a_level1" href='boxes/edit.php?id=2' target='main'>Адресна Кутия - en</a><br>
 <a onclick="setSelected(this);" class="a_level1" href='boxes/edit.php?id=3' target='main'>Адресна Кутия - de</a><br>
 <a onclick="setSelected(this);" class="a_level1" href='gallery/?cid=1&page_id=1' target='main'>Водещи Снимки</a>
</div>
 
<div class="hr"></div>
<!--  START -->
<div id="cont" style="white-space:nowrap">
<?php

function getHref($v) {
    
    $sp=str_repeat("&nbsp;", $v['level']?$v['level']-1:0);
    $value=$v['value'];
    
    
    $class = $GLOBALS['CONFIG']['FEPageTypes'][$v['type_id']]['class'];
    $dir = $GLOBALS['CONFIG'][$class]['be']['menu'];
    if(!empty($dir)) 
    {
        if(is_array($dir)) {    //zaradi custompages
        	
            $dir=$dir[$v['template_id']];
            $dir=str_replace("_#CID#_",$v['id'],$dir);
        }
        else {
            $dir .= $v['id'];   //dobavqme cid otzad
        }
    }

    return $dir;
}

function drawNode($node,$table,$as_child=false) {
        $db=getdb();
        $children=$db->getassoc("select id, value, level,type_id,template_id, visible FROM {$table} where pid='{$node['id']}'  order by l");
        $str='';
        $img12=$as_child?2:1;
        if(!empty($children)) {
            $href=getHref($node);

			$node['id'] .=  $table;			
            $str.=<<<EOD
<img id="img{$node['id']}" src="/be/i/design/plus{$img12}.png" onclick="SwitchMenu('sub{$node['id']}');switchImg('img{$node['id']}','sub{$node['id']}');" />
EOD;
if(empty($href))
$str.=<<<EOD
    <a href="#" id="menu{$node['id']}" class="a_level{$img12}" onclick="setSelected(this);SwitchMenu('sub{$node['id']}');switchImg('img{$node['id']}','sub{$node['id']}');return false;" onmouseover="ChangeClass('menu{$node['id']}','menuOver')" onmouseout="ChangeClass('menu{$node['id']}','menuOut')">{$node['value']}</a><br />
EOD;
else {
    //$href=htmlentities($href);
    $str.=<<<EOD
    <a id="menu{$node['id']}" href="{$href}" target="main" class="a_level{$img12}" onclick="setSelected(this);OpenMenu('sub{$node['id']}');OpenImg('img{$node['id']}','sub{$node['id']}');" onmouseover="ChangeClass('menu{$node['id']}','menuOver')" onmouseout="ChangeClass('menu{$node['id']}','menuOut')">{$node['value']}</a><br />    
EOD;
}
$str.=<<<EOD
<div class="submenu" id="sub{$node['id']}" style="display:none;">
EOD;
            foreach($children as $k=>$v) {
                $str.=drawNode($v,$table,true);
            }
            $str.="</div>";
        }
        else {
            $href=  getHref($node);
            if(!empty($href)) {
                $str.="<img src='/be/i/z.gif' width='12' /><a class='a_level{$img12}' onclick='setSelected(this);' href='".getHref($node)."' target='main'>{$node['value']}</a><br/> ";
            }
            else {
                $str.="<img src='/be/i/z.gif' width='12' />".$node['value']."<br />";
            }
        }
        return $str;
    }

require_once(dirname(__FILE__). '/../lib/db.php');
require_once(dirname(__FILE__). '/../config/config.php');
	$db=getdb();
	
    $menu=$db->getassoc("SELECT id, value, level, type_id,template_id, visible FROM categories where level=1 ORDER BY l");
	foreach($menu as $k=>$v) {
        echo drawNode($v,"categories",false);
    }
    
    //echo $str;

?>
</div>
<div class="hr"></div>
<div><a class="a_level1" href='/be/' target='_top'>Logout</a></div>
<div class="hr"></div>
<!--  END -->
</td></tr></table>
</div>
</body>
</html>