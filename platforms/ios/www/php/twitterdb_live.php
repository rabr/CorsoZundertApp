<?php
/**
 * Twitter-API-PHP : Simple PHP wrapper for the v1.1 API
 * 
 * PHP version 5.3.10
 * 
 * @category Awesomeness
 * @package  Twitter-API-PHP
 * @author   James Mallison <me@j7mbo.co.uk>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://github.com/j7mbo/twitter-api-php
 */

if ( !class_exists('TwitterAPIExchange') ) {
class TwitterAPIExchange 
{
    private $oauth_access_token;
    private $oauth_access_token_secret;
    private $consumer_key;
    private $consumer_secret;
    private $postfields;
    private $getfield;
    protected $oauth;
    public $url;

    /**
     * Create the API access object. Requires an array of settings::
     * oauth access token, oauth access token secret, consumer key, consumer secret
     * These are all available by creating your own application on dev.twitter.com
     * Requires the cURL library
     * 
     * @param array $settings
     */
    public function __construct(array $settings)
    {
        if (!in_array('curl', get_loaded_extensions())) 
        {
            throw new Exception('You need to install cURL, see: http://curl.haxx.se/docs/install.html');
        }
        
        if (!isset($settings['oauth_access_token'])
            || !isset($settings['oauth_access_token_secret'])
            || !isset($settings['consumer_key'])
            || !isset($settings['consumer_secret']))
        {
            throw new Exception('Make sure you are passing in the correct parameters');
        }

        $this->oauth_access_token = $settings['oauth_access_token'];
        $this->oauth_access_token_secret = $settings['oauth_access_token_secret'];
        $this->consumer_key = $settings['consumer_key'];
        $this->consumer_secret = $settings['consumer_secret'];
    }
    
    /**
     * Set postfields array, example: array('screen_name' => 'J7mbo')
     * 
     * @param array $array Array of parameters to send to API
     * 
     * @return TwitterAPIExchange Instance of self for method chaining
     */
    public function setPostfields(array $array)
    {
        if (!is_null($this->getGetfield())) 
        { 
            throw new Exception('You can only choose get OR post fields.'); 
        }
        
        if (isset($array['status']) && substr($array['status'], 0, 1) === '@')
        {
            $array['status'] = sprintf("\0%s", $array['status']);
        }
        
        $this->postfields = $array;
        
        return $this;
    }
    
    /**
     * Set getfield string, example: '?screen_name=J7mbo'
     * 
     * @param string $string Get key and value pairs as string
     * 
     * @return \TwitterAPIExchange Instance of self for method chaining
     */
    public function setGetfield($string)
    {
        if (!is_null($this->getPostfields())) 
        { 
            throw new Exception('You can only choose get OR post fields.'); 
        }
        
        $search = array('#', ',', '+', ':');
        $replace = array('%23', '%2C', '%2B', '%3A');
        $string = str_replace($search, $replace, $string);  
        
        $this->getfield = $string;
        
        return $this;
    }
    
    /**
     * Get getfield string (simple getter)
     * 
     * @return string $this->getfields
     */
    public function getGetfield()
    {
        return $this->getfield;
    }
    
    /**
     * Get postfields array (simple getter)
     * 
     * @return array $this->postfields
     */
    public function getPostfields()
    {
        return $this->postfields;
    }
    
    /**
     * Build the Oauth object using params set in construct and additionals
     * passed to this method. For v1.1, see: https://dev.twitter.com/docs/api/1.1
     * 
     * @param string $url The API url to use. Example: https://api.twitter.com/1.1/search/tweets.json
     * @param string $requestMethod Either POST or GET
     * @return \TwitterAPIExchange Instance of self for method chaining
     */
    public function buildOauth($url, $requestMethod)
    {
        if (!in_array(strtolower($requestMethod), array('post', 'get')))
        {
            throw new Exception('Request method must be either POST or GET');
        }
        
        $consumer_key = $this->consumer_key;
        $consumer_secret = $this->consumer_secret;
        $oauth_access_token = $this->oauth_access_token;
        $oauth_access_token_secret = $this->oauth_access_token_secret;
        
        $oauth = array( 
            'oauth_consumer_key' => $consumer_key,
            'oauth_nonce' => time(),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_token' => $oauth_access_token,
            'oauth_timestamp' => time(),
            'oauth_version' => '1.0'
        );
        
        $getfield = $this->getGetfield();
        
        if (!is_null($getfield))
        {
            $getfields = str_replace('?', '', explode('&', $getfield));
            foreach ($getfields as $g)
            {
                $split = explode('=', $g);
                $oauth[$split[0]] = $split[1];
            }
        }
        
        $base_info = $this->buildBaseString($url, $requestMethod, $oauth);
        $composite_key = rawurlencode($consumer_secret) . '&' . rawurlencode($oauth_access_token_secret);
        $oauth_signature = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));
        $oauth['oauth_signature'] = $oauth_signature;
        
        $this->url = $url;
        $this->oauth = $oauth;
        
        return $this;
    }
    
    /**
     * Perform the acual data retrieval from the API
     * 
     * @param boolean $return If true, returns data.
     * 
     * @return json If $return param is true, returns json data.
     */
    public function performRequest($return = true)
    {
        if (!is_bool($return)) 
        { 
            throw new Exception('performRequest parameter must be true or false'); 
        }
        
        $header = array($this->buildAuthorizationHeader($this->oauth), 'Expect:');
        
        $getfield = $this->getGetfield();
        $postfields = $this->getPostfields();

        $options = array( 
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_HEADER => false,
            CURLOPT_URL => $this->url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false
        );

        if (!is_null($postfields))
        {
            $options[CURLOPT_POSTFIELDS] = $postfields;
        }
        else
        {
            if ($getfield !== '')
            {
                $options[CURLOPT_URL] .= $getfield;
            }
        }

        $feed = curl_init();
        curl_setopt_array($feed, $options);
        $json = curl_exec($feed);
        curl_close($feed);

        if ($return) { return $json; }
    }
    
    /**
     * Private method to generate the base string used by cURL
     * 
     * @param string $baseURI
     * @param string $method
     * @param string $params
     * 
     * @return string Built base string
     */
    private function buildBaseString($baseURI, $method, $params) 
    {
        $return = array();
        ksort($params);
        
        foreach($params as $key=>$value)
        {
            $return[] = "$key=" . $value;
        }
        
        return $method . "&" . rawurlencode($baseURI) . '&' . rawurlencode(implode('&', $return)); 
    }
    
    /**
     * Private method to generate authorization header used by cURL
     * 
     * @param array $oauth Array of oauth data generated by buildOauth()
     * 
     * @return string $return Header used by cURL for request
     */    
    private function buildAuthorizationHeader($oauth) 
    {
        $return = 'Authorization: OAuth ';
        $values = array();
        
        foreach($oauth as $key => $value)
        {
            $values[] = "$key=\"" . rawurlencode($value) . "\"";
        }
        
        $return .= implode(', ', $values);
        return $return;
    }

}
}

