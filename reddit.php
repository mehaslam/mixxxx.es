<h1>latest from <a href="http://reddit.com/r/futuregarage/">r/futuregarage</a></h1>
<?php
error_reporting(E_ERROR | E_PARSE);
//require(__DIR__.'/../video.php');
//require(__DIR__.'/../simple_html_dom.php');
require('classes/simple_html_dom.php');

		function get_data($url)
		{
		  $ch = curl_init();
		  $timeout = 5;
		  curl_setopt($ch,CURLOPT_URL,$url);
		  curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		  curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
		  $data = curl_exec($ch);
		  curl_close($ch);
		  return json_decode($data);
		}
	
		$json = get_data('http://www.reddit.com/r/futuregarage/.json');
		
		foreach ($json as $key => $dt) {
			if ($key == "data") {
				foreach ($dt as $parent) {
					if (isset($parent) && $parent != null && count($parent) > 1) {
						foreach ($parent as $post) {
							foreach ($post as $postkey => $content) {
								if ($postkey == "data") {
									$posts[] = object2array($content);
								}
							}
						}
					}
				}
			}
		}
		
		foreach ($posts as $post) {
			if ($post['domain'] == "youtube.com") {
				$media = object2array($post['media_embed']);
				$embed = $media['content'];
				
				$html = str_get_html(html_entity_decode(($embed)));
				
				if (isset($html) && $html !=null) {
				
						foreach ($html->find('embed') as $embedded) {
							$full_urls[] = $embedded->src;
						}
						
						foreach ($full_urls as $full_url) {
							if (isset($full_url) && $full_url != null && $full_url != '') {
								$url[] = youtube_id_from_url($full_url);
								foreach ($url as $single_url) {
									if (isset($single_url) && $single_url != null && $single_url != '') {
										$urls_d[] = $single_url;
									}
								}
							}
						}
				 
				}
			}
		}
		
		$urls = array_unique($urls_d); //remove duplicates.
		
		function object2array($object) {
		    if (is_object($object)) {
		        foreach ($object as $key => $value) {
		            $array[$key] = $value;
		        }
		    }
		    else {
		        $array = $object;
		    }
		    if (isset($array)) {
 		    	return $array;
 		    } else {
 		    	return null;
 		    }
		}
		
		function youtube_id_from_url($url) {
			
			$vars = parse_url($url);
			$path = $vars['path'];
			
			preg_match('%^(?:/v/)([\w-]{10,12})$%x', $path, $matches);
			return $matches[1];
			
		}
		
		//Yo dawg, try get this to show 4 videos with a more button. Manipulate embed tags in JS rather than more PHP processing?
		
		$redditCount = 0;
		foreach ($urls as $key => $video) {
			?>
			
			<div class="vid_container">
			
				<iframe	id="<?php echo $key; ?>" width="460" height="250" src="http://www.youtube.com/embed/<?php echo $video; ?>?html5=1" frameborder="0" allowfullscreen></iframe>
			
			</div>
		
		<?php } ?>
