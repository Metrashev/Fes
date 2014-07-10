<?php

$GLOBALS['FESkinPage']->PageTitle[] = $data['title'];

$img = (!empty($data['picture'])) ? '<img src="../../files/mf/news_pages/'.$data['id'].'_picture_pic'.$data['picture'].'" alt="" class="img" />' : '';

echo <<<EOD
<div class="news">
<div class="subInfo">{$data['due_date']['date']}</div>				  
	<h1>{$data['title']}</h1>
	{$img}
	<div style="padding:10px 0">{$data['body']}</div>
</div>
EOD;

?>