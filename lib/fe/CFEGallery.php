<?php
class CBOGallery extends ABOBase {
	
	private $cid;
	
	public $fields = array(
		'img'=> array('type'=>'ManagedImage', 'label'=>'Picture', 'sizes'=>array()),
		'text'=> array('type'=>'string', 'label'=>'Име', 'size'=>255, 'lng'=>0, 'lngs'=>array( 'en'=>'')),
	);
	
	public $tableName = 'gallery';

}

class CFEGallery extends CFECustomPage {
	
	function getBodyHTML() {

		$cid = (int)$this->data['node']['id'];
		$bo = new CBOGallery();
		$param = $this->data['node']['php_data']['parameters']['gallery'];
		if($param['s']['t']!='') $bo->fields['img']['sizes'][]='s';
		if($param['m']['t']!='') $bo->fields['img']['sizes'][]='m';
		if($param['l']['t']!='') $bo->fields['img']['sizes'][]='l';
		if(!empty($this->template['ItemsPerPage'])){
			$href = $_GET;
			unset($href['p']);
			$href = '/?'.http_build_query($href);
			$this->data += $bo->getPagedList($href, $this->template['ItemsPerPage'], 'id,img,text',"cid=$cid",'order_field');
		} else {		
			$this->data['gallery'] = $bo->getList('id,img,text',"cid=$cid",'order_field');
		}

		return parent::getBodyHTML();
	}
}

?>