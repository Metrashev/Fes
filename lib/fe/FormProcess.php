<?php

class FormProcessor{
	/* @var $dom DOMDocument*/
	public $dom;
	private $data=array();
	public $areAllRequiredFieldsValid=true;
	
	function loadTemplate($str){
		/* @var $dom DOMDocument*/
		$this->dom=new DOMDocument("1.0", "UTF-8");
		@ $this->dom->loadHTML($str);	
		
	}
	
	function autoProcessFields($markRequired=true,$process_labels=true) {
		$this->setIdAttributes();
		if($markRequired) {
			$this->markRequiredFields();	
		}
		if($process_labels) {
			$this->processMultiselectLabels();
		}
	}
	
	function setIdAttributes() {
		$xp=new DOMXPath($this->dom);
		$e=$xp->query("//*[@name]");
		if($e->length) {
			for($i=0;$i<$e->length;$i++) {
				$node=$e->item($i);
				$name=$node->getAttribute("name");
				if(empty($name)) {
					continue;
				}
				$id=$node->getAttribute("id");
				if(empty($id)) {
					if(strpos($name,"[]")!==false) {
						if(!$node->hasAttribute("multiple")) {							
							continue;
						}
					}
					$id=str_replace(array("[","]"),array("_","_"),$name);
					$it=$xp->query("//*[@id='{$id}']");
					if($it->length) {
						continue;
					}
					$node->setAttribute("id",$id);
				}
			}
		}
	}
	
	function processMultiselectLabels() {
		$xp=new DOMXPath($this->dom);
		$e=$xp->query("//select[@multiple]");
		if($e->length) {
			for($i=0;$i<$e->length;$i++) {
				$node=$e->item($i);
				$id=$node->getAttribute("id");
				if(empty($id)) {
					continue;
				}
				$label=$xp->query("//label[@for='{$id}']");
				if($label->length) {
					$label=$label->item(0);
				}
				else {
					continue;
				}
				$label->removeAttribute("for");
				$label->setAttribute("onclick","try{document.getElementById('{$id}').focus()}catch(e){}");
			}
		}
	}
	
	function markRequiredFields() {
		$xp=new DOMXPath($this->dom);
		$e=$xp->query("//*[@required]");
		if($e->length) {
			for($i=0;$i<$e->length;$i++) {
				$node=$e->item($i);
				$id=$node->getAttribute("id");
				if(empty($id)) {
					continue;
				}
				$label=$xp->query("//label[@for='{$id}']");
				if($label->length) {
					$label=$label->item(0);
				}
				else {
					continue;
				}
				$span=$this->dom->createElement('span');
				$span->nodeValue="*";
				$span->setAttribute("class","error");
				$next=$label->nextSibling;
				if($next) {
					$next->parentNode->insertBefore($span,$next);
				}
				else {
					$label->parentNode->appendChild($span);					
				}
				
			}
		}
	}
	
	function getPostValue($data,$name) {
		$v=strpos($name,"[");
		if($v!==false) {
			$v="[".substr_replace($name,"][",$v,1);
		}
		else {
			$v='['.$name.']';
		}
		$str='$data'.$v;
		$str=str_replace("[]","",$str);
		$str=str_replace(array("[","]"),array("['","']"),$str);
		
		@eval("\$h=$str;");
		return $h;
	}
	
	function fillInputs($data,$inputs) {
		if(empty($inputs)) {
			return;
		}
		foreach ($inputs as $v) {
			/* @var $v DOMElement */
			$type=(string)$v->getAttribute("type");
			$type=strtolower($type);
			$name=$v->getAttribute("name");
			if(!$name) {
				continue;
			}
			switch ($type) {
				case "text":
				case "password":
					 {
					$v->setAttribute("value",htmlspecialchars(($this->getPostValue($data,$name))));
					break;
				}
				case "checkbox": {
					$val=$this->getPostValue($data,$name);
					
					if(!is_null($val)) {
						$v->setAttribute("checked","checked");
					}
					break;
				}
				case "radio": {
					$val=$this->getPostValue($data,$name);
					$val1=$v->getAttribute("value");
					if("$val"=="$val1") {
						$v->setAttribute("checked","checked");
					}
					break;
				}
			}
		}
	}
	
