<?php

if($data['picture']['pic']) {	
		 $img = <<<EOD
		 <img src="{$data['picture']['pic2']}" class="img" alt="{$data['title']}" style="margin:10px 10px 0 0;" />
EOD;
		} else {
			$img = "";
		}
echo <<<EOD
<div>

<h3>{$data['title']}</h3>

<table cellpadding="0" cellspacing="0" style="padding-bottom:10px;">
<tr>
	<td>
		{$img}
	</td>
	<td>
		<div>{$data['subtitle']}</div>
	</td>
</tr>
</table>
		
	
	<div class="text">{$data['body']}</div>
</div>
EOD;
?>