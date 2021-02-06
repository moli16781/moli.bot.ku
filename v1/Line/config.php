<?php 
class Line
{

	public $URL_OAUTH_ACCESS_TOKEN = 'https://api.line.me/v2/oauth/accessToken';
	public $URL_GET_PROFILE = 'https://api.line.me/v2/bot/profile/';
	public $client_id;
	public $client_secret;

	public function __construct($client_id=null,$client_secret=null)
	{
		$this->client_id = $client_id;
		$this->client_secret = $client_secret;
	}

	public function getTokenForChanal()
	{
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $this->URL_OAUTH_ACCESS_TOKEN,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => "grant_type=client_credentials&client_id=$this->client_id&client_secret=$this->client_secret",
		  CURLOPT_HTTPHEADER => array(
		    "cache-control: no-cache",
		    "content-type: application/x-www-form-urlencoded"
		  )
		));

		$response = curl_exec($curl);
		curl_close($curl);

		return $response;
	}

	public function getProfile($userID=null,$token=null)
	{
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $this->URL_GET_PROFILE.$userID,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  CURLOPT_HTTPHEADER => array(
		    "cache-control: no-cache",
			"Authorization: Bearer ".$token
		  )
		));

		$response = curl_exec($curl);
		curl_close($curl);

		return $response;
	}
}

?>