	function fillSelects($data,$selects) {
		if(empty($selects)) {
			return;
		}
		foreach ($selects as $v) {
			/* @var $v DOMElement */
			$name=$v->getAttribute("name");
			if(!$name) {
				continue;
			}
			if(!$v->hasChildNodes()) {
				continue;
			}
			$val=$this->getPostValue($data,$name);
			if(is_null($val)) {
				continue;
			}
			
			for($i=0;$i<$v->childNodes->length;$i++) {
				$op=$v->childNodes->item($i);
				$op_val=$op->getAttribute("value");
				if($op->tagName&&strtolower($op->tagName)=="option") {
					if(is_array($val)) {
						if(in_array($op_val,$val)) {
							$op->setAttribute("selected","true");
						}
					}
					else {
						if("$op_val"=="$val") {
							$op->setAttribute("selected","true");
							break;
						}
					}
				}
			}
		}
	}
	
	function fillText($data,$text_fields) {
		if(empty($text_fields)) {
			return;
		}
		foreach ($text_fields as $v) {
			$name=$v->getAttribute("name");
			if(!$name) {
				continue;
			}
			$val=(string)$this->getPostValue($data,$name);
			if(!empty($val)) {
				//$v->nodeValue=str_replace("&","&amp;",$val);
				$val=str_replace(array("&","<",">"),array("&amp;","&lt;","&gt;"),$val);
				$v->nodeValue=htmlspecialchars($val);
			}
		}
	}
	
	function is_valid_email($email) {
		return ereg("^[^@]+@([0-9a-zA-Z][0-9a-zA-Z-]*\.)+[a-zA-Z]{2,4}$", $email);
	}
	
	function fillData($data) {
		$this->data=$data;
		$inputs=$this->dom->getElementsByTagName("input");
		$this->fillInputs($data,$inputs);
		$this->fillSelects($data,$this->dom->getElementsByTagName("select"));
		$this->fillText($data,$this->dom->getElementsByTagName("textarea"));
	}
	
	function validateType($type,$value,$error_msg="") {
		if(empty($type)||empty($value)) {
			return true;
		}
		switch ($type) {
			case "email": {
				if($this->is_valid_email($value)) {
					return true;
				}
				if(empty($error_msg)) {
					return "Invalid E-mail";
				}
				return $error_msg;
			}
		}
		return true;
	}
	
	function cleanErrors($name="") {		
		$xp=new DOMXPath($this->dom);
		if(empty($name)) {
			$e=$xp->query("//*[@for_name]");
		}
		else {
			$e=$xp->query("//*[@for_name='{$name}']");
		}
		if(!$e->length) {
			return;
		}
		for($i=$e->length-1;$i>=0;$i--) {
			$e->item($i)->parentNode->removeChild($e->item($i));
		}
	}
	
	function insertError($node,$name) {
		$xp=new DOMXPath($this->dom);
		$e=$xp->query("//*[@for_name='{$name}']");
		if(!$e->length) {
			return;
		}
		for($i=0;$i<$e->length;$i++) {
			$error_style=(string)$e->item($i)->getAttribute('error_style');
			if(empty($error_style)) {
				$error_style="display:block";
			}
			$e->item($i)->setAttribute("style",$error_style);
		}
		return;		
	}
	
	function validateInputs($inputs,$insert_errors) {
		if(empty($inputs)) {
			return true;
		}
		$errors=array();
		$result=true;
		foreach ($inputs as $v) {
			/* @var $v DOMElement */
			
			$type=(string)$v->getAttribute("type");
			$type=strtolower($type);
			$name=$v->getAttribute("name");
			if(!$name) {
				continue;
			}
			$id=(string)$v->getAttribute("id");
			$error_msg=(string)$v->getAttribute("error_msg");
			switch ($type) {
				case "text": {
					$val=(string)$v->getAttribute("value");
					if(empty($val)) {
						if(!$v->hasAttribute("required")) {
							//$this->cleanErrors($name);
							continue;
						}
						$this->areAllRequiredFieldsValid=false;
						if(!empty($error_msg)) {
							$errors[$id]=$error_msg;
						}
					//	else {
					//		$errors[$id]="";
					//	}
						$result=false;
						if($insert_errors) {
							$this->insertError($v,$name);
						}
						continue;
					}
					$validation=$this->validateType((string)$v->getAttribute("validate"),$val,$error_msg);
					if($validation!==true) {
						$errors[$id]=$validation;
						
						$result=false;
						if($insert_errors) {
							$this->insertError($v,$name);
						}
						continue;
					}
					//$this->cleanErrors($name);
					break;
				}
				case "radio":
				case "checkbox": {
					$val=$this->getPostValue($this->data,$name);
					if(is_null($val)) {
						if(!$v->hasAttribute("required")) {
							continue;
						}
						if(!empty($error_msg)) {
							$errors[$id]=$error_msg;
						}
						//else {
						//	$errors[$id]="";
						//}
						$this->areAllRequiredFieldsValid=false;
						$result=false;
						if($insert_errors) {
							$this->insertError($v,$name);
						}
						continue;
					}
					//$this->cleanErrors($name);
					break;
				}
			}
		}
		if($result) {
			return $result;
		}
		return $errors;
	}
	
