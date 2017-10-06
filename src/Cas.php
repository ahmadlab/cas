<?php

/**
 * @Author: ahmadnorin
 * @Date:   2017-10-06 17:16:40
 * @Last Modified by:   ahmadnorin
 * @Last Modified time: 2017-10-06 20:35:50
 */
namespace Ahmadlab\Cas;
use Redirect;

class Cas {


	public static $result;
	public static $profile_result;
	public static $request;
	public static $fails;
	public static $profile_fails;
	public static $token_access;
	public function __construct()
	{
		//Self::$request 			= $post;
		Self::$fails   			= false;
		Self::$profile_fails   	= false;
		Self::$token_access     = false;
		//Self::Curl();
		$this->profile = '';

	}


	public function Attempt($post)
	{
		$curl=curl_init();
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($curl,CURLOPT_URL,env('SSO_URL').'/cas/v1/login');
		curl_setopt($curl,CURLOPT_POST,1);
		curl_setopt($curl,CURLOPT_POSTFIELDS,http_build_query($post));
		curl_setopt($curl,CURLOPT_TIMEOUT,20);
		curl_setopt($curl,CURLOPT_HTTPHEADER, array(
		    'Accept: application/json')
		);
		$exec=curl_exec($curl);
		if(!$exec)
		{
			return Cas::$fails = true;
		}
		curl_close($curl);
		$result = json_decode($exec);
		Cas::$result = $result;
	}

	public function  Profile($token)
	{
		$curl=curl_init();
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($curl,CURLOPT_URL,env('SSO_URL').'/cas/v1/profile');
		curl_setopt($curl,CURLOPT_POST,1);
		curl_setopt($curl,CURLOPT_POSTFIELDS,http_build_query(['token' => $token]));
		curl_setopt($curl,CURLOPT_TIMEOUT,20);
		curl_setopt($curl,CURLOPT_HTTPHEADER, array(
		    'Accept: application/json', 
		    'Authorization: Bearer '.$token)
		);
		$exec=curl_exec($curl);
		if(!$exec)
		{
			Cas::$profile_fails = true;
			return [
			'error' => true, 
			'messages'	=> 
				[
					'sso_app' => ['Connection Error, Please Check Your Configurations']
				],
			'data' => []
			];
		}
		Cas::$token_access = true;
		curl_close($curl);
		$profile_result 		= json_decode($exec);
		Cas::$profile_result 	= $profile_result;
		if(Cas::$profile_result->error == true)
		{
			$this->profile      = !Cas::$profile_result->error;
			return $this;
		}
		$this->profile          = Cas::$profile_result->data->user;
		return $this;
	}

	public function CasLogin($request)
	{
		Cas::$request = $request;
	}

	public function Cas($app)
	{
		return Redirect::to(env('SSO_URL').'/login?'.http_build_query($app));
	}

	public function UserData()
	{
		return Cas::$result;
	}

	public function Fails()
	{
		if(Cas::$fails == true)
		{
			return true;
		}
		return Cas::$result->error;
	}

	public function Check()
	{
		if(Cas::$fails == true)
		{
			return false;
		}
		return !Cas::$result->error;
	}

	public function Message()
	{
		if(Cas::$token_access == false)
		{
			if(Cas::$fails == true)
			{
				//return [0 => [0 => 'Connection Error, Please Check Your Configurations']];
				return ['sso_app' => ['Connection Error, Please Check Your Configurations']];
			}
			return Cas::$result->messages;
		}
		$this->profile     = Cas::$profile_result->messages;
		return $this;
	}


	public function Token()
	{
		return Cas::$result->data->token;
	}

	public function id()
	{
		if(Cas::$token_access == false)
		{
			if(Cas::$profile_fails == true)
			{
				return ['sso_app' => ['Connection Error, Please Check Your Configurations']];
			}
			return Cas::$result->messages;
		}
		//$this->profile     = Cas::$profile_result->data->user->id;
		//return $this;
		return Cas::$profile_result->data->user->id;
	}

	public function Role()
	{
		if(Cas::$token_access == false)
		{
			if(Cas::$profile_fails == true)
			{
				return ['sso_app' => ['Connection Error, Please Check Your Configurations']];
			}
			return Cas::$result->messages;
		}
		/*$this->profile     = Cas::$profile_result->data->role;
		return $this;*/
		return Cas::$profile_result->data->role;
	}

	public function Permission()
	{
		if(Cas::$token_access == false)
		{
			if(Cas::$profile_fails == true)
			{
				return ['sso_app' => ['Connection Error, Please Check Your Configurations']];
			}
			return Cas::$result->messages;
		}
		$this->profile     = Cas::$profile_result->data->permission;
		return $this;
	}
}