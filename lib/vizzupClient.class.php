<?php
/**
*	Class VizzupClient
*	pour API Vizzup
*	PHP 5
*
* @package API Vizzup 1.0
* @author Lionel Guichard
* @version 0.2 (2009/03)
*
*/
class vizzupClient {
	
	var $api_key;
	var $user;
	var $server_addr;
	var $user_token;
	var $user_id;
	var $response;
	var $response_xml;
	var $endpoint;
	var $debug = false;
	var $cache_apc = false;
	var $cache_ttl = 300;
	
	public function __construct($api_key)
	{
		$this->server_addr  = 'http://api.vizzup.com/';
		$this->user_token   = false;
		$this->user_id      = false;
		$this->response     = false;
		$this->response_xml = false;
	}
	
	public function setEndPoint($endpoint)
	{
		$this->server_addr = $endpoint;
	}
		
	public function setDebug()
	{
		return $this->debug = true;
	}
	
	public function getResponse()
	{
		return $this->response;
	}
	
	public function getXmlResponse()
	{
		return $this->response_xml;
	}
	
	public function getUserToken()
	{
		return $this->user_token;
	}
	
	public function getUserId()
	{
		return $this->user_id;
	}
	
	public function setCache($bool)
	{
		return $this->cache_apc = $bool;
	}
	
	public function setAuthenticationSso($sso)
	{
		$result = $this->auth_getTokenSso($sso);
		$this->user_token = $result['user_token'];
		$this->user_id = $result['user_id'];
		if($this->user_token)
		{
			return $this->user_id;
		}
		
		return false;
	}
	
	public function setAuthentication($username, $password)
	{
		$result = $this->auth_getToken($username, $password);
		$this->user_token = $result['user_token'];
		$this->user_id = $result['user_id'];
		if($this->user_token)
		{
			return $this->user_token;
		}
		
		return false;
	}
	
	public function setLogout()
	{
		if($this->auth_destroyToken($this->user_token))
		{
			$this->user_token = null;
			$this->user_id = null;
			return true;
		}
		
		return false;
	}
	
	/**
	* Auth method
	*
	*/
	public function auth_getToken($username, $password, $sso = '')
	{
		$api_sig = md5($username.md5($password));
		return $this->call_method('vizzup.auth.getToken', array('api_sig' => $api_sig));
  	}

	public function auth_getTokenSso($sso)
	{
    	$api_sig = md5($sso.'method.auth.getToken'.$this->api_key);
		return $this->call_method('vizzup.auth.getToken', array('api_sig' => $api_sig));
  }

	public function auth_checkToken($user_token)
	{
    	return $this->call_method('vizzup.auth.checkToken', array('user_token' => $user_token));
  }

	public function auth_destroyToken($user_token)
	{
    	return $this->call_method('vizzup.auth.destroyToken', array('user_token' => $user_token));
  }
	
	/**
	* User method
	*
	*/
	public function user_getInfo($user_id)
	{
    	return $this->call_method('vizzup.user.getInfo', array('user_id' => $user_id));
	}
	
	public function user_setInfo($data)
	{
    	return $this->call_method('vizzup.user.setInfo', array('data' => json_encode($data)));
	}
	
	public function user_create($data, $sso = '')
	{
    	return $this->call_method('vizzup.user.create', array('data' => json_encode($data), 'sso' => $sso));
	}
	
	public function user_delete()
	{
    	return $this->call_method('vizzup.user.delete', array());
	}
	
	public function user_getVideos($user_id, $limit = false, $page = 1, $since = false)
	{
    	return $this->call_method('vizzup.user.getVideos', array('user_id' => $user_id, 'limit' => $limit, 'page' => $page, 'since' => $since));
	}
	
	public function user_getFeaturedVideos($user_id, $limit = false, $page = 1, $since = false)
	{
    	return $this->call_method('vizzup.user.getFeaturedVideos', array('user_id' => $user_id, 'limit' => $limit, 'page' => $page, 'since' => $since));
	}
	
