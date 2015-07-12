<?php
   function fetchData($url){
      try{
         $ch = curl_init();
         curl_setopt($ch, CURLOPT_URL, $url);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch, CURLOPT_TIMEOUT, 20);
         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
         $result = curl_exec($ch);
         curl_close($ch); 
         return $result;
      } catch(Exception $e) {
	      return $e->getMessage();
      }
   }

   $result = fetchData("https://api.instagram.com/v1/users/41490154/media/recent/?client_id=aca30b5a2275414aa38510c40e79ebab");

   $result = json_decode($result);
   foreach ($result->data as $post) {
      if(empty($post->caption->text)) {
         // Do Nothing
      }
      else {
         echo '<a target="blank" href="'.$post->link.'"><div class="instagram-item">
         <img src="'.$post->images->low_resolution->url.'" alt="'.$post->caption->text.'" />
         <div class="instagram-desc">'.htmlentities($post->caption->text).' | '.htmlentities(date("F j, Y, g:i a", $post->caption->created_time)).'</div></div></a>';
      }

   }
?>