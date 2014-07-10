<?php

require_once(dirname(__FILE__).'/ABOBase.php');
require_once(dirname(__FILE__).'/CFESkinPageBase.php');
require_once(dirname(__FILE__).'/ads.php');

class CFESession {

	static public function StartConditional(){
		self::isStarted();
	}

	static public function isStarted(){
		if(isset($_COOKIE[session_name()])){
			if(!session_id())  session_start();
			return true;
		} else {
			return false;
		}
	}

	static public function start(){
		if(!self::isStarted()){
			session_start();
			$_COOKIE[session_name()] = session_id();
		}
	}

	static public function destroy(){
		session_destroy();
		$tmp = session_get_cookie_params();
		setcookie(session_name(), false, $tmp['lifetime'], $tmp['path'], $tmp['domain'], $tmp['secure']);
		unset($_COOKIE[session_name()]);
		$_SESSION=array();
		unset($_SESSION);
	}
}






class EPageNotFound extends Exception {
	
	function __construct($message='', $code=0){
		
		header("HTTP/1.0 404 Not Found", true, 404);
		readfile('Error404.html');
		die();
	}

}

class CBackLinkCounter {

  static function getCnt(){
    return (int)$_POST['HistoryBackCnt'] + 1;
  }

  static function getInputHidden(){
    $cnt = self::getCnt();
    return "<input name=\"HistoryBackCnt\" type=\"hidden\" value=\"$cnt\" />";
  }
}

class CFETree {

  private $TableName='';
  
  private $pids=array();

  protected $db;
  /* @var $db CDB*/
  
  public $currentNode;
  public $nodesPath = array();
  public $expandedMenu;
  
  public $href='/?cid=';

  function __construct($tableName) {
  	$this->db=getdb();
  	$this->TableName=$tableName;
  }
  
  function preloadTree($cid,$visible=1){

		$pids = Array();

		$cid = (int)$cid;

		$SQL="SELECT id,l, level FROM {$this->TableName} WHERE id=$cid";
		$selected = $this->db->getRow($SQL);

		if(is_array($selected)&&count($selected)>0)
		{
			$sl = $selected['l'];
			$slevel = $selected['level'];

			$pids[$selected['id']] = $selected['id'];
			$SQL = "SELECT id FROM {$this->TableName} WHERE l<$sl AND (l+weight)>=$sl";
			$res=$this->db->getAll($SQL);
			if(is_array($res)&&count($res)>0) {

				foreach ($res as $value) {
					$pids[$value['id']] = $value['id'];
				}
			}
		} else {
			return false;
		}
		
		$this->pids = $pids;

    //$SQL = "SELECT * FROM {$this->TableName} WHERE visible=1 AND level>1 AND pid IN (".implode(', ', $pids).") ORDER BY l";
	if($visible) {
    $SQL = "SELECT * FROM {$this->TableName} WHERE visible={$visible}  AND pid IN (".implode(', ', $pids).") ORDER BY l";
	}
	else {
    $SQL = "SELECT * FROM {$this->TableName} WHERE pid IN (".implode(', ', $pids).") ORDER BY l";
	}
    $cats = $this->db->getAll($SQL);
		$this->setNodesData($cats);
		foreach($cats as &$cat){

			if($cat['selected']){
				$this->nodesPath[] = $cat;
			}
			if($cat['id']==$cid){
				$this->currentNode = $cat;
			}
		}
		
		$this->expandedMenu = $cats;
  	return true;
  }
  
  function setNodesData(&$cats){
		foreach($cats as &$cat){

		  $cat['selected'] = $this->pids[$cat['id']]?true:false;
			$cat['target'] ='';

			if(!empty($cat['path'])) {
				$href = $cat['path'];
			} else
			if ($cat['type_id']==3) {
			  $tmp = unserialize($cat['php_data']);
			  $tmp = $tmp['parameters'];
			  $href = $tmp['url'];
			  $cat['target'] = "target='{$tmp['target']}'";
			} else {
			  $href = $this->href.$cat['id'];
			}

			$cat['href'] = $href;		
		}
  }

