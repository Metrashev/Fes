<?php

/*

class CMenuItem{
	public $name;
	public $href;
	public $target='';
	public $selected=false;
	public $visible=true;
	
}
*/

class CFESkinPageBase {

	public $styles = array();
	public $css = array();
	public $js = array();
	public $scripts = array();
	
	
	public $CrumbsPath=array();
	public $MenuItems=array();
	public $PageTitle=array();
		
	public $data = array();
	
	private $fc;
	
	
	function __construct(){
		$this->fc = FrontControler::getInstance(1);
		
		$this->CrumbsPath = $this->fc->nodesPath;
		$this->MenuItems = $this->fc->tree->expandedMenu;
		
		foreach($this->CrumbsPath as $node){
			
			
	    $this->PageTitle[] = $node['value'];
	  }
	}

	static function isIE4(){
		return (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 5')!==false || strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 4')!==false);
	}




	function getPageTitle(){
	  $Nodes = $this->PageTitle;
	  $Nodes[0] = $GLOBALS['CONFIG']['SiteName'][LNG_CURRENT];
	  return implode(" &gt; ", $Nodes);
	}

	static function getBackLink(){
		return 'JavaScript:history.go(-'.CBackLinkCounter::getCnt().')';
	}

	function getCrumbsPathHtml(){
		$res = array();
		foreach($this->CrumbsPath	as $item){
			if($item['is_crumb_visible']!="1") continue;
			$res[] = "<a href=\"{$item['href']}\"{$node['target']}>{$item['value']}</a>";
		}		
		return implode(' &gt;&gt; ', $res);
	}
	

	
	function getData(){
		$data = $this->data;

		$data['PageTitle'] = self::getPageTitle();
		$data['BackLinkHref'] = self::getBackLink();
		$data['PrintLinkHref'] = FrontControler::getPrintLink();
		$data['HidePrintLink'] = $GLOBALS['HidePrintLink'];
		$data['isIE4'] = self::isIE4();

		$data['Header'] = '';
		if(!empty($this->styles)){
			$data['Header'] .= '<style type="text/css">'.implode("\n", $this->styles).'</style>'."\n";
		}

		if(!empty($this->scripts)){
			$data['Header'] .= '<script type="text/javascript">'.implode("\n", $this->scripts).'</script>'."\n";
		}

		if(!empty($this->css)){
			$data['Header'] .= '<link rel="stylesheet" type="text/css"  href="'.implode('" />'."\n".'<link rel="stylesheet" type="text/css"  href="', $this->css).'" />'."\n";
		}

		if(!empty($this->js)){
			$data['Header'] .= '<script type="text/javascript" src="'.implode('"></script>'."\n".'<script type="text/javascript" src="', $this->js).'"></script>'."\n";
		}


		$data['CrumbsPath'] = $this->getCrumbsPathHtml();
		
		return $data;
	}

}

?>