/*******************
 * Start main code *
 *******************/

/********************************************************************************************************************
 * Parameters
 * verbose =  {0,1} : print debug teksten
 * summary = {0,1} : 0 = display alle gegevens, 1 = display alleen summary van tweet (foto, naam, tweet) voor twitter_box
 * max = {0, N} : 0 = default aantal berichten (=50), N: display maximaal N berichten
 * stats = {0,N} : 0 = display geen tweet toppers, N: display de top N twiteraars
 ********************************************************************************************************************/

  /*
   * UDT PARAMETERS
   */
  $params['verbose'] = 0;
  $params['max'] = 200;
  $params['stats'] = 0;
   
   
error_reporting(0); // don't show error messages (sloppy and not usefull for visitors)

$verbose = $params["verbose"];
$summary_only = $params["summary"];
$max_tweets_to_display = $params["max"];
$show_statistics = $params["stats"];

$settings = array(
    'oauth_access_token' => "70978179-jeXKYh7QPE1t4jCUf396sg4iKKWjQ6q7yMN33Ih0",
    'oauth_access_token_secret' => "UxYabZk97IWtUktAWRwkFL55YkT5hLMP3j8fWhKzuk",
    'consumer_key' => "ALR8oLnyGxZGwls5nOVLQ",
    'consumer_secret' => "OQHttyl1KLMQCb4xcL41WeTc5D1UYZz7dKH7VTeHA"
);

