<?php

$MenuItems = $GLOBALS['FESkinPage']->MenuItems;
$html = '';
		
		$startLevel = 2;
		$level = $startLevel-1;
		
		foreach ($MenuItems as $item){
			if($item['visible']!=1) continue;
			if($item['level']<$startLevel) continue;
			
			$sel = $item['selected'] ? ' class="selected"' : '';
			if (($item['level']>$level) && ($level != $startLevel-1)) {
				$html .= <<<EOD
<ul>

EOD;
			}
			
			if($item['level']<$level){
				for($level; $level>$item['level']; $level--) {
					if ($level == $startLevel+1) { 
						$html .= '</ul>';
					} else {
						$html .= "</ul>";	
					}
				}
			}

			$level = $item['level'];
			
	if ($level == $startLevel) {
		
		if ($opened) {
			$html .= "</div>";
			$opened = false;
		}
		
		
			$opened = true;
			$html .= <<<EOD
<div>
	<h1 $sel><a href="{$item['href']}"{$node['target']} $sel>{$item['value']}</a></h1>
EOD;
	} else {
			$html .= <<<EOD
<li $sel><a href="{$item['href']}"{$node['target']} $sel>{$item['value']}</a></li>
EOD;

		   }
		}
		
/*		for($level; $level>=$startLevel; $level--){
				$html .= <<<EOD
</ul>33
EOD;
		}*/
		echo $html."</div>";
		?>
		
		<script>
		(function(d, s, id) {
			var js, fjs = d.getElementsByTagName(s)[0];
			if (d.getElementById(id))
				return;
			js = d.createElement(s);
			js.id = id;
			js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.0";
			fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));
	</script>

	<div  style=" background: #FFFFFF; padding:0; margin-top: 20px; border:0;" class="fb-like-box" 
		data-href="https://www.facebook.com/FESBulgarien" data-width="170" 
		data-height="300" data-colorscheme="light" data-show-faces="true"
		data-header="true" data-stream="false" data-show-border="true"></div>