  function getNodeById($id, $fields='*') {
    $id = (int)$id;
    $SQL = "SELECT {$fields} FROM {$this->TableName} WHERE id = $id";
    return $this->db->getRow($SQL);
  }

  function getNodePath($id, $fields='*'){
    $id = (int)$id;
    $result = array();

		$SQL="SELECT id, l FROM {$this->TableName} WHERE id=$id";
		$row=$this->db->getRow($SQL);
		if(is_array($row)&&count($row)>0)
		{
			$l = $row["l"];
			$id = $row["id"];
			$SQL = "SELECT $fields FROM {$this->TableName} WHERE l<=$l and (l+weight)>={$l} AND level>0 ORDER BY l";
			$result=$this->db->getAll($SQL);
		}

		return $result;
	}


	function getExpandedMenu($cid){
		$SQL="SELECT id,l, weight FROM {$this->TableName} WHERE id=$cid";
		$selected = $this->db->getRow($SQL);

		if(empty($selected)) return ;
		
		$sl = $selected['l'];
		$sr = $sl +  $selected['weight'];
		$SQL = "SELECT * FROM {$this->TableName} WHERE l BETWEEN $sl AND $sr ORDER BY l";
		$res=$this->db->getAll($SQL);
		$this->setNodesData($res);
		return $res;
	}
	
	static private function _convertFlat2Tree($items, &$i){
		$result = array();
		$level = $items[$i]['level'];
		for($i; $i<count($items); $i++){
			$item = $items[$i];
			if($item['level']<$level) {
				$i--;
				break;
			}
			
			if($items[$i+1]['level']>$level){
				$i++;
				$item['childs'] = self::_convertFlat2Tree($items, $i);
			}
			$result[] = $item;
			
		}
		return $result;
	}
	
	static function convertFlat2Tree($items){
		$i = 0;
		return self::_convertFlat2Tree($items, $i);
	}
	
}

class FrontControler {

  const CIDVarName='cid';
  const PrintVarName='print';

  static $me=null;

  public $nodesPath = null;

  public $node;

  public $printMode = false;

  /**
   * Keeps instance of CFETree
   *
   * @var CFETree
   */
  public $tree;

  private $language;

  private $isEror404=false;

	/**
	 * Enter description here...
	 *
	 * @param int $defaultCID
	 * @return FrontControler
	 */
	static function getInstance($defaultCID) {
	  //static $me=null;
	  if(!self::$me)
	    self::$me = new self($defaultCID);

		return self::$me;
	}

	static function getEroro404Instance($Eroro404CID){
		$_GET['cid'] = $Eroro404CID;
		$_REQUEST['cid'] = $Eroro404CID;

		self::$me = new self(1);
		return self::$me;
	}

	static function getPrintLink(){
	  $a = $_GET;
	  $a[self::PrintVarName] = 'on';
	  return '?'.http_build_query($a);
	}


  private function __construct($defaultCid){

    $cid = (int)$_GET[self::CIDVarName];
    if($cid==0) $cid = $defaultCid;

    $this->language = DEFAULT_LANGUAGE;

    $this->printMode = isset($_GET[self::PrintVarName]);

    $this->tree = new CFETree('categories');
    
    $this->tree->preloadTree($cid,0);
    
    $this->node = $this->tree->currentNode;
    


    if(!$this->node){
    	$this->isEror404 = true;
      $this->node['id'] = $defaultCid;
      define('LNG_CURRENT', DEFAULT_LANGUAGE);
       $this->nodesPath = array();

    } else {
	    $this->language = $this->node['language_id'];

	    define('LNG_CURRENT', $this->language);

	    $this->node['php_data'] = unserialize($this->node['php_data']);

	    //$this->nodesPath = $this->tree->getNodePath($this->node['id'], 'id, value, skin_id');
	    $this->nodesPath = $this->tree->nodesPath;
    }

  }

