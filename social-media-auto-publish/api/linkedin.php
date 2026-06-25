<?php


class SMAPOAuth2{
    protected $access_token;
    protected $access_token_url;
    protected $authorize_url;
    protected $access_token_name;
    public $error;
    function __construct($access_token=''){
        $this->access_token = $access_token;
        $this->error = "";
        $this->access_token_name='access_token';
    }
    public function getAuthorizeUrl($client_id,$redirect_url, $additional_args=array() ){
        $auth_link = $this->authorize_url.
                    "?response_type=code".
                    "&client_id=".$client_id.
                    "&redirect_uri=".urlencode($redirect_url);
        foreach($additional_args as $k=>$v){
            $auth_link.='&'.$k.'='.urlencode($v);
        }
        return $auth_link;
    }
    public function getAccessToken($client_id="", $secret="", $redirect_url="", $code = ""){
        if($code==""){
            $code = isset($_REQUEST['code'])?$_REQUEST['code']:"";
        }
        $params=array();
        $params['url'] = $this->access_token_url;
        $params['method']='post';
        $params['args']=array(  'code'=>$code,
                                'client_id'=>$client_id,
                                'redirect_uri'=>$redirect_url,
                                'client_secret'=>$secret,
                                'grant_type'=>'authorization_code');
        $result = $this->makeRequest($params);
        return $result;
    }
    protected function makeRequest($params=array()){
        $this->error = '';
        $method=isset($params['method'])?$params['method']:'get';
        $headers = isset($params['headers'])?$params['headers']:array();
        $args = isset($params['args'])?$params['args']:'';
        $url = $params['url'];
        $url.='?';
        if($method=='get'){
            $url.='&'.$this->preparePostFields($args);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        if($method=='post'){
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->preparePostFields($args));
        }elseif($method=='delete'){
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        }elseif($method=='put'){
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        }
        if(is_array($headers) && !empty($headers)){
            $headers_arr=array();
            foreach($headers as $k=>$v){
                $headers_arr[]=$k.': '.$v;
            }
            curl_setopt($ch,CURLOPT_HTTPHEADER,$headers_arr);
        }
        $sslverify=(get_option('xyz_smap_peer_verification')=='1') ? TRUE : FALSE;
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,$sslverify);
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result,true);
    }
    protected function makeRequest_ln_header($params=array()){
        $this->error = '';
        $method=isset($params['method'])?$params['method']:'get';
        $headers = isset($params['headers'])?$params['headers']:array();
        $args = isset($params['args'])?$params['args']:'';
        $url = $params['url'];
        $url.='?';
        if($method=='get'){
            $url.='&'.$this->preparePostFields($args);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        curl_setopt($ch, CURLOPT_HEADER,true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);

        if($method=='post'){
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->preparePostFields($args));
        }elseif($method=='delete'){
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        }elseif($method=='put'){
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        }
        if(is_array($headers) && !empty($headers)){
            $headers_arr=array();
            foreach($headers as $k=>$v){
                $headers_arr[]=$k.': '.$v;
            }
            curl_setopt($ch,CURLOPT_HTTPHEADER,$headers_arr);
        }
        $sslverify=(get_option('xyz_smap_peer_verification')=='1') ? TRUE : FALSE;
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,$sslverify);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    protected function makeRequestToAssetAPI($params=array()){
    	$this->error = '';
    	$method=isset($params['method'])?$params['method']:'get';
    	$headers = isset($params['headers'])?$params['headers']:array();
    	$args = isset($params['args'])?$params['args']:'';
    	$url = $params['url'];
    	if($method=='get'){
    		$url.='&'.$this->preparePostFields($args);
    	}
    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    	if($method=='post'){
    		curl_setopt($ch, CURLOPT_POST, TRUE);
    		if (isset($params['smap']))
    		{
    			$url.='?';
    			curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
    		}
    		else
    			curl_setopt($ch, CURLOPT_POSTFIELDS, $this->preparePostFields($args));
    	}elseif($method=='delete'){
    		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    	}elseif($method=='put'){
    		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    	}
    	if(is_array($headers) && !empty($headers)){
    		$headers_arr=array();
    		foreach($headers as $k=>$v){
    			$headers_arr[]=$k.': '.$v;
    		}
    		curl_setopt($ch,CURLOPT_HTTPHEADER,$headers_arr);
    	}
    	$sslverify=(get_option('xyz_smap_peer_verification')=='1') ? TRUE : FALSE;
    	curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,$sslverify);
    	$result = curl_exec($ch);
    	curl_close($ch);
    	return $result;
    }
    protected function preparePostFields($array) {
        if(is_array($array)){
            $params = array();
            foreach ($array as $key => $value) {
                $params[] = $key . '=' . urlencode($value);
            }
            return implode('&', $params);
        }else{
            return $array;
        }
    }

}
class SMAPLinkedInOAuth2 extends SMAPOAuth2 {
	public function __construct($access_token=''){
		$this->access_token_url = "https://www.linkedin.com/oauth2/accessToken";
		$this->authorize_url = "https://www.linkedin.com/oauth2/authorization";
		parent::__construct($access_token);
		$this->access_token_name='oauth2_access_token';
	}
	public function getAuthorizeUrl($client_id,$redirect_url,$scope=''){
		$additional_args = array();
		if($scope!=''){
			if(is_array($scope)){
				$additional_args['scope']=implode(" ",$scope);
				$additional_args['scope'] = $additional_args['scope'];
			}else{
				$additional_args['scope'] = $scope;
			}
		}
		$additional_args['state'] = md5(time());
		return parent::getAuthorizeUrl($client_id,$redirect_url,$additional_args);
	}
	public function getAccessToken($client_id="", $secret="", $redirect_url="", $code = ""){
		$result = parent::getAccessToken($client_id, $secret, $redirect_url, $code);
		$result = json_decode($result,true);
		if(isset($result['error'])){
			$this->error = $result['error'].' '.$result['error_description'];
			return false;
		}else{
			$this->access_token = $result['access_token'];
			return $result;
		}
	}

