<?php

namespace App\Controllers\Pages;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;
use App\Libraries\StructuredData;
use App\Libraries\ComponentResources;

class HomePage extends BaseController
{
    public function index()
    {
        $session = session();
        $userData = $session->get('user');
        $userModel = new UserModel();
        $user = null;
        $fullname = '';
        if ($userData && !empty($userData['id'])) {
            $user = $userModel->getUserById($userData['id']);
            if ($user && isset($user['personal_info']['first_name']) && isset($user['personal_info']['last_name'])) {
                $fullname = $user['personal_info']['first_name'] . ' ' . $user['personal_info']['last_name'];
            } else if ($user && isset($user['username'])) {
                $fullname = $user['username'];
            }
        }
        $data = [];
        $data['meta'] = [
            'title' => 'Home',
            'description' => 'Homepage',
            'keywords' => 'home, dashboard, user',
            'image' => base_url('resources/images/logo.png'),
            'url' => base_url('/'),
        ];
        $data['headers'] = [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization',
        ];
        $data['statusCode'] = 200;
        $data['fullname'] = $fullname;
        $data['user'] = $user;
        $data['resources']['styles'] = [  
            'resources/css/pages/homepage.min.css'
        ];
        $data['resources']['scripts'] = [
            'resources/js/pages/homepage.min.js'
        ];

        // Structured Data: Website > WebPage > Organization
        $sd = new StructuredData([
            'baseUrl' => base_url('/'),
            'title' => $data['meta']['title'],
            'description' => $data['meta']['description'],
            'keywords' => $data['meta']['keywords'],
            'organizationName' => APP_NAME,
            'logoUrl' => base_url('resources/images/logo.png'),
            'language' => 'en-US',
        ]);
        $organization = $sd->getDefaultStructuredByType('organization');
        $webPage = $sd->getDefaultStructuredByType('webpage');
        $webPage['about'] = $organization;
        $website = $sd->getDefaultStructuredByType('website');
        $website['mainEntity'] = $webPage;
        $sd->addProperties($website);
        $data['structuredData'] = $sd->generate();

        // Add all default component resources (navbar, footer, sidebar) at once
        $componentResources = ComponentResources::getAllDefaultResources();
        $data['resources']['styles'] = array_merge($componentResources['styles'], [
            'resources/css/pages/homepage.min.css'
        ]);
        $data['resources']['scripts'] = array_merge($componentResources['scripts'], [
            'resources/js/pages/homepage.min.js'
        ]);

        $this->setStatusCode($data['statusCode']);
        $this->setHeaders($data['headers']);

        if (!$userData || empty($userData['id'])) {
            return redirect()->to(base_url('login'));
        }

        return view('pages/homepage', $data);
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
