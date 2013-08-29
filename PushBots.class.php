<?php
/**
 * PushBots Library
 *
 * @category  Library
 * @author    Abdullah Diaa [@Abdullahdiaa]
 */
class PushBots
{
	private $appId ;
	private $appSecret ;
	private $pushData ;
	private $aliasData;
	private $deviceToken;
	public $timeout = 0; 
	public $connectTimeout = 0;
	public $sslVerifypeer = 0; 

	public function __construct() {
		//set Default Push values
		$this->pushData['msg'] = "Notification Message";
		$this->pushData['badge'] = "+1";
		$this->pushData['sound'] = "ping.aiff";
	}
	
	/**
	 * @param	string	$appId			PushBots Applciation Id.
	 * @param	string	$appSecret	PushBots Application Secret.
	 */
	public function App($appId, $appSecret) {
		$this->appId = $appId;
		$this->appSecret = $appSecret;
	}
	
	/**
	 * sendRequest
	 * @param	string	$host	PushBots API.
	 * @param	string	$path	API Path.
	 */
	private function sendRequest($method, $host, $path, $data) {
		$jsonData = json_encode($data);
		echo $jsonData;
        $ci = curl_init();
		
		//PushBots Headers
		$headers = array(
			'X-PUSHBOTS-APPID:' . $this->appId,
			'X-PUSHBOTS-SECRET:' . $this->appSecret,
			'Content-Type: application/json',
			'Content-Length: ' . strlen($jsonData)
		);
		
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this->connectTimeout); 
        curl_setopt($ci, CURLOPT_TIMEOUT, $this->timeout); 
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE); 
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $this->sslVerifypeer); 
		curl_setopt($ci, CURLOPT_HTTPHEADER, $headers );
		curl_setopt($ci, CURLOPT_HEADER, FALSE); 

        switch ($method) { 
        case 'POST': 
            curl_setopt($ci, CURLOPT_POST, TRUE); 
            if (!empty($jsonData)) { 
                curl_setopt($ci, CURLOPT_POSTFIELDS, $jsonData); 
            }
		break;
        case 'PUT':
            curl_setopt($ci, CURLOPT_CUSTOMREQUEST, "PUT");
            if (!empty($jsonData)) { 
                curl_setopt($ci, CURLOPT_POSTFIELDS, $jsonData); 
            }
		break;
        case 'DELETE': 
            curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE'); 
            if (!empty($jsonData)) { 
                $url = "{$url}?{$jsonData}"; 
            }
		break;
        }
		
        curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE ); 
        curl_setopt($ci, CURLOPT_URL, $host . $path); 

        $content = curl_exec($ci); 
		
		$response = curl_getinfo($ci); 
		if($response['http_code'] != 200) { 
			$res['status'] = 'ERROR';
			$res['code'] = $response['http_code'];
			$res['data'] = $response['msg'];
		}else{
			$res['status'] = 'OK';
			$res['code'] = $response['http_code'];
			$res['data'] = $content;
		}
		
        curl_close ($ci);
        return $res; 
		
	}

	/**
	 * Push Notification 
	 */
	 
	public function Push() {
		$response = $this->sendRequest( 'POST' ,'https://api.pushbots.com', '/push/all', $this->pushData);
		return $response;
	}
	
	
	/**
	 * Update Device Alias
	 */
	
	public function setAlias() {
		$response = $this->sendRequest( 'PUT' ,'https://api.pushbots.com', '/alias' , $this->aliasData);
		return $response;
	}

	/**
	 * set Platforms
	 * @param	array	$platform	Platforms array 0=>iOS , 1=>Android
	 */
	public function Platform($platform) {
		if(is_array($platform) != true){
			$platform = array($platform);
		}
		$this->pushData['platform'] = $platform;
	}
	
	public function Alert($alert) {
		$this->pushData['msg'] = $alert;
	}

	public function Badge($badge) {
		$this->pushData['badge'] = $badge;
	}
	
	public function Sound($sound) {
		$this->pushData['sound'] = $sound;
	}
	
	public function Alias($alias) {
		$this->pushData['alias'] = $alias;
	}
	
	public function exceptAlias($alias) {
		$this->pushData['except_alias'] = $alias;
	}
	
	/**
	 * set Tags
	 * @param	array	$tags	Tags Array.
	 */
	 
	public function Tags($tags) {
		if(is_array($tags) != true){
			$tags = array($tags);
		}
		if(count($tags) > 0){
			$this->pushData['tags'] = $tags;
		}
	}
	
	/**
	 * set Alias Data
	 * @param	integer	$platform 0=> iOS or 1=> Android.
	 * @param	String	$token Device Registration ID.
	 * @param	String	$alias New Alias.
	 */
	 
	public function AliasData($platform, $token, $alias) {
			$this->aliasData['platform'] = $platform;
			$this->aliasData['token'] = $token;
			$this->aliasData['alias'] = $alias;
	}
	
	
	/**
	 * set Payload
	 * @param	array	$payload	Custom fields Array.
	 */
	 
	public function Payload($customfields) {
		if(is_array($customfields) != true){
			$customfields = array($customfields);
		}
		if(count($customfields) > 0){
			$this->pushData['payload'] = $customfields;
		}
	}
	
	/**
	 * set Geolocation Data
	 * @param	string	$country	Country.
	 * @param	string	$gov	Governorate or State.
	 */
	 
	public function Geo($country , $gov=null) {
		$this->pushData["geo"] = array();
			
		if($country)
			$this->pushData["geo"]["country"] = $country;
			
		if($gov)
			$this->pushData["geo"]["gov"] = $gov;
		
	}
	
}