  function run(){

  	if($this->isEror404)
  		throw new EPageNotFound();

    $conf = $GLOBALS['CONFIG']['FEPageTypes'][$this->node['type_id']];

    if($conf['file'])
      require_once($conf['file']);

    $GLOBALS['FESkinPage'] = $skinClass = new CFESkinPage();
    

    $bodyClass = $conf['class'];

    $bodyClass = new $bodyClass($this->node, $_REQUEST);

    
    $skinClass->data['body'] = $bodyClass->getBodyHTML();
    $data = $skinClass->getData();

    if($this->printMode){
      $skin = $GLOBALS['CONFIG']['PrintSkin'];
    } else {
       $skin = $this->getSkinFile($this->nodesPath);
    }

    echo ob_include($skin, $data);

//include($skin);
  }


  function getLanguage(){
    return $this->language;
  }

  private function getSkinFile($nodesPath){
    $skin = "";
    for($i=count($nodesPath)-1; $i>=0; $i--)
    {
      $row = $nodesPath[$i];	
      if($row['skin_id']){
        $skinId = $row['skin_id'];
        break;
      }
    }

    $skin = $GLOBALS['CONFIG']['Skins'][$skinId];
    return $skin['file'];
  }

}


interface IFEPage {
  function __construct($node, $request);
  function getBodyHTML();
}

class CFERedirectPage {
  function __construct($node, $request){
    header("Location: {$node['php_data']['parameters']['url']}");
    exit();
  }

  function getBodyHTML(){}
}

class CFEStaticPage implements IFEPage {
	
  function __construct($node, $request){
    $cid = (int)$node['id'];
    $spid = (int)$request['spid'];

    $db = getdb();

		if($spid>0) {
			$row=$db->getRow("SELECT * FROM static_pages WHERE id={$spid}");
  		if( empty($row) )
  			 throw new EPageNotFound();

		}
		else {
			$row=$db->getRow("SELECT * FROM static_pages WHERE cid={$cid} AND def=1 LIMIT 1");
		}



		$this->data=$row;
		$this->data['node'] = $node;
		$this->data['request'] = $request;

		$this->template = $GLOBALS['CONFIG']['CFEStaticPage']['templates'][$node['template_id']]['file'];
  }

	function getBodyHTML() {

		return ob_include($this->template, $this->data);
	}


}

class CFECustomPage implements IFEPage {
	public $node;
	public $request;
	public $data=array();
	public $template;

  function __construct($node, $request){
  	$cn = get_class($this);
  	$this->data['node']=$node;
  	$this->data['request']=$request;
		$this->template = $GLOBALS['CONFIG'][$cn]['templates'][$node['template_id']];
		if(isset($GLOBALS['CONFIG'][$cn]['templates'][$node['template_id']]['class'])) {
			$this->class=$GLOBALS['CONFIG'][$cn]['templates'][$node['template_id']]['class'];
		}
		else {
			$this->class=null;
		}
  }
  
	function getBodyHTML() {
		if(!empty($this->class)) {
			$obj=new $this->class($this->template,$this);
			return $obj->getBodyHTML();
		}
		return ob_include($this->template['file'], $this->data);
	}

}

class CFESimpleListPage implements IFEPage {
	public $node;
	public $request;

	private $data = array();

	function __construct($node, $request) {
  	$this->node=$node;
  	$this->request=$request;
  	$config = $GLOBALS['CONFIG']['CFESimpleListPage']['templates'][$node['template_id']];

		$IdName = $config['IDVarName'];
		$this->data['IDVarName'] = $config['IDVarName'];

		if(isset($request[$IdName])) {
			$this->template = $config['fileFull'];
		}
		else {
			$this->template = $config['fileList'];
			$this->itemsPerPage = (int)$this->node['php_data']['parameters']['ItemsPerPage'];
			if(empty($this->itemsPerPage)) {
				$this->itemsPerPage=10;
			}
		}
	}

	function getBodyHTML() {
		return ob_include($this->template, $this->data);
	}
}


