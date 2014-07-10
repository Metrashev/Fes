<?php

include(dirname(__FILE__).'/../../lib/fe/FormProcess.php');
require_once(dirname(__FILE__).'/../../lib/be/fe_utils.php');
$_POST=pPrado::pradoStripSlashes($_POST);

$fp=new FormProcessor();
$str=file_get_contents(dirname(__FILE__).'/form_bg.html');
$fp->loadTemplate($str);

$fp->fillData($_POST);
$errors=$fp->validate();
echo $fp->getHtml();



if(is_array($errors)) {
	echo "<pre>";
	print_r($errors);
	echo "</pre>";
}

echo "<hr />";
echo $fp->getReadOnlyVersion("<print>");

echo <<<EOD
<style>
.error {
	color:red;
}
</style>
EOD;
return;


$GLOBALS['HidePrintLink'] = true;

//	require_once('../lib/fe/controls.php');
//	require_once('../lib/fe/controls.php');

	require_once(dirname(__FILE__).'/../../lib/fe/FormProcessor5.php');
	require_once(dirname(__FILE__).'/../../lib/be/fe_utils.php');



	$fp=new FormProcessor();
	$file=dirname(__FILE__).'/form_bg.html';
	ob_start();
	include($file);
	$str=ob_get_clean();


	$fp->loadTemplate($str);

	$_POST=pPrado::pradoEncodeData($_POST);

	$fp->fillData($_POST);

	$errors=array();
	if(isset($_POST['Submit'])) {
		
		
		if(!is_valid_email_address($_POST['email'])) {
			$errors['email'] ="Невалиден e-mail!";
		}
		//if ($_POST['Agree']==false){
			//$errors['Agree']= "Трябва да се съгласите с условията!";
		//}

		if(!$fp->validate()) {
			$errors[]= "Попълнете всички задължителни полета!";
		}

		if(empty($errors)) {


				$db=getDB();
				$arr=$_POST;
				unset($arr['Submit']);
				unset($arr['Agree']);
				//$fields=serialize($arr);

				$arr['created_date'] = date("Y-m-d");


				$vals=implode(",",array_fill(0,count($arr),'?'));
				$fields="`".implode("`,`",array_keys($arr))."`";

				foreach ($arr as $ak=>$av) {
					$arr[$ak]=htmlspecialchars($arr[$ak]);
				}


				//$db->execute("insert into clients ({$fields}) values({$vals})",$arr);
				

  				 require_once(dirname(__FILE__).'/../../lib/mime_mail/htmlMimeMail.php');
  				
				$file = $fp->getReadOnlyVersion();
    			$mail = new htmlMimeMail();
   				$mail->setSubject("Contact Us Form");
   				$mailBody = "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" /><style>".file_get_contents("lib.css")."</style><body>$file</body>";
    			$mail->setHtml($mailBody);
   				//$bla=$mail->send($_POST['email'],array("spare.parts@inatrding.com"));
   				$bla=$mail->send($_POST['email'],array("orlin@studioitti.com"));
    
    			
				if($bla){include(dirname(__FILE__).'/10x_bg.html');
				return ;

				}
		}

	}
	if(!empty($errors)) {
		$str_errors=FE_Utils::renderErrors($errors);
	}
	else {
		$str_errors='';
	}
echo <<<EOD

EOD;
	$str = $fp->getHtml();
$db=getdb();

//$data=$db->getAll("select body from static_pages where cid=37");


	//echo str_replace('#ERRORS#', '<br/><div class="error">'.$str_errors.'</div>', $str);
	
$crumbs = $GLOBALS['FESkinPage']->getCrumbsPathHtml();
$title = $db->getOne("SELECT value FROM categories WHERE id='".$_GET['cid']."'");

echo <<<EOD

<div id="addressBar">{$crumbs}</div>

	
	
<div class="content">
<img src="/i/order/catalog.jpg" align="right"/>
	<div class="Title">{$title}</div><br/>
	<p style="color:red;">$str_errors</p>
	$str

</div>
EOD;

?>

