<?php
class Router {
	private $_url = [];
	private $_methods = [];
	private $_request_type = "";

	public function __construct() {
		$this->setMethod();
	}

	public function get($url, $method = null) {
		$this->url($url, $method);
	}

	public function post($url, $method = null) {
		$this->url($url, $method);
	}

	public function run() {
		$url_param = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;
		if ( in_array($url_param, $this->_url) ) {
			$key = array_search($url_param, $this->_url);
			call_user_func($this->_methods[$key]);
		} else {
			http_response_code(404);
		}
		
	}

	public function getRequestBody() {
		$request_data = json_decode(file_get_contents("php://input"), true);

		if ( !empty($request_data) ) {
			return $request_data;
		} else {
			if ( $this->_request_type == "GET" ) {
				$request_data = $_GET;
			} else if ( $this->_request_type == "POST" ) {
				$request_data = $_POST;
			}
		}

		return $request_data;
	}

	private function setMethod() {
		$this->_request_type = !empty($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : "GET";
	}

	private function url($uri, $method = null) {
		$this->_url[] = '/' . trim($uri, '/');
		if ($method != null) {
			$this->_methods[] = $method;
		}
	}

}
?>