	public function getImagePostResponses($args=array()){
		$params['url'] = "https://api.linkedin.com/rest/images?action=initializeUpload";
		$params['method']='post';
		$params['headers']['Authorization']='Bearer '.$this->access_token;
		$params['headers']['Content-Type']='application/json';//application/binary
    $params['headers']['Linkedin-Version']=XYZ_SMAP_LINKEDIN_VERSION;
		$params['headers']['X-Restli-Protocol-Version']='2.0.0';
		$params['headers']['Connection']='Keep-Alive';
		$params['args']=json_encode($args);
		$result =  $this->makeRequestToAssetAPI($params);
		return json_decode($result,true);
	}
  public function getVideooPostResponses($args = array()){
	    $params['url'] = "https://api.linkedin.com/rest/videos?action=initializeUpload";
	    $params['method']='post';
	    $params['headers']['Authorization']='Bearer '.$this->access_token;
	    $params['headers']['Content-Type']='application/json';//application/binary
      $params['headers']['Linkedin-Version']=XYZ_SMAP_LINKEDIN_VERSION;
	    $params['headers']['X-Restli-Protocol-Version']='2.0.0';
	    $params['headers']['Connection']='Keep-Alive';
	    $params['args']=json_encode($args);
	    $result =  $this->makeRequestToAssetAPI($params);
	    return json_decode($result,true);
	}
	public function com_multipart_upload($args = array()){
	    $params['url'] = "https://api.linkedin.com/v2/assets?action=completeMultiPartUpload";
	    $params['method']='post';
	    $params['headers']['Authorization']='Bearer '.$this->access_token;
	    $params['headers']['Content-Type']='application/json';//application/binary
	    $params['headers']['X-Restli-Protocol-Version']='2.0.0';
	    $params['headers']['Connection']='Keep-Alive';
	    $params['args']=json_encode($args);
	    $result =  $this->makeRequestToAssetAPI($params);
	    return json_decode($result,true);
	}
  public function getUploadUrlResponses($uploadUrl,$image,$args=array())
	{
		$headers = array();
		$sslverify=(get_option('xyz_smap_peer_verification')=='1') ? TRUE : FALSE;
		$response = wp_remote_get(
		$image,
		array(
			'sslverify' => $sslverify,
			'timeout'   => 60,
		)
		);
		if (is_wp_error($response)) {
			return $response;
		}
		$image_data = wp_remote_retrieve_body($response);
		if (empty($image_data)) {
			return 'Image download returned empty content.';
		}
		$headers[] = 'Authorization: Bearer '.$this->access_token;// token generated above code
		$headers[] = 'LinkedIn-Version:'.XYZ_SMAP_LINKEDIN_VERSION;
		$headers[] = 'X-Restli-Protocol-Version: 2.0.0';
		$headers[] = 'Content-Type: application/octet-stream';
		$ch = curl_init($uploadUrl);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
 	    curl_setopt($ch, CURLOPT_HEADER,true);
	    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,$sslverify);
		curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $image_data);
		$response = curl_exec($ch);
 	    $header_data= curl_getinfo($ch);
		curl_close($ch);
		return json_decode($response,true);
	}
	public function getUploadvidUrlResponses($uploadUrl,$vid,$args=array())
	{
	    $headers = array();
     	$headers[] = 'LinkedIn-Version:'.XYZ_SMAP_LINKEDIN_VERSION;
	    $headers[] = 'X-Restli-Protocol-Version: 2.0.0';
      	$headers[] = 'Content-Type: application/octet-stream';
	    $ch = curl_init($uploadUrl);
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 130);
	    curl_setopt($ch,CURLOPT_USERAGENT,'curl/7.35.0');
	    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
      curl_setopt($ch, CURLOPT_HEADER,true);//To get html output
	    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    $sslverify=(get_option('xyz_smap_peer_verification')=='1') ? TRUE : FALSE;
	    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,$sslverify);
	    curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents($vid));
		$response = curl_exec($ch);
		$header_data= curl_getinfo($ch);
		curl_close($ch);
		return $response;
	}
  public function FinalizeUpload($args = array()){
      $params['url'] = "https://api.linkedin.com/rest/videos?action=finalizeUpload";
      $params['method']='post';
      $params['headers']['Authorization']='Bearer '.$this->access_token;
      $params['headers']['Content-Type']='application/json';//application/binary
      $params['headers']['Linkedin-Version']=XYZ_SMAP_LINKEDIN_VERSION;
      $params['headers']['X-Restli-Protocol-Version']='2.0.0';
      $params['headers']['Connection']='Keep-Alive';
      $params['args']=json_encode($args,JSON_UNESCAPED_SLASHES);
      $result =  $this->makeRequestToAssetAPI($params);
      return json_decode($result,true);
  }
