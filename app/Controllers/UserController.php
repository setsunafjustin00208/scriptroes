<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;
use App\Libraries\EmailLibrary;

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
        // Check if user already exists
        $existing = $userModel->getUserByEmail($data['email']);
        if ($existing) {
            if (!empty($existing['is_active'])) {
                return $this->failValidationErrors('Account already active. Please log in.');
            } else {
                return $this->failValidationErrors('Account already registered but not activated. Please check your email for the confirmation code.');
            }
        }
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['type'] = $data['type'] ?? 'user';
        $data['permissions'] = $data['permissions'] ?? UserModel::PERM_VIEW;
        $data['personal_info'] = $data['personal_info'] ?? [];
        // Generate 6-digit confirmation code
        $confirmation_code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $data['confirmation_code'] = $confirmation_code;
        $data['is_active'] = false;
        $result = $userModel->insertUser($data);
        if (isset($result['inserted_id'])) {
       
            $this->sendRegistrationConfirmation($data['email'], $confirmation_code);
       
            return $this->respondCreated([
                'message' => 'User registered. Please check your email for the confirmation code.',
                'id' => $result['inserted_id'],
                'personal_info_id' => $result['personal_info_id'],
                'confirmation_code' => $confirmation_code // REMOVE in production
            ]);
        }
        return $this->failServerError('Registration failed.');
    }

    /**
     * Endpoint to verify confirmation code and activate account
     */
    public function activate()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        if (empty($data['email']) || empty($data['confirmation_code'])) {
            return $this->failValidationErrors('Email and confirmation code are required.');
        }
        $userModel = new UserModel();
        $user = $userModel->getUserByEmail($data['email']);
    
        if (!empty($user['is_active'])) {
            return $this->failValidationErrors('Account already active.');
        }
        if ((string)$user['confirmation_code'] != (string)$data['confirmation_code']) {
            return $this->failValidationErrors('Invalid confirmation code.');
        }
        // Activate account
        $update = $userModel->updateUser($user['_id'], [
            'is_active' => true,
            'confirmation_code' => null
        ]);

        if (isset($update['modified_count']) && $update['modified_count'] > 0) {
            return $this->respond(['message' => 'Account activated. You can now log in.']);
        }
        return $this->failServerError('Activation failed.');
    }


        /**
     * Send confirmation code to a registered email after registration.
     * @param string $email
     * @param string $code
     * @return bool|string
     */
    private function sendRegistrationConfirmation(string $email, string $code): bool|string
    {
        $emailLib = new EmailLibrary();
        return $emailLib->sendConfirmationCode($email, $code);
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
