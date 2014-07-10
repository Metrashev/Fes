<?PHP

function win3utf($s) {
   for($i=0, $m=strlen($s); $i<$m; $i++)    {
       $c=ord($s[$i]);
       if ($c<=127) {$t.=chr($c); continue; }
       if ($c>=192 && $c<=207) {$t.=chr(208).chr($c-48); continue; }
       if ($c>=208 && $c<=239) {$t.=chr(208).chr($c-48); continue; }
       if ($c>=240 && $c<=255) {$t.=chr(209).chr($c-112); continue; }
       if ($c==184) { $t.=chr(209).chr(209); continue; };
       if ($c==168) { $t.=chr(208).chr(129); continue; };
   }
   return $t;
}

class FormNode {
  var $node;
  
  function FormNode($node){
  	$this->node = $node;
  }
  
  function getName(){
    $name = $this->node->getAttribute('name');
    return strtr($name, " ", "_");
  }
  function validate(){
    return true;
  }
}

class ButonNode extends FormNode {
  function preserve($data){
  	
  }
  
  function makeReadOnly(){
    $doc = $this->node->ownerDocument;
    $newNode = $doc->createElement('SPAN');    
    $this->node->parentNode->replaceChild($newNode, $this->node);
  }
} 

class TextInputNode extends FormNode {
  
  function preserve($data){
 
    $this->node->setAttribute('value',$data[$this->getName()]);
  }
  
  function makeReadOnly(){
  	
  	$doc = $this->node->ownerDocument;
    $newNode = $doc->createElement('SPAN');
    $newNode->nodeValue = $this->node->getAttribute('value');
    $this->node->parentNode->replaceChild($newNode, $this->node);
  }
  
  function validate(){
    if($this->node->hasAttribute('required')){
      return $this->node->getAttribute('value')!="";
    }
    return true;
  }
}

class TextAreaNode extends FormNode {
  
  
  function preserve($data){
    $content = $data[$this->getName()];
    $this->node->nodeValue = $content;
  }
  
  function makeReadOnly(){
    $doc = $this->node->ownerDocument;
    $newNode = $doc->createElement('SPAN');
    $lines  = explode("\n",$this->node->nodeValue);
    foreach ($lines as $line){
      $tmp = $doc->createTextNode($line);
      $newNode->appendChild($tmp);
      $tmp = $doc->createElement('BR');
      $newNode->appendChild($tmp);
    }
    $this->node->parentNode->replaceChild($newNode, $this->node);
  }
  
  function validate(){
    if($this->node->hasAttribute('required')){
      return $this->node->nodeValue!="";
    }
    return true;
  } 
}

class SelectNode extends FormNode {
  
  
  function preserve($data){

    $content = $data[$this->getName()];
    $options = $this->node->childNodes;
    //print_r($options);
    foreach ($options as $option){
      if($option->tagName!='option')
        continue;
      $val = $option->getAttribute('value');
      if(!$val)
        $val = $option->nodeValue;
      if($option->hasAttribute('selected'))
        $option->removeAttribute('selected');
        
      if($val == $content){
        $option->setAttribute('selected', 'selected');
        $this->node->setAttribute('value', $option->nodeValue);
      }
    }
  }
  
  function makeReadOnly(){
    $doc = $this->node->ownerDocument;
    $newNode = $doc->createElement('SPAN');
    if($this->node->hasAttribute('value')) {
    	$newNode->nodeValue =$this->node->getAttribute('value');
    }
    else {
    	$arr=array();
    	foreach ($this->node->childNodes as $n) {
    		
    		if($n->hasAttribute('selected')) {
    			$arr[]=$n->nodeValue;
    		}
    	}
    	
    	$newNode->nodeValue='['.implode(", ",$arr).']';
    }
    $this->node->parentNode->replaceChild($newNode, $this->node);
  }
  
  function validate(){
    if($this->node->hasAttribute('required')){
      return $this->node->getAttribute('value')!="";
    }
    return true;
  }  
}


class CheckBoxNode extends FormNode {
  
  function preserve($data){
    if(isset($data[$this->getName()])){
      $this->node->setAttribute('checked', 'checked');
    } else {
      $this->node->removeAttribute('checked');
    }
  }
  
  function makeReadOnly(){
    $doc = $this->node->ownerDocument;
    $newNode = $doc->createElement('SPAN');
    $newNode->nodeValue = $this->node->hasAttribute('checked') ? '[x]' : '[ ]';
    $this->node->parentNode->replaceChild($newNode, $this->node);
  }
}

class RadioButtonNode extends FormNode {
  