	public function user_getThreads($user_id, $limit = false, $page = 1, $since = false)
	{
    	return $this->call_method('vizzup.user.getThreads', array('user_id' => $user_id, 'limit' => $limit, 'page' => $page, 'since' => $since));
	}
	public function user_search($query = '', $order_by = '', $sort = '', $limit = '', $page = '', $since = '')
	{
    	return $this->call_method('vizzup.user.search', array('query' => json_encode($query), 'order_by' => $order_by, 'sort' => $sort, 'limit' => $limit, 'page' => $page, 'since' => $since));
	}
	
	/**
	* Videos method
	*
	*/
	public function video_getInfo($video_id)
	{
    	return $this->call_method('vizzup.video.getInfo', array('video_id' => $video_id));
	}
	
	public function video_setInfo($video_id, $data)
	{
    	return $this->call_method('vizzup.video.setInfo', array('video_id' => $video_id, 'data' => json_encode($data)));
	}
	
	public function video_delete($video_id)
	{
    	return $this->call_method('vizzup.video.delete', array('video_id' => $video_id));
	}
	
	public function video_getFromThread($video_id)
	{
    	return $this->call_method('vizzup.video.getFromThread', array('video_id' => $video_id));
	}
	
	public function video_addTag($video_id, $tag)
	{
    	return $this->call_method('vizzup.video.addTag', array('video_id' => $video_id, 'tag' => $tag));
	}
	
	public function video_deleteTag($video_id, $tag_id)
	{
    	return $this->call_method('vizzup.video.deleteTag', array('video_id' => $video_id, 'tag_id' => $tag_id));
	}
	
	public function video_deleteAllTags($video_id)
	{
    	return $this->call_method('vizzup.video.deleteAllTags', array('video_id' => $video_id));
	}
	
	public function video_getTags($video_id)
	{
    	return $this->call_method('vizzup.video.getTags', array('video_id' => $video_id));
	}
	
	public function video_upload($file = false, $upload_token = false, $upload_progress_key = false)
	{
		if(!$upload_progress_key) $upload_progress_key = md5(uniqid());
		if($file) $file = $file;
		if(empty($_FILES['file']['tmp_name'])) $file = false; else $file = $_FILES['file']['tmp_name'];
    	return $this->call_method('vizzup.video.upload', array('file' => '@'.$file, 'upload_token' => $upload_token, 'upload_progress_key' => $upload_progress_key));
	}
	
	public function video_create($upload_token, $title, $description, $reply_to_video_id = false)
	{
    	return $this->call_method('vizzup.video.create', array('upload_token' => $upload_token, 'title' => $title, 'description' => $description, 'reply_to_video_id' => $reply_to_video_id));
	}
	
	public function video_getPlayerCode($video_id, $size = 'middle', $allow_navigation = true)
	{
    	return $this->call_method('vizzup.video.getPlayerCode', array('video_id' => $video_id, 'size' => $size, 'allow_navigation' => $allow_navigation));
	}
	
	public function video_getRecorderCode($size = 'middle')
	{
    	return $this->call_method('vizzup.video.getRecorderCode', array('size' => $size));
	}
	
	public function video_getUploadToken()
	{
    	return $this->call_method('vizzup.video.getUploadToken', array());
	}
	
	public function video_checkUploadToken($upload_token = false)
	{
    	return $this->call_method('vizzup.video.checkUploadToken', array('upload_token' => $upload_token));
	}
	
	public function video_getUploadStatus($upload_token = false)
	{
    	return $this->call_method('vizzup.video.getUploadStatus', array('upload_token' => $upload_token));
	}
	
	public function video_getUploadProgress($upload_token = false, $upload_progress_key = false)
	{
    	return $this->call_method('vizzup.video.getUploadProgress', array('upload_token' => $upload_token, 'upload_progress_key' => $upload_progress_key));
	}
	