	function validateSelect($selects,$insert_errors) {
		if(empty($selects)) {
			return true;
		}
		$errors=array();
		$result=true;
		foreach ($selects as $v) {
			/* @var $v DOMElement */
			if(!$v->hasAttribute("required")) {
				continue;
			}			
			$name=$v->getAttribute("name");
			if(!$name) {
				continue;
			}
			if(!$v->hasChildNodes()) {
				continue;
			}
			$val=$this->getPostValue($this->data,$name);
			$id=(string)$v->getAttribute("id");
			$error_msg=(string)$v->getAttribute("error_msg");
			if(is_null($val)) {
				if(!empty($error_msg)) {
					$errors[$id]=$error_msg;
				}
				//else {
				//	$errors[$id]="";
				//}
				$result=false;
				$this->areAllRequiredFieldsValid=false;
				if($insert_errors) {
					$this->insertError($v,$name);
				}
				continue;
			}
			if("$val"=="") {
				if(!empty($error_msg)) {
					$errors[$id]=$error_msg;
				}
				//else {
				//	$errors[$id]="";
				//}
				if($insert_errors) {
					$this->insertError($v,$name);
				}
				$result=false;	
				$this->areAllRequiredFieldsValid=false;	
				continue;		
			}
			//$this->cleanErrors($name);
		}
		if($result) {
			return true;
		}
		return $errors;
	}
	
	function validateText($text_fields,$insert_errors) {
		if(empty($text_fields)) {
			return true;
		}
		$errors=array();
		$result=true;
		foreach ($text_fields as $v) {
			if(!$v->hasAttribute("required")) {
				continue;
			}
			$val=(string)$v->nodeValue;
			$id=(string)$v->getAttribute("id");
			if(empty($val)) {
				$error_msg=(string)$v->getAttribute("error_msg");
				if(!empty($error_msg)) {
					$errors[$id]=$error_msg;
				}
				//else {
				//	$errors[$id]="";
				//}
				$result=false;
				$this->areAllRequiredFieldsValid=false;
				if($insert_errors) {
					$this->insertError($v,(string)$v->getAttribute("name"));
				}
				continue;
			}			
			//$this->cleanErrors($name);
		}
		if($result) {
			return true;
		}
		return $errors;
	}
	
	function validate($insert_errors=true) {
		$xp=new DOMXPath($this->dom);
		$e=$xp->query("//input[@required]");		
		//$result_inp=$this->validateInputs($this->dom->getElementsByTagName("input"),$insert_errors);
		$result_inp=$this->validateInputs($e,$insert_errors);
		$e=$xp->query("//input[@validate]");
		//$result_inp=$this->validateInputs($this->dom->getElementsByTagName("input"),$insert_errors);
		$result_inp1=$this->validateInputs($e,$insert_errors);
		
		//$result_sel=$this->validateSelect($this->dom->getElementsByTagName("select"),$insert_errors);
		$result_sel=$this->validateSelect($xp->query("//select[@required]"),$insert_errors);
		//$result_text=$this->validateText($this->dom->getElementsByTagName("textarea"),$insert_errors);
		$result_text=$this->validateText($xp->query("//textarea[@required]"),$insert_errors);
		$arr=array();
		$result=true;
		
		if(is_array($result_inp)) {
			$arr=$result_inp;
			$result=false;
		}
		if(is_array($result_inp1)) {
			$arr=array_merge($arr,$result_inp1);
			$result=false;
		}
		if(is_array($result_sel)) {
			$arr=array_merge($arr,$result_sel);
			$result=false;
		}
		if(is_array($result_text)) {
			$arr=array_merge($arr,$result_text);
			$result=false;
		}
		
		$e=$xp->query("//*[@for_name]");
		if($e->length) {
			for($i=0;$i<$e->length;$i++) {
				$node=$e->item($i);
				$name=(string)$node->getAttribute("for_name");
				if(empty($name)) {
					continue;
				}
				$id=str_replace(array("[","]"),array("_","_"),$name);
				if(!isset($arr[$id])) {
					$this->cleanErrors($name);
				}
			}
		}
	//	foreach ($arr as $ek=>$ev) {
	//		
	//		//$this->cleanErrors($name);
	//	}
		if($result) {
			return true;
		}
		return $arr;
	}
	