  function getRadioGroupMembers(){
    
    
    
    $name = $this->node->getAttribute('name');
    
    $xpat_ctx = new DOMXPath($this->node->ownerDocument);
    $res = $xpat_ctx->query("//textarea | //input | //select");
    return $res->nodeset;
    /*
    $xpat_ctx =& xpath_new_context($this->node->ownerDocument());
    $res =& xpath_eval($xpat_ctx, "//input[@type='radio' and @name=\"$name\"]");

    return $res->nodeset;*/
  }
  
  function preserve($data){

    $content = $data[$this->getName()];
    $options = $this->getRadioGroupMembers();
    
    foreach ($options as $option){
      $val = $option->getAttribute('value');
      if($option->hasAttribute('checked'))
        $option->removeAttribute('checked');
        
      if($val == $content){
        $option->setAttribute('checked', 'checked');
      }
    }
  }
  
  function makeReadOnly(){
    $doc = $this->node->ownerDocument;
    $newNode = $doc->createElement('SPAN');
    $newNode->nodeValue = $this->node->hasAttribute('checked') ? '(o)' : '( )';
    $this->node->parentNode->replaceChild($newNode, $this->node);
  }
}

function factory($node){
	switch ($node->tagName) {
		case 'textarea': {
			return new TextAreaNode($node);		
		}
		case 'input': {
			$type=$node->hasAttribute('type')?$node->getAttribute('type'):'text';
			switch($type) {
				case 'text': {
					return new TextInputNode($node);	
				}
				case 'checkbox': {
					return new CheckBoxNode($node);	
				}
				case 'radio': {
					return new RadioButtonNode($node);
				}
				default: {
					return new ButonNode($node);
				}
			}
		}
		break;
		case 'select': {
			 return new SelectNode($node);
		}
	}
	return null;
 /* if($node->tagName == 'textarea'){
    return new TextAreaNode($node);
  } else if ($node->tagName == 'input') {
    if($node->getAttribute('type')=='text'||(!$node->hasAttribute('type'))) {
      return new TextInputNode($node);
    } else if ($node->getAttribute('type')=='checkbox') {
      return new CheckBoxNode($node);
    } else if ($node->getAttribute('type')=='radio') {
      return new RadioButtonNode($node);
    } else {
      return new ButonNode($node);
    }
  } else if ($node->tagName == 'select') {
    return new SelectNode($node);
  }
  
  return null;*/
}

class FormProcessor{
  public $dom=null;	/* @var $dom DomDocument*/
  function loadTemplate($file){
    $this->dom = new DOMDocument("1.0", "UTF-8");

 //   $file=str_replace("<nobr>","",$file);
 //   $file=str_replace("</nobr>","",$file);
   @ $this->dom->loadHTML($file);
    
  }
  
 
  function fillData($data){
    /*
    foreach ($data as $k=>$v){
      $data[$k] = win3utf($v);
    } 
    */   
    $list = $this->getFormElements();
    foreach ($list as $node){
      $n = factory($node);
      if(is_null($n)) continue;
      
      $n->preserve($data);
    }
  }
    
  function getFormElements($extra=''){
    //$xpat_ctx =& xpath_new_context($this->dom);
    //$res =& xpath_eval($xpat_ctx, "//textarea | //input | //select");
    if(!empty($extra)) {
    	$extra="|//".$extra;
    }
    $xpat_ctx = new DOMXPath($this->dom);
    return $xpat_ctx->query("//textarea | //input | //select |//script{$extra}");
  }
  
  function getReadOnlyVersion($remove_A_values=array()){
  
    $list = $this->getFormElements('a');
    foreach ($list as $node){
   		if(strtolower($node->tagName)=='script') {
   			$node->parentNode->removeChild($node);
   			continue;
   		}
   		if($node->tagName=='a') {
   			$doc = $node->ownerDocument;
   			$newNode = $doc->createElement('SPAN');
   			$s=(string)$node->nodeValue;
   			if(empty($s)||!in_array($s,$remove_A_values)) {
   		    	$newNode->nodeValue = $node->nodeValue;
   			}
   			else {
   				$newNode->nodeValue='';
   			}
		    $node->parentNode->replaceChild($newNode, $node);
   			continue;
   		}
      $n = factory($node);
      if(is_null($n)) continue;
      $n->makeReadOnly();
    }
    return $this->getHtml();
  }
  
  function validate(){
    $valid = true;
    $list = $this->getFormElements();
    foreach ($list as $node){
      $n = factory($node);
      if(is_null($n)) continue;
      $valid = $valid & $n->validate();
    }
    
    return $valid;
  }
  
  function getHtml(){
  //	$html=$this->dom->saveHTML();
  //	return( iconv('UTF-8', 'windows-1251', $html));

    
    $s = simplexml_import_dom($this->dom);
    foreach ($s->body->children()  as $q)
    $html .= $q->asXML(); 
    return $html;
   //return( iconv( 'windows-1251','UTF-8', $html));
  }
}


?>