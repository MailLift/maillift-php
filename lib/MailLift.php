<?php

define('MAILLIFT_API_VERSION',		'2013-08-17');
define('MAILLIFT_API_HOST', 		'api.maillift.com'); 

define('MAILLIFT_REQUEST_POST', 		'POST'); 
define('MAILLIFT_REQUEST_GET', 			'GET'); 
define('MAILLIFT_REQUEST_PUT', 			'PUT'); 
define('MAILLIFT_REQUEST_DELETE',		'DELETE'); 

class MailLift { 

	private static $username; 
	private static $apiKey; 

	private static $curlHandle; 


	public function init($username, $apiKey) { 
		self::$username 	= $username; 
		self::$apiKey 		= $apiKey; 

		self::$curlHandle = curl_init(); 
	}


	public function BuildUrl($object) { 
		return 'https://' . MAILLIFT_API_HOST . '/' . MAILLIFT_API_VERSION . '/' . $object; 
	}

	public function Request($requestMethod, $url, $payload=null) { 

		if(strstr(MAILLIFT_API_HOST, 'dev') !== false) {
			curl_setopt(self::$curlHandle, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt(self::$curlHandle, CURLOPT_SSL_VERIFYHOST, false);
		} else {
			curl_setopt(self::$curlHandle, CURLOPT_SSL_VERIFYPEER, true);
			curl_setopt(self::$curlHandle, CURLOPT_SSL_VERIFYHOST, false);
		}

		curl_setopt(self::$curlHandle, CURLOPT_URL, $url); 
		curl_setopt(self::$curlHandle, CURLOPT_CUSTOMREQUEST, $requestMethod); 
		curl_setopt(self::$curlHandle, CURLOPT_RETURNTRANSFER, true); 
		curl_setopt(self::$curlHandle, CURLOPT_USERPWD, self::$username . ':' . self::$apiKey);

		if($requestMethod == MAILLIFT_REQUEST_POST || $requestMethod == MAILLIFT_REQUEST_PUT) { 
			curl_setopt(self::$curlHandle, CURLOPT_POST, true); 
			curl_setopt(self::$curlHandle, CURLOPT_POSTFIELDS, $payload); 
		} else { 
			curl_setopt(self::$curlHandle, CURLOPT_POST, false); 
		}

		$result = curl_exec(self::$curlHandle); 

		if(curl_getinfo(self::$curlHandle, CURLINFO_HTTP_CODE) !== 200) {
			throw new MailLiftException(curl_error(self::$curlHandle), curl_getinfo(self::$curlHandle, CURLINFO_HTTP_CODE), $result); 
		}

		$resultObject = json_decode($result);
		
		return $resultObject; 
	}



	public function GetLetters($parameters=null) { 
		$letterResults = self::Request(MAILLIFT_REQUEST_GET, self::BuildUrl('letter') . '?' . http_build_query($parameters), null); 

		$output = array(); 
		foreach($letterResults as $letterObject) {
			$letter = new MailLiftLetter; 
			$letter->FromObject($letterObject); 
			$output[] = $letter; 
		}

		return $output; 
	}

	public function Get($uuid) { 
		$letterResult = self::Request(MAILLIFT_REQUEST_GET, self::BuildUrl('letter/' . $uuid), null); 
		$output = new MailLiftLetter; 
		$output->FromObject($letterResult); 

		return $output; 
	}



}

class MailLiftException extends Exception {
	public $serverResponse; 
	public $responseCode; 

	public function __construct($message, $responseCode=null, $serverResponse=null) { 
		$this->responseCode = $responseCode; 
		$this->serverResponse = $serverResponse;

		parent::__construct($message); 
	}
}

class MailLiftLetter {

	private $properties = array( 
			// Letter Metadata
			'Uuid'					=> null, 
			'Price'					=> null, 
			'Pictures'				=> null, 
			'Status'				=> null,

			// Recipient
			'RecipientName1'		=> null, 
			'RecipientName2'		=> null, 
			'RecipientAddress1'		=> null, 
			'RecipientAddress2'		=> null, 
			'RecipientCity'			=> null, 
			'RecipientStateCode'	=> null, 
			'RecipientPostCode'		=> null, 

			'MessageBody'			=> null, 
			'ScheduledDelivery' 	=> null,

			// Sender
			'SenderName1'			=> null, 
			'SenderName2'			=> null, 
			'SenderAddress1'		=> null, 
			'SenderAddress2'		=> null, 
			'SenderCity'			=> null, 
			'SenderStateCode'		=> null, 
			'SenderPostCode'		=> null, 

			// MISC
			'Notes'					=> null
		); 


	public function __set($property, $value) { 
		if(!array_key_exists($property, $this->properties)) 
			throw new MailLiftException("Cannot set invalid property: $property");

		$this->properties[$property] = $value; 
	}

	public function __get($property) { 
		if(!array_key_exists($property, $this->properties))
			throw new MailLiftException("Cannot get invalid property: $property");

		return $this->properties[$property]; 
	}

	public function FromObject($letterObject) { 
		foreach($letterObject as $key=>$value) {
			try { 
				$this->$key = $value;
			} catch(Exception $e) { 
				// Sink the exception of adding any extra properties... for now...
			}
		}
	}


	public function Send() { 
		$url = MailLift::BuildUrl('letter'); 
		$result = MailLift::Request(MAILLIFT_REQUEST_POST, $url, $this->properties); 

		// Update object
		$this->FromObject($result); 
	}

	public function Cancel() {
		if(!$this->Uuid) 
			throw new MailLiftException("Uuid required to cancel letter"); 

		$url = MailLift::BuildUrl('letter/' . $this->Uuid); 
		$result = MailLift::Request(MAILLIFT_REQUEST_DELETE, $url, null); 

		return $result; 
	}

	public function Update() { 
		if(!$this->Uuid) 
			throw new MailLiftException("Uuid required to modify letter"); 

		$url = MailLift::BuildUrl('letter/' . $this->Uuid); 
		$result = MailLift::Request(MAILLIFT_REQUEST_PUT, $url, $this->properties); 

		// Update object
		$this->FromObject($result);
	}
}