	function makereadOnlyInputs($inputs) {
		if(!empty($inputs)) {
			for($i=$inputs->length-1;$i>=0;$i--) {
				$span=$this->dom->createElement('span');
				$type=(string)$inputs->item($i)->getAttribute("type");
				$type=strtolower($type);
				switch ($type) {
					case "text": {
						$val=(string)$inputs->item($i)->getAttribute("value");;
						$span->nodeValue=str_replace("&","&amp;",$val);
						break;
					}
					case "checkbox": {
						$span->nodeValue=$inputs->item($i)->hasAttribute("checked")?"[x]":"[&nbsp;&nbsp;]";
						break;
					}
					case "radio": {
						$span->nodeValue=$inputs->item($i)->hasAttribute("checked")?"(o)":"(&nbsp;&nbsp;)";
						break;
					}
					default: {
						$span->nodeValue="";
					}
				}
				
				$inputs->item($i)->parentNode->replaceChild($span,$inputs->item($i));
			}
		}
	}
	
	function makereadOnlySelects($selects) {
		if(!empty($selects)) {
			for($i=$selects->length-1;$i>=0;$i--) {
				$span=$this->dom->createElement('span');
				$name=$selects->item($i)->getAttribute("name");
				if(!$name) {
					$name="";
				}
				$val=$this->getPostValue($this->data,$name);
				if(is_array($val)) {
					$span->nodeValue=implode(", ",$val);
				}
				else {
					$span->nodeValue=(string)$val;
				}				
				$selects->item($i)->parentNode->replaceChild($span,$selects->item($i));
			}
		}
	}
	
	function makereadOnlyText($text_fields) {
		if(!empty($text_fields)) {
			for($i=$text_fields->length-1;$i>=0;$i--) {
				$div=$this->dom->createElement('div');
				$val=(string)$text_fields->item($i)->nodeValue;
				$text_fields->item($i)->parentNode->replaceChild($div,$text_fields->item($i));
				$val=str_replace("&","&amp;",$val);
				$ex=explode("\n",$val);
				foreach ($ex as $ex_v) {
					$span=$this->dom->createElement('div');
					$span->nodeValue=$ex_v;
					$div->appendChild($span);
				}				
			}
		}
	}
	function makereadOnlyA($a_fields) {
		if(!empty($a_fields)) {
			for($i=$a_fields->length-1;$i>=0;$i--) {
				$span=$this->dom->createElement('span');
				$val=(string)$a_fields->item($i)->nodeValue;
				$span->nodeValue=str_replace("&","&amp;",$val);
				$a_fields->item($i)->parentNode->replaceChild($span,$a_fields->item($i));
			}
		}
	}
	
	function getReadOnlyVersion($bodyTag="<body>") {
		$this->makereadOnlyInputs($this->dom->getElementsByTagName("input"));
		$this->makereadOnlySelects($this->dom->getElementsByTagName("select"));
		$this->makereadOnlyText($this->dom->getElementsByTagName("textarea"));
		$this->makereadOnlyA($this->dom->getElementsByTagName("a"));
		return $this->getHTML($bodyTag);
	}
	
	function getHTML($bodyTag="<body>") {
		
		$s=$this->dom->saveHTML();
		$pos=stripos($s,$bodyTag);
		if($pos!==false) {
 			$pos+=strlen($bodyTag);
		}
		else {
			$pos=201;
		}
		$end_body_tag=str_replace("<","</",$bodyTag);
		
		$pos1=strrpos($s,$end_body_tag);
		
		
		if($pos1==false) {
			$pos1=$pos+16;
			$len=strlen($s)-$pos1;
		}
		else {
			$len=$pos1-$pos;
		} 
		//return substr($s,$pos,$len);
		return html_entity_decode(substr($s,$pos,$len),ENT_NOQUOTES,"UTF-8");
	}
	
}
?>