class CFEPageBar {

  private $GETVarName;

  public $pages;
  public $CurrentPage;

  function __construct($ItemsPerPage, $ItemsCnt, $GETVarName='p'){
  	$this->GETVarName = $GETVarName;
    $this->pages = ceil($ItemsCnt/$ItemsPerPage);
    $this->CurrentPage = (int)$_REQUEST[$this->GETVarName];
    if($this->CurrentPage==0) $this->CurrentPage = 1;

  }

  function getData($href){
  	
  	if(empty($href)){
  		$href = $_GET;
  		unset($href[$this->GETVarName]);
  		$href = empty($href) ? '' : '?'.http_build_query($href);
  	}

    $result = array();

    $separator = strpos($href, "?")===false ? '?':'&amp;';
    $separator .= $this->GETVarName.'=';

    $result['total'] = $this->pages;
    $result['current'] = $this->CurrentPage;

    if ($this->CurrentPage==1){
      $result['prev'] = '';
      $result['first'] = '';
    } else {
      $result['prev'] = $this->CurrentPage>2 ? $href.$separator.($this->CurrentPage-1) : $href;
      $result['first'] = $href;
    }

    if ($this->CurrentPage==$this->pages){
      $result['next'] = '';
      $result['last'] = '';
    } else {
      $result['next'] = $href.$separator.($this->CurrentPage+1);
      $result['last'] = $href.$separator.($this->pages);
    }


    list($fp, $lp) = self::getPagesRange($this->pages, $this->CurrentPage);
    for($i=$fp; $i<=$lp; $i++){
      if($i==1) {
        $result['pages'][$i] = $href;
      } else {
        $result['pages'][$i] = $href.$separator.$i;
      }
    }
    $result['pages'][$this->CurrentPage] = '';
    return $result;
  }

  static function getPagesRange($pages, $page, $maxpages = 20){
        $page_offset=1;
        $dl = ceil( ($maxpages-1) / 2 );
        $dr = floor( ($maxpages-1) / 2 );

        if( $maxpages >= $pages )
        {
            $fp = $page_offset;
            $ep = $pages;
        } else {
            $fp = $page - $dl;
            $ep = $page + $dr;
            if ( $fp < $page_offset )
            {
                $fp = $page_offset;
                $ep = $maxpages;
            }

            if ( $ep > $pages )
            {
                $ep = $pages;
                $fp = $pages - $maxpages;
            }
        }

      return array($fp, $ep);
  }


}



class FEHelper {

	static function getPagedList($hreh, $itemsPerPage, $fields, $SQL) {
		$db=getdb();


		/* Napraven e test v Mysq, pri count(*) toi avtomatichno razkarva ORDER BY chasta*/
		$count = $db->getOne("SELECT COUNT(*) AS cnt ".$SQL);
		$pb = new CFEPageBar($itemsPerPage, $count);
		$data = array();
		$data['PageBar'] = $pb->getData($hreh);
		$i = $pb->CurrentPage - 1;
		$lim = $itemsPerPage;
		$i *= $lim;
		$data['data_list'] = $db->getAll("SELECT $fields $SQL LIMIT {$i}, {$lim}");

		return $data;
	}


	static function IternalToFe($data, $fields){
		foreach ($data as $k=>&$v){
			switch ($fields[$k]['type']){
				case 'string' : $v = htmlspecialchars($v, ENT_QUOTES); break;
				case 'Date' : $v = array('row'=>$v, 'date'=>strftime(DATE_FORMAT, $v)); break;
				case 'Time' : $v = array('row'=>$v, 'time'=> strftime(TIME_FORMAT, $v)); break;
				case 'DateTime' : $v = array('row'=>$v, 'date'=>strftime(DATE_FORMAT, $v), 'time'=> strftime(TIME_FORMAT, $v)); break;

			}
		}
		return $data;
	}

	static function trimArray($a){
		return array_walk_recursive($a, 'trim');
	}



}





?>