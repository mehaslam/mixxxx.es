<h1>latest from <a href="http://reddit.com/r/futuregarage/">r/futuregarage</a></h1>
<?php
error_reporting(0);
//require(__DIR__.'/../video.php');
//require('classes/simple_html_dom.php');


		$filename = 'reddit.data';

		if(file_exists($filename)) {
			if(filemtime($filename)>strtotime('-1 hour')) {
				$json = unserialize(file_get_contents($filename));
			} else {
				$json = get_data('http://www.reddit.com/r/futuregarage/.json');
				file_put_contents($filename, serialize($json));
			}
		} else {
			$json = get_data('http://www.reddit.com/r/futuregarage/.json');
			file_put_contents($filename, serialize($json));
		}
		
		$posts = $json->data->children;
		
		foreach ($posts as $post) {
			if ($post->data->domain == "youtube.com") {
				$links[] = $post->data->media->oembed->url;
			}
		}
		
		//remove duplicates
		$urls = array_unique($links);
	
		//convert full youtube url into video ids.
		foreach ($urls as $url) {
			$youtube_ids[] = youtube_id_from_url($url);
		}
		
		//Get rid of any empty entries.
		foreach ($youtube_ids as $key => $id) {
		
		    if ($youtube_ids[$key] == '')
		    {
		        unset($youtube_ids[$key]);
		    }
		}

		foreach ($youtube_ids as $key => $youtube_id) {
			?>
			
			<div class="vid_container">
			
				<iframe	id="<?php echo $key; ?>" width="460" height="250" src="http://www.youtube.com/embed/<?php echo $youtube_id; ?>?html5=1" frameborder="0" allowfullscreen></iframe>
			
			</div>
		
		<?php }

//Generic functions.		
		
function get_data($url) {
	$ch = curl_init();
	$timeout = 5;
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
	$data = curl_exec($ch);
	curl_close($ch);
	return json_decode($data);
}
		
function youtube_id_from_url($url) { //credit to user hakre - http://stackoverflow.com/questions/6556559/youtube-api-extract-video-id
    $pattern = 
        '%^# Match any youtube URL
        (?:https?://)?  # Optional scheme. Either http or https
        (?:www\.)?      # Optional www subdomain
        (?:             # Group host alternatives
          youtu\.be/    # Either youtu.be,
        | youtube\.com  # or youtube.com
          (?:           # Group path alternatives
            /embed/     # Either /embed/
          | /v/         # or /v/
          | /watch\?v=  # or /watch\?v=
          )             # End path alternatives.
        )               # End host alternatives.
        ([\w-]{10,12})  # Allow 10-12 for 11 char youtube id.
        $%x'
        ;
    $result = preg_match($pattern, $url, $matches);
    if (false !== $result) {
        return $matches[1];
    }
    return false;
}

?>