	public function video_search($query = '', $order_by = '', $sort = '', $limit = '', $page = '', $since = '')
	{
    	return $this->call_method('vizzup.video.search', array('query' => json_encode($query), 'order_by' => $order_by, 'sort' => $sort, 'limit' => $limit, 'page' => $page, 'since' => $since));
	}
	

	public function call_method($method, $params)
	{
		$xml = $this->post_request($method, $params);
		
		$result = $this->convert_xml_to_result($xml, $method, $params);
		
		if(is_array($result) && isset($result['error_code']))
		{
			if($this->debug)
				echo 'Error : '.$result['error_code'].' '.$result['error_msg'];
		}
		
		$this->response = $result;

		return $result;
	}
	
	private function create_post_string($method, $params) 
	{
    	$method = explode('.',$method);
    	$params['api_key'] = $this->api_key;
			if($this->user_token)
				$params['user_token'] = $this->user_token;
			if(!$this->cache_apc)
    		$params['call_id'] = microtime(true);
    	$post_params = array();

    	return $params;
  }

	private function create_method_uri($method)
	{
		$method = explode('.',$method);
		$uri = $this->server_addr.$method[1].'/'.$method[2];
		
		return $uri;
	}
	
	public function curlopen($request) 
	{
		$useragent = 'Vizzup API PHP5 Client 1.0 (curl) ' . phpversion();
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $request);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 100);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		// Fix bug if use lighttpd
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Expect:"));
		curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
		$results = curl_exec ($ch);
		curl_close($ch);
		return $results;
	}
	
	public function post_request($method, $params)
	{
		$url_method = $this->create_method_uri($method);
		$post_data = $this->create_post_string($method, $params);
		
		$post_params = array();
    foreach ($post_data as $key => &$val) {
     	if (is_array($val)) $val = implode(',', $val);
     	$post_params[] = $key.'='.urlencode($val);
    }
		$url = $url_method.'?'.implode('&', $post_params);
		
    if (function_exists('curl_init')) 
    {
    	// Use CURL if installed...
    	if($this->cache_apc && function_exists('apc_fetch'))
    	{
      	// Use if cache apc enabled...
     		if(!$doc = apc_fetch($url)) 
     		{
					$doc = $this->curlopen($url);
					if ($doc === false) return false;
					apc_store($url,$doc, $ttl);
				}
				$result = $doc;
			}
			else
			{
				// Use if cache apc disabled...
				$result = $this->curlopen($url);
			}
    }
		else
		{
			// Non-CURL based version...
			$content_type = 'application/x-www-form-urlencoded';
			$user_agent = 'Vizzup API PHP5 Client 1.0 (non-curl) '.phpversion();
			$context =
				array('http' =>
              	array('method' => 'POST',
                    'header' => 'Content-type: '.$content_type."\r\n".
                                'User-Agent: '.$user_agent."\r\n".
                                'Content-length: ' . strlen($post_data),
                    'content' => $post_data));
			$contextid=stream_context_create($context);
			$sock = fopen($url_method, 'r', false, $contextid);
			if ($sock)
			{
				$result='';
				while (!feof($sock))
					$result.=fgets($sock, 4096);
				fclose($sock);
      }
    }
    	
    $this->response_xml = $result;
    	
    return $result;
	}

	private function convert_xml_to_result($xml, $method, $params) {
		//$sxml = simplexml_load_string(utf8_encode($xml));
		$sxml = simplexml_load_string($xml);
    	$result = self::convert_simplexml_to_array($sxml);
    	return $result;
  	}

	public static function convert_simplexml_to_array($sxml) {
		$arr = array();
		if($sxml)
		{
			foreach($sxml as $k => $v)
			{
        if(isset($arr[$k]))
				{
          			$arr[$k."_".(count($arr) + 1)] = self::convert_simplexml_to_array($v);
        }
				else
				{
          			$arr[$k] = self::convert_simplexml_to_array($v);
        		}
      		}
    	}
    	if(sizeof($arr) > 0)
		{
      		return $arr;
    	}
		else
		{
      		return (string)$sxml;
    	}
  	}
}