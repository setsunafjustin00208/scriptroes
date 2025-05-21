<?php

namespace App\Controllers\Pages;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Login extends BaseController
{
    public function index()
    {
        $data = [];
        $data['meta'] = $this->setMetaData(
            'Login',
            'Login to your account',
            'login, account, user'
        );
        $data['headers'] = [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization',
        ];
        $data['statusCode'] = 200;
        $data['title'] = $data['meta']['title'];
        $data['description'] = $data['meta']['description'];
        $data['keywords'] = $data['meta']['keywords'];

        $data['resources']['styles'] = [
            base_url('resources/css/login.css'),    

        ];



        $this->setStatusCode($data['statusCode']);
        $this->setHeaders($data['headers']);

        return view('pages/login', $data);
    }

    private function setMetaData ($title, $description, $keywords)
    {
        $metaData = Array (
            'title' => $title,
            'description' => $description,
            'keywords' => $keywords
        );

        return $metaData;
    
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
