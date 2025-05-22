<?php

namespace App\Controllers\Pages;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Login extends BaseController
{
    public function index()
    {
        $data = [];
        $data['meta'] = [
            'title' => 'Login',
            'description' => 'Login to your account',
            'keywords' => 'login, account, user',
            'image' => base_url('resources/images/logo.png'),
            'url' => base_url('login'),
        ];
        $data['headers'] = [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization',
        ];
        $data['statusCode'] = 200;


        $data['resources']['styles'] = [  
            'resources/css/pages/login.min.css'
        ];
        $data['resources']['scripts'] = [
            'resources/js/pages/login.min.js'
        ];

        $this->setStatusCode($data['statusCode']);
        $this->setHeaders($data['headers']);

        return view('pages/login', $data);
    }

    private function setHeaders ($headers)
    {
        foreach ($headers as $key => $value) {
            $this->response->setHeader($key, $value);
        }
    }
    private function setStatusCode ($code)
    {
        $this->response->setStatusCode($code);
    }
}
