<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;

class UserController extends BaseController
{
    use ResponseTrait;

    public function register()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        if (!$data || empty($data['username']) || empty($data['password']) || empty($data['email'])) {
            return $this->failValidationErrors('Username, password, and email are required.');
        }
        $userModel = new UserModel();
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['type'] = $data['type'] ?? 'user';
        $data['permissions'] = $data['permissions'] ?? UserModel::PERM_VIEW;
        // Accept personal_info as part of registration
        $data['personal_info'] = $data['personal_info'] ?? [];
        $result = $userModel->insertUser($data);
        if (isset($result['inserted_id'])) {
            return $this->respondCreated(['message' => 'User registered', 'id' => $result['inserted_id'], 'personal_info_id' => $result['personal_info_id']]);
        }
        return $this->failServerError('Registration failed.');
    }

    public function login()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        if (!$data || empty($data['username']) || empty($data['password'])) {
            return $this->failValidationErrors('Username and password are required.');
        }
        $userModel = new UserModel();
        // Use getUserByEmail instead of getUser
        $user = $userModel->getUserByEmail($data['username']);
        if ($user && password_verify($data['password'], $user['password'])) {
            session()->set('user', [
                'id' => (string)($user['_id'] ?? $user['id'] ?? ''),
                'username' => $user['username'],
                'type' => $user['type'],
                'permissions' => $user['permissions'],
                'is_logged_in' => true,
            ]);
            return $this->respond(['message' => 'Login successful']);
        }
        return $this->failUnauthorized('Invalid username or password.');
    }

    public function logout()
    {
        session()->remove('user');
        return redirect()->to('/login')->with('message', 'Logged out');
    }

    public function getUser($id)
    {
        $userModel = new UserModel();
        $user = $userModel->getUserById($id);
        if ($user) {
            return $this->respond($user);
        }
        return $this->failNotFound('User not found.');
    }

    public function update($id)
    {
        $data = $this->request->getJSON(true) ?? $this->request->getRawInput();
        if (!$data) {
            return $this->failValidationErrors('No data provided.');
        }
        $userModel = new UserModel();
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        // Accept personal_info for update
        $data['personal_info'] = $data['personal_info'] ?? null;
        $result = $userModel->updateUser($id, $data);
        if (isset($result['modified_count']) && $result['modified_count'] > 0) {
            return $this->respond(['message' => 'User updated']);
        }
        return $this->failNotFound('User not found or no changes made.');
    }

    public function delete($id)
    {
        $userModel = new UserModel();
        $result = $userModel->deleteUser($id);
        if (isset($result['deleted_count']) && $result['deleted_count'] > 0) {
            return $this->respondDeleted(['message' => 'User deleted']);
        }
        return $this->failNotFound('User not found.');
    }
}
