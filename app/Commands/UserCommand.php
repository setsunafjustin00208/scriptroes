<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\UserModel;

class UserCommand extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'App';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'user';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'User management: create, update, delete, login';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'user [create|update|delete|login] [options]';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [
        'action' => 'The action to perform: create, update, delete, login',
    ];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [
        '--id' => 'User ID (for update/delete/login)',
        '--username' => 'Username',
        '--password' => 'Password',
        '--email' => 'Email',
        '--type' => 'User type (super-admin, admin, user, publisher, editor, moderator, viewer)',
        '--permissions' => 'Permissions as integer or comma-separated list (e.g. 3 or create,edit)',
        '--data' => 'JSON string for update',
    ];

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        $action = $params[0] ?? null;
        $userModel = new UserModel();

        switch ($action) {
            case 'create':
                $username = CLI::getOption('username') ?? CLI::prompt('Username');
                $password = CLI::getOption('password') ?? CLI::prompt('Password');
                $email = CLI::getOption('email') ?? CLI::prompt('Email');
                $type = CLI::getOption('type') ?? CLI::prompt('User Type (super-admin, admin, user, publisher, editor, moderator, viewer)');
                $personalInfo = [];
                // Prompt for personal info fields interactively
                CLI::write('Enter personal information (leave blank to skip):');
                $fields = ['first_name', 'last_name', 'bio', 'avatar', 'phone', 'address'];
                foreach ($fields as $field) {
                    $val = CLI::prompt(ucwords(str_replace('_', ' ', $field)));
                    if ($val !== '') {
                        $personalInfo[$field] = $val;
                    }
                }
                $permMap = [
                    'create'      => UserModel::PERM_CREATE,
                    'edit'        => UserModel::PERM_EDIT,
                    'delete'      => UserModel::PERM_DELETE,
                    'publish'     => UserModel::PERM_PUBLISH,
                    'view'        => UserModel::PERM_VIEW,
                    'admin'       => UserModel::PERM_ADMIN,
                    'super-admin' => UserModel::PERM_SUPER,
                ];
                // Assign permissions based on user type
                if ($type == 'super-admin') {
                    $permInt = UserModel::PERM_ALL;
                } elseif ($type == 'admin') {
                    $permInt = UserModel::PERM_ADMIN | UserModel::PERM_CREATE | UserModel::PERM_EDIT | UserModel::PERM_DELETE | UserModel::PERM_PUBLISH | UserModel::PERM_VIEW;
                } elseif ($type == 'viewer') {
                    $permInt = UserModel::PERM_VIEW;
                } else {
                    // Prompt for permissions interactively
                    $permInt = 0;
                    CLI::write('Select permissions for the user (y/n):');
                    foreach ($permMap as $permName => $permBit) {
                        // Accept 'y' for yes, anything else (including 'n' or empty) as no
                        $answer = strtolower(trim(CLI::prompt(ucfirst($permName) . '? [y/N]')));
                        if ($answer === 'y') {
                            $permInt |= $permBit;
                        }
                    }
                }
                $data = [
                    'username' => $username,
                    'password' => password_hash($password, PASSWORD_DEFAULT),
                    'email' => $email,
                    'type' => $type,
                    'permissions' => $permInt,
                    'personal_info' => $personalInfo,
                ];
                $result = $userModel->insertUser($data);
                CLI::write('User created: ' . json_encode($result));
                CLI::write('Permissions (int): ' . $permInt);
                $permNames = [];
                foreach ($permMap as $name => $bit) {
                    if (UserModel::hasPermission($permInt, $bit)) {
                        $permNames[] = $name;
                    }
                }
                CLI::write('Permissions (names): ' . (empty($permNames) ? 'none' : implode(', ', $permNames)));
                break;
            case 'update':
                $id = CLI::getOption('id') ?? CLI::prompt('User ID');
                $dataJson = CLI::getOption('data') ?? CLI::prompt('Update data (JSON)');
                $data = json_decode($dataJson, true);
                if (!$data) {
                    CLI::error('Invalid JSON data.');
                    return;
                }
                // Optionally prompt for personal info update
                if (CLI::prompt('Update personal info? [y/N]', ['y', 'n'], 'n') === 'y') {
                    $personalInfo = [];
                    $fields = ['first_name', 'last_name', 'bio', 'avatar', 'phone', 'address'];
                    foreach ($fields as $field) {
                        $val = CLI::prompt(ucwords(str_replace('_', ' ', $field)));
                        if ($val !== '') {
                            $personalInfo[$field] = $val;
                        }
                    }
                    $data['personal_info'] = $personalInfo;
                }
                $result = $userModel->updateUser($id, $data);
                CLI::write('User updated: ' . json_encode($result));
                break;
            case 'delete':
                $id = CLI::getOption('id') ?? CLI::prompt('User ID');
                $result = $userModel->deleteUser($id);
                CLI::write('User deleted: ' . json_encode($result));
                break;
            case 'login':
                $username = CLI::getOption('username') ?? CLI::prompt('Username');
                $password = CLI::getOption('password') ?? CLI::prompt('Password');
                $user = $userModel->getUser(['username' => $username]);
                if ($user && password_verify($password, $user['password'])) {
                    CLI::write('Login successful!');
                    CLI::write('User type: ' . ($user['type'] ?? 'unknown'));
                    CLI::write('Permissions: ' . ($user['permissions'] ?? 0));
                    // Show permissions as names
                    $permInt = $user['permissions'] ?? 0;
                    $permNames = [];
                    $permMap = [
                        'create'      => UserModel::PERM_CREATE,
                        'edit'        => UserModel::PERM_EDIT,
                        'delete'      => UserModel::PERM_DELETE,
                        'publish'     => UserModel::PERM_PUBLISH,
                        'view'        => UserModel::PERM_VIEW,
                        'admin'       => UserModel::PERM_ADMIN,
                        'super-admin' => UserModel::PERM_SUPER,
                    ];
                    foreach ($permMap as $name => $bit) {
                        if (UserModel::hasPermission($permInt, $bit)) {
                            $permNames[] = $name;
                        }
                    }
                    CLI::write('Permissions (names): ' . (empty($permNames) ? 'none' : implode(', ', $permNames)));
                } else {
                    CLI::error('Login failed.');
                }
                break;
            default:
                CLI::write('Usage: ' . $this->usage);
                break;
        }
    }
}