if ($verbose) echo "<p>Twitter API 1.1 script...</p>";

//eerst verbinding maken met de database
$conn = mysql_connect('localhost', "bloemencor_db", 'Bl0emen!');
mysql_select_db ('bloemencor_db', $conn);

/*
 * Update de database met de recente tweets
 */
$url = 'https://api.twitter.com/1.1/search/tweets.json';
$twitter_query = "from:corsozundert OR corsozundert";
$getfield = '?q=from:corsozundert%20OR%20corsozundert&count=100';
$requestMethod = 'GET';
$twitter = new TwitterAPIExchange($settings);
$results = $twitter->setGetfield($getfield)
             ->buildOauth($url, $requestMethod)
             ->performRequest(); 
             
//print_r($results);
$results_xml = json_decode($results,1);
//print_r($results_xml);

$num_tweets = count($results_xml["statuses"]);
if ($verbose) echo "<p>Found " . $num_tweets . " recent tweets</p>";

foreach ($results_xml["statuses"] as $tweet)
{
      if ($verbose) echo '<p>';
      if ($verbose) echo $tweet["id_str"] . ': ' . $tweet["user"]["name"] . ' [' . $tweet["text"] . '] @ ' . $tweet["created_at"] .'<br>';

      if ($verbose)
      {
          $dt = new DateTime($tweet["created_at"]);
          $dt->setTimezone(new DateTimeZone('Europe/Amsterdam'));
          echo $dt->format('Y-m-d H:i:s') . '<br>';
      }
      
      //kijken of we deze tweet al gelogd hebben
      $sqlwrk = "Select tweet_id
      From tweetlog
      Where tweet_id=" . $tweet["id_str"];
      //nu het resultaat ophalen
      $res = mysql_query($sqlwrk) or die (mysql_error().$sqlwrk);
      $found =  mysql_num_rows($res);

      //exclude 'Corso Heraldieken', die retweeten alleen maar
      if (stristr(mysql_real_escape_string($tweet["user"]["name"]),"Heraldieken")) {
          $found = True;
      }
      
      if (!$found)
      {
          if ($verbose) echo 'Deze tweet zit nog niet in de database, dus nu toevoegen...';
          
          // format de datum zodat hij goed is voor MYSQL
          //$date_str = date_format( date_create($tweet["created_at"]), 'Y-m-d H:i:s');
          $dt = new DateTime($tweet["created_at"]);
          $dt->setTimezone(new DateTimeZone('Europe/Amsterdam'));
          $date_str = $dt->format('Y-m-d H:i:s') . '<br>';

          // insert deze tweet in de database
          $sqlwrk = "Insert Into tweetlog
          (user_id, user_name, user_image, tweet_id, text, to_user_id, date, source)
          Values ('" . $tweet["user][id_str"] . "', '" . mysql_real_escape_string($tweet["user"]["name"]) . "', '" .
          mysql_real_escape_string($tweet["user"]["profile_image_url"]) . "', '" . $tweet["id_str"] . "', '" .
          mysql_real_escape_string($tweet["text"]) . "', '" . $tweet["in_reply_to_user_id"] . "', '" .
          $date_str . "', '" . mysql_real_escape_string($tweet["source"]) . "')";

          $res = mysql_query($sqlwrk) or die (mysql_error().$sqlwrk);

          if ($verbose) {
              if ($res) echo 'gelukt.';
              else echo 'mislukt.';
          }
      }
      else
      {
          // geen actie nodig, deze tweet zit al in de database
          if ($verbose) echo 'Deze tweet zit al in de database.';
      }


      if ($verbose) echo '</p>';
}
  
if ($verbose) echo '<hr>';
  
  
  /*
   * Display de tweets uit de database
   */

  if ($verbose) echo '<p>Display alle tweets uit de database</p>';

  if ($max_tweets_to_display == 0) $limit_str = " Limit 50"; //voor nu alleen de laatste 50 tweets, kan later met pagina's....
  else $limit_str = " Limit " . $max_tweets_to_display;

  $sqlwrk = "Select * From tweetlog Order By date Desc" . $limit_str;
  $res = mysql_query($sqlwrk) or die (mysql_error().$sqlwrk);
  $found =  mysql_num_rows($res);

  if ($verbose) echo $found . " tweets";

  if ($summary_only == 0) {
    if ($show_statistics) echo '<div id="tweets" style="border-right: 1px solid gray;">';
    else echo '<div id="tweets">';
  }

  while ($tweet = mysql_fetch_array($res)) {
     echo '<div class="tweet">';
     if ($max_tweets_to_display>1) {
       echo '  <a href="http://twitter.com/' . $tweet["user_name"] . '" rel="external" target="_new"><div class="picture"><img src="' . $tweet["user_image"] . '" alt="' . $tweet[user_name] . '" width="48px"/></div></a>';
     }
// NBRA: converteer http:// in tweets in a hrefs (target = _new)
//           (ereg_replace code gevonden op http://www.liamdelahunty.com/tips/php_convert_url_to_link.php)
// OUD:     echo '  <p><strong><a href="http://twitter.com/' . $tweet["user_name"] . '" rel="external" target="_new">' . $tweet["user_name"] . '</a></strong> ' . $tweet["text"] . '</p>';
     $linkedtweet = ereg_replace("[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]","<a href=\"\\0\" rel=\"external\" target=\"_new\">\\0</a>", $tweet["text"]);
// Ook @username en #tag link van maken
// Tijdelijk uitgeschakeld omdat site traag is? (deze ereg_replace doen als tweets in database gezet worden?)
     $linkedtweet = ereg_replace("@[-_[:alnum:]]+","<a href=\"http://twitter.com/\\0\" rel=\"external\" target=\"_new\">\\0</a>", $linkedtweet);
     $linkedtweet = ereg_replace("#([-_[:alnum:]]+)","<a href=\"http://twitter.com/#!/search?q=\\1\" rel=\"external\" target=\"_new\">\\0</a>", $linkedtweet);
     echo '  <p><strong><a href="http://twitter.com/' . $tweet["user_name"] . '" rel="external" target="_new">' . $tweet["user_name"] . '</a></strong> ' . $linkedtweet . '</p>';
     if ($summary_only==0) {
         $date = new DateTime($tweet["date"]);
         echo '<p>op ' . $date->format("d-m-Y") . ' om ' . $date->format("H:i") . ' via ' . htmlspecialchars_decode($tweet["source"]) . '</p>';
     }
     echo '  <div style="clear: right"></div>';
     echo '</div>';
  }
  if ($summary_only == 0) echo '</div>';


  if ($show_statistics)
  {
      // laat de top drie twitteraars zien
      // NBRA: top 3 --> top $show_statistics (instelbaar, 0 = geen stats werkt nog steeds)
      echo '<div id="tweet_stats">';
      echo '<h1>Tweet Toppers</h1>';

      // nu wat MYSQL magic om de top 3 twitteraars te vinden....
      $sqlwrk = "SELECT user_name, user_image, Count(*) As cnt
      From tweetlog Where user_name <> 'BSMolenstraat' Group By user_name Having cnt >=1 Order By cnt Desc Limit " . $show_statistics;
      $res = mysql_query($sqlwrk) or die (mysql_error().$sqlwrk);

      while ($row = mysql_fetch_array($res)) {
          echo '<div class="user">';
          echo '  <a href="http://twitter.com/' . $row["user_name"] . '" rel="external" target="_new"><div class="picture"><img src="' . $row["user_image"] . '" alt="' . $row[user_name] . '" width="48px"/></div></a>';
          echo '  <p><strong><a href="http://twitter.com/' . $row["user_name"] . '" rel="external" target="_new">' . $row["user_name"] . '</a> : ' . $row["cnt"] . '</strong></p>';
          echo '</div>';
      }

      echo '</div>';
  }

  echo '<div id="clearfloat"></div>';
  
  mysql_close($conn);
?>
