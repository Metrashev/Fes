<?php

$con=array(
'controls'=>array(
     'value'=>
        array(
            'control'=>array('Label'=>"",'name'=>'in_data[value]','tagName'=>'Input','bound_field'=>'value','userFunc'=>'','FormatString'=>'',),
            'write_data'=>array('type'=>DATA_VARCHAR,'required'=>true),
        ),
    /* 'path'=>
        array(
            'control'=>array('Label'=>CLanguage::translate(DEFAULT_LANGUAGE,"Path"),'name'=>'in_data[path]','tagName'=>'Input','bound_field'=>'path','userFunc'=>'','FormatString'=>'',),
            'write_data'=>array('type'=>DATA_VARCHAR,),
        ),  */
     'visible'=>
        array(
            'control'=>array('Label'=>"",'name'=>'in_data[visible]','tagName'=>'CheckBox','states'=>array('on'=>1,'off'=>0), 'bound_field'=>'visible','userFunc'=>'','FormatString'=>'','attributes'=>array('type'=>'checkbox')),
            'write_data'=>array('type'=>DATA_TINYINT,),
        ),
     'is_crumb_visible'=>
        array(
            'control'=>array('Label'=>"",'name'=>'in_data[is_crumb_visible]','tagName'=>'CheckBox','states'=>array('on'=>1,'off'=>0), 'bound_field'=>'is_crumb_visible','userFunc'=>'','FormatString'=>'','attributes'=>array('type'=>'checkbox')),
            'write_data'=>array('type'=>DATA_TINYINT,),
        ),
     'use_in_search'=>
        array(
            'control'=>array('Label'=>"",'name'=>'in_data[use_in_search]','tagName'=>'CheckBox','states'=>array('on'=>1,'off'=>0), 'bound_field'=>'use_in_search','userFunc'=>'','FormatString'=>'','attributes'=>array('type'=>'checkbox')),
            'write_data'=>array('type'=>DATA_TINYINT,),
        ),
    'language_id'=>
        array(
            'control'=>array('Label'=>"",'name'=>'in_data[language_id]','tagName'=>'Select','bound_field'=>'language_id','userFunc'=>'','FormatString'=>'','autoload'=>array('type'=>'arrayname','value'=>array('DataSource'=>$GLOBALS['CONFIG']['SiteLanguages']))),
            'write_data'=>array('type'=>DATA_VARCHAR,),
        ),
     'type_id'=>
        array(
            'control'=>array('Label'=>"",'name'=>'in_data[type_id]','tagName'=>'Select','bound_field'=>'type_id','userFunc'=>'','FormatString'=>'','autoload'=>array('type'=>'arrayname','value'=>array('DataSource'=>$GLOBALS['CONFIG']['FEPageTypes'],'DataTextField'=>'name')),'attributes'=>array('onchange'=>'getForm(this).submit();')),
            'write_data'=>array('type'=>DATA_TINYINT,),
        ),
    'skin_id'=>
        array(
            'control'=>array('Label'=>"",'name'=>'in_data[skin_id]','tagName'=>'Select','bound_field'=>'skin_id','userFunc'=>'','FormatString'=>'','autoload'=>array('type'=>'arrayname','value'=>array('DataSource'=>$skins,'DataTextField'=>'name','more'=>array('DataTextField'=>'name')))),
            'write_data'=>array('type'=>DATA_TINYINT,),
        ),
    'template_id'=>
        array(
            'control'=>array('Label'=>"",'name'=>'in_data[template_id]','tagName'=>'Select','bound_field'=>'template_id','userFunc'=>'','FormatString'=>'','autoload'=>array('type'=>'arrayname','value'=>array('DataSource'=>$templates,'DataTextField'=>'name'))),
            'write_data'=>array('type'=>DATA_TINYINT,),
        ),
    'img'=>array(   
     'control'=>array("Label"=>"img","name"=>"in_data[img]","isHTML"=>false,"tagName"=>"ManagedImage","bound_field"=>"img","userFunc"=>"","FormatString"=>"",
    	"parameters"=>array(
    		"table"=>"categories",
    		"field"=>"img",
    		"id"=>$_GET['id'],
    		"dir"=>$GLOBALS['MANAGED_FILE_DIR'],
    		"view_dir"=>$GLOBALS['MANAGED_FILE_DIR_IMG'],
    		'resize'=>true,'overwrite'=>false,
    		'sizes'=>array(
				'1'=>array(570,180,"(570x180)"),
			)
		)
	),
    'write_data'=>array("type"=>DATA_VARCHAR,"required"=>false,),    'search_data'=>array("search_name"=>"categories.img","matchAllValue"=>"","cond"=>"like"    ),
    ),
   
    
),
    'template'=>array('dir'=>dirname(__FILE__).'/edit.tpl'),
);

?>