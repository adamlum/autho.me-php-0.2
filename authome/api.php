<?php

class Session {
	
	private $customer_id;
	private $passkey;
	private $url;
	private $host;
	private $port;
	
	function Session($config = array()) {
		$this->customer_id = $config->customer_id;
		$this->passkey = $config->passkey;
		$this->url = $config->url;
		$url_split_port	= explode(":", $this->url);
		$this->host = str_replace("//", "", $url_split_port[1]);
		$this->port = (count($url_split_port) > 1) ? $url_split_port[2] : "";
	}
	
	function extract_session_id($state) {
		$data = json_decode($state);
		$session_id = null;
		if (isset($data->session_id)) {
			$session_id = $data->session_id;
		}
		return $session_id;
	}
	
	function send($action = "", $params = array()) {
		$post_fields = array_merge($params, array("customer_id" => $this->customer_id, "passkey" => $this->passkey));
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->host . $action);
		curl_setopt($ch, CURLOPT_REFERER, "");
		curl_setopt($ch, CURLOPT_USERAGENT, "");
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		if (strlen($this->port) > 0) {
			curl_setopt($ch, CURLOPT_PORT, $this->port);	
		}
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
		$state = curl_exec($ch);
		if (curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) {
			throw new Exception(curl_error($ch));
			curl_close($ch);
			exit();
		}
		else {
			curl_close($ch);
			return $state;
		}	
	}
	
	function register_start() {
		$state = $this->send("/API/register_start");
		$data =	json_decode($state);
		return array("salt" => $data->salt, "state" => $state);
	}
	
	function register_finish($username, $salt, $verifier) {
		return $this->send("/API/register_finish", array("username" => $username, "salt" => $salt, "verifier" => $verifier));	
	}
	
	function account_disable($username) {
		return $this->send("/API/account_disable", array("username" => $username));
	}
	
	function account_enable($username) {
		return $this->send("/API/account_enable", array("username" => $username));	
	}
	
	function account_access($username) {
		return $this->send("/API/account_access", array("username" => $username));
	}
	
	function auth_start($username) {
		$data = $this->send("/API/auth_start", array("username" => $username));
		return array("session_id" => $this->extract_session_id($data), "data" => $data);
	}
	
	function auth_finish($session_id, $username, $client_proof, $client_pub) {
		return $this->send("/API/auth_finish", array("session_id" => $session_id, "username" => $username, "client_proof" => $client_proof, "client_pub" => $client_pub));
	}
	
}

function load_config($config_path) {
	return json_decode(file_get_contents($config_path));	
}

?>
