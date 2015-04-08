<?php

/**
 * @class NutshellApi
 * @brief Easy access to the Nutshell JSON-RPC API
 * 
 * This class is instantiated with a username and API key. Once it has been
 * instantiated, the call() method is used to make calls to the Nutshell API.
 * 
 * Rather than using call(), you can also call any Nutshell API methods on 
 * this class. For example, rather than calling
 * @code
 *  $api->call('getContact', $params);
 * @endcode
 * you can call
 * @code
 *  $api->getContact($params);
 * @endcode
 * 
 * Calls made using this class are synchronous - the method blocks until the
 * request is completed.
 * 
 * Requires PHP 5.0+ and the CURL and JSON modules.
 * CURL: http://php.net/manual/en/book.curl.php
 * JSON Module: http://pecl.php.net/package/json
 * 
 * @version 0.1
 * @date March 2, 2011
 */

class NutshellApi {
	const ENDPOINT_DISCOVER_URL = 'https://api.nutshell.com/v1/json';
	protected $curl = NULL;
	
	/**
	 * Initializes the API access class. Takes care of endpoint discovery.
	 * 
	 * @param string $username
	 * @param string $apiKey
	 * @throws NutshellApiException if either parameter is invalid
	 */
	function __construct($username, $apiKey) {
		if (!is_string($username) || !is_string($apiKey)) {
			throw new NutshellApiException('You must specify a username and API key.');
		}
		if (strpos($username, '@') === FALSE) {
			throw new NutshellApiException('Username is not a valid email address.');
		}
		if (strlen($apiKey) <= 12) {
			throw new NutshellApiException('API key is not long enough to be a valid key.');
		}
		
		$endpoint   = $this->_getApiEndpointForUser($username);
		$authHeader = base64_encode($username . ':' . $apiKey);
		
		$this->curl = curl_init($endpoint);
		curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('Authorization: Basic '.$authHeader));
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->curl, CURLOPT_POST, true);
		curl_setopt($this->curl, CURLOPT_HEADER, false);
		curl_setopt($this->curl, CURLOPT_CAINFO, dirname(__FILE__).'/geotrust_global_ca.crt');
	}
	
	function __destruct() {
		if ($this->curl) {
			curl_close($this->curl);
		}
	}
	
	/**
	 * Calls a Nutshell API method
	 * 
	 * See call() for detailed specs.
	 * 
	 * @return array
	 * @throws NutshellApiException
	 */
	public function __call($name, $args) {
		$params = null;
		if (count($args) === 1 && is_array($args[0])) {
			// e.g. $api->getLead(array('leadId' => 11))
			$params = $args[0];
		} else {
			// e.g. $api->getLead(11)
			$params = $args;
		}
		return $this->call($name, $params);
	}
	
	/**
	 * Calls a Nutshell API method.
	 * 
	 * Returns the result from that call or, if there was an error on the server, 
	 * throws an exception.
	 * 
	 * @param string $method
	 * @param array|null $params
	 * @return array
	 * @throws NutshellApiException
	 */
	public function call($method, array $params = NULL) {
		if ($this->curl === NULL) {
			throw new NutshellApiException('Nutshell API uninitialized; perhaps the constructor failed?');
		}
		if ($params === NULL) {
			$params = array();
		}
		if (!is_string($method)) {
			throw new NutshellApiException("Invalid method '$method'");
		} else if (!is_array($params)) {
			throw new NutshellApiException('$params must be an array');
		}
		
		$payload = array(
			'method' => $method,
			'params' => $params,
			'id' => $this->_generateRequestId(),
		);
		
		curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->json_encode($payload));
		$fullResult = curl_exec($this->curl);
		if (curl_errno($this->curl)) {
			throw new NutshellApiException('Curl error #' . curl_errno($this->curl) . ' during API call: '. curl_error($this->curl));
		}
		$fullResult = $this->json_decode($fullResult);
		
		if ($fullResult->error !== NULL) {
			throw new NutshellApiException('API Error: ' . $fullResult->error->message, $fullResult->error->code, $fullResult->error->data);
		}
		
		return $fullResult->result;
	}
	
	/**
	 * Finds the appropriate API endpoint for the given user.
	 * 
	 * Info on endpoint discovery: http://nutshell.com/api/endpoint-discovery.html
	 * 
	 * @param string $username
	 * @return string API endpoint
	 * @throws NutshellApiException
	 */
	protected function _getApiEndpointForUser($username) {
		$payload = array(
			'method' => 'getApiForUsername',
			'params' => array('username' => $username),
			'id' => $this->_generateRequestId(),
		);

		$curl = curl_init(self::ENDPOINT_DISCOVER_URL);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $this->json_encode($payload));
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_CAINFO, dirname(__FILE__).'/geotrust_global_ca.crt');
		$result = curl_exec($curl);
		if (curl_errno($curl)) {
			throw new NutshellApiException('Curl error #' . curl_errno($curl) . ' while finding endpoint: '. curl_error($curl));
		}
		curl_close($curl);

		$decoded = $this->json_decode($result);
		if ($decoded->error !== NULL) {
			throw new NutshellApiException($decoded->error->message);
		}
		return 'https://' . $decoded->result->api . '/api/v1/json';
	}
	
	/**
	 * Generates a random JSON request ID
	 * 
	 * @return string
	 */
	protected function _generateRequestId() {
		return substr(md5(rand()), 0, 8);
	}
	
	/**
	 * Encodes object in JSON
	 * 
	 * Can be overridden to support PHP installations without built-in JSON support.
	 */
	protected function json_encode($x) {
		return json_encode($x);
	}
	
	/**
	 * Decodes object from JSON
	 * 
	 * Can be overridden to support PHP installations without built-in JSON support.
	 */
	protected function json_decode($x) {
		return json_decode($x);
	}
}

class NutshellApiException extends Exception {
	protected $data;
	
	public function __construct($message, $code = 0, $data = NULL) {
		parent::__construct($message, $code);
		$this->data = $data;
	}

	public function getData() {
		return $this->data;
	}
}
