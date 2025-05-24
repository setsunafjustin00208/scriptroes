<?php

namespace App\Controllers\Pages;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Libraries\StructuredData;
use App\Libraries\EmailLibrary;

class Register extends BaseController
{
    public function index()
    {
        $data = [];
        $data['meta'] = [
            'title' => 'Register',
            'description' => 'Create a new account',
            'keywords' => 'register, signup, account, user',
            'image' => base_url('resources/images/logo.png'),
            'url' => base_url('register'),
        ];
        $data['headers'] = [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization',
        ];
        $data['statusCode'] = 200;

        $data['resources']['styles'] = [  
            'resources/css/pages/register.min.css' // reuse login styles for register
        ];
        $data['resources']['scripts'] = [
            'resources/js/pages/register.min.js' // reuse login/register JS
        ];

        $sd = new StructuredData([
            'baseUrl' => base_url('login'),
            'title' => $data['meta']['title'],
            'description' => $data['meta']['description'],
            'keywords' => $data['meta']['keywords'],
            'organizationName' => APP_NAME,
            'logoUrl' => base_url('resources/images/logo.png'),
            'language' => 'en-US',
        ]);
        $webPage = $sd->getDefaultStructuredByType('webpage');
        $sd->addProperties($webPage);
        $data['structuredData'] = $sd->generate();

        $this->setStatusCode($data['statusCode']);
        $this->setHeaders($data['headers']);

        if (session()->get('user')) {
            return redirect()->to(base_url('home'));
        }

        return view('pages/register', $data);
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
