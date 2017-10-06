Welcome to Ahmadlab/cas!
===================



Documents
-------------

Iniadalah package yang di gunakan untuk sso client pemprov banten, dan package ini masih versi beta, found some bugs, text me at 085711511295 or drop email to ahmadnorin@gmail.com


> **Note:**

> - Package ini masih dalam tahap pengembangan.
> - package ini di gunakan untuk sso app provinsi banten.
> - Package ini untuk laravel 5.2 keatas.

#### <i class="icon-file"></i> Install package

```sh
composer require ahmadlab/cas 0.1.*-dev
```
#### <i class="icon-file"></i> edit file config/app.php

tambahan class ini pada file config/app.php
```sh
Ahmadlab\Cas\CasServiceProvider::class,
```
#### <i class="icon-file"></i> tambah beberapa parameter enviroment di file .env

```sh
SSO_URL=http://sso.dev
SSO_APPID=212
SSO_SECRET=secret
SSO_CALLBACK=http://cas.dev/callback
```
#### <i class="icon-file"></i> contoh controller untuk cas login
```sh
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Ahmadlab\Cas\Cas;
use Redirect;

class DevelController extends Controller
{

    public function Login()
    {
    	$email 			= "ahmadnorin@gmail.com";
    	$password       = "karawang";

    	$post = [
    	'email'			=> $email,
    	'password'		=> $password,
    	'appid'			=> env('SSO_APPID'),
    	'secret'		=> env('SSO_SECRET'),
    	'callback'		=> env('SSO_CALLBACK')
    	];

    	$data = new Cas();
        $data->Attempt($post);
	   	if($data->Fails())
	   	{
            dd($data->message());
	   	}
	   	else
	   	{
	   	//return token
	   	//dd($data->Token());
	   	//dapetin data profile
            $profile = $data->profile($data->token());
            dd($profile);
              // dapetin data role
	     dd($profile->role());
	     //dapetin data id
	     dd($profile->id());
	     //dapetin data message
	     dd($profile->messages);
	   	}
    }
```

#### <i class="icon-file"></i> contoh controller untuk sso login

```sh
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SsoClient\Sso;
use Ahmadlab\Karawang\Karawang;
use Ahmadlab\Cas\Cas;
use Redirect;

class DevelController extends Controller
{

    public function cas(Request $request)
    {
        $data = new Cas;
        $app = [
        'appid'         => env('SSO_APPID'),
        'secret'        => env('SSO_SECRET'),
        'callback'      => env('SSO_CALLBACK')
        ];
        return $data->cas($app);
    }

    public function callback(Request $request)
    {
        $data = new Cas();

        //dd($data->Token());
        $profile = $data->profile($request->get('token'));
        $profile = $data->profile($data->token());
            dd($profile);
              // dapetin data role
	     dd($profile->role());
	     //dapetin data id
	     dd($profile->id());
	     //dapetin data message
	     dd($profile->messages);
    }
}

```


