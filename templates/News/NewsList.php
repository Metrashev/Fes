<?php

foreach ($data['data_list'] as $news){

	include(dirname(__FILE__)."/BriefNewsBlock.php");

}


include(dirname(__FILE__)."/../Core/PageBar.php");
?>