public function check_status_linkedin_asset($url) {
    $repeatCount = 0;
    $maxRetries = 5;
    $final_result = array();

    $headers_arr = array(
        'Content-Type: application/json',
        'x-li-format: json',
        'Connection: Keep-Alive',
        'Linkedin-Version: ' . XYZ_SMAP_LINKEDIN_VERSION,
        'X-RestLi-Protocol-Version: 2.0.0',
        'Authorization: Bearer ' . $this->access_token
    );

    $sslverify = (get_option('xyz_smap_peer_verification') == '1') ? TRUE : FALSE;

    // 1. Initialize and set options once
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers_arr);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $sslverify);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);

    // 2. Start the polling loop just before execution
    do {
        $result = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        // Check for cURL transport errors
        if (curl_errno($ch)) {
            $final_result = array('error' => curl_error($ch));
        } else {
            $final_result = json_decode($result, true);
        }
        $current_status = '';
        if (isset($final_result['status'])) {
            $current_status = $final_result['status'];
        }
        if ($current_status === "AVAILABLE") {
            return $final_result;
        }
        if ($current_status === "FAILED" || $current_status === "CANCELED" || $http_code == 401) {
            return $final_result;
        }
        $repeatCount++;
        if ($repeatCount < $maxRetries) {
            sleep(3); 
        }
    } while ($repeatCount < $maxRetries);
    curl_close($ch);
    return $final_result;
}
      public function shareStatus($args=array()){
    		$params['url'] = 'https://api.linkedin.com/rest/posts';
    		$params['method']='post';
    		$params['headers']['Authorization']='Bearer '.$this->access_token;
    		$params['headers']['Content-Type']='application/json';
    		$params['headers']['x-li-format']='json';
        $params['headers']['Linkedin-Version']=XYZ_SMAP_LINKEDIN_VERSION;
    		$params['headers']['X-Restli-Protocol-Version']='2.0.0';
    		$params['headers']['Connection']='Keep-Alive';
    		$params['args']=json_encode($args,JSON_UNESCAPED_SLASHES);
    		$result =  $this->makeRequest_ln_header($params);
        return $result;
    			}
	  public function xyz_smap_fetch_user_data($user_data_endpoint_url){
    $params['url'] = $user_data_endpoint_url;
		$params['method']='get';
		$params['headers']['Authorization']='Bearer '.$this->access_token;
		$params['headers']['Content-Type']='application/json';
		$params['headers']['x-li-format']='json';
		$params['headers']['Connection']='Keep-Alive';
		$params['headers']['X-RestLi-Protocol-Version']='2.0.0';
		$result =  $this->makeRequest($params);
		$this->error = '';
		$method=isset($params['method'])?$params['method']:'get';
		$headers = isset($params['headers'])?$params['headers']:array();
		$args = isset($params['args'])?$params['args']:'';
		$url = $params['url'];
		$url.='?';
		if($this->access_token){
			$url .'oauth2_access_token='.$this->access_token;
			}
		if($method=='get'){
			$url.='&'.$this->preparePostFields($args);
			}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		if($method=='post'){
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $this->preparePostFields($args));
		}elseif($method=='delete'){
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
		}elseif($method=='put'){
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
			}
		if(is_array($headers) && !empty($headers)){
			$headers_arr=array();
			foreach($headers as $k=>$v){
				$headers_arr[]=$k.': '.$v;
		}
			curl_setopt($ch,CURLOPT_HTTPHEADER,$headers_arr);
		}
		$result = curl_exec($ch);
		curl_close($ch);
		return json_decode($result,true);
	}
}
?>
