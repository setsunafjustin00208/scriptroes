<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Libraries\MongoDBLibrary;
use MongoDB\BSON\ObjectId;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    protected $mongo;

    /**
     * User types
     */
    protected array $userTypes = [
        'super-admin',
        'admin',
        'user',
        'publisher',
        'editor',
        'moderator',
        'viewer',
    ];

    /**
     * Bitwise permissions
     * Example usage: $user['permissions'] = UserModel::PERM_CREATE | UserModel::PERM_EDIT;
     */
    public const PERM_NONE    = 0;
    public const PERM_CREATE  = 1 << 0; // 1
    public const PERM_EDIT    = 1 << 1; // 2
    public const PERM_DELETE  = 1 << 2; // 4
    public const PERM_PUBLISH = 1 << 3; // 8
    public const PERM_VIEW    = 1 << 4; // 16
    public const PERM_ADMIN   = 1 << 5; // 32
    public const PERM_SUPER   = 1 << 6; // 64
    public const PERM_ALL     = 0b1111111; // 127 (all bits including super-admin)

    /**
     * Check if a user has a specific permission
     */
    public static function hasPermission(int $userPermissions, int $permission): bool
    {
        return ($userPermissions & $permission) === $permission;
    }

    public function __construct()
    {
        parent::__construct();
        $uri = getenv('database.mongodb.connetion_string') ?: 'mongodb://localhost:27017/?directConnection=true';
        $dbName = 'users'; // TODO: set your database name here or get from config
        $collection = 'credentials';
        $this->mongo = new MongoDBLibrary($uri, $dbName, $collection);
    }

    protected $credentialsCollection = 'credentials';
    protected $personalInfoCollection = 'personal_information';

    public function setCollection($collection)
    {
        $uri = getenv('database.mongodb.connetion_string') ?: 'mongodb://localhost:27017/?directConnection=true';
        $dbName = 'users';
        $this->mongo = new MongoDBLibrary($uri, $dbName, $collection);
    }

    public function insertUser($data)
    {
        // Insert personal info first
        $personalInfo = $data['personal_info'] ?? [];
        $this->setCollection($this->personalInfoCollection);
        $personalResult = $this->mongo->insertDocument($personalInfo);
        $personalId = is_object($personalResult) && method_exists($personalResult, 'getInsertedId') ? $personalResult->getInsertedId() : null;
        // Insert credentials, referencing personal info
        $credentials = [
            'username' => $data['username'],
            'password' => $data['password'],
            'email' => $data['email'],
            'type' => $data['type'],
            'permissions' => $data['permissions'],
            'personal_info_id' => $personalId ? (string)$personalId : null,
        ];
        $this->setCollection($this->credentialsCollection);
        $credResult = $this->mongo->insertDocument($credentials);
        if (is_object($credResult) && method_exists($credResult, 'getInsertedId')) {
            return [
                'inserted_id' => (string) $credResult->getInsertedId(),
                'personal_info_id' => $personalId ? (string)$personalId : null
            ];
        }
        return $credResult;
    }

    public function getUserById($id)
    {
        $this->setCollection($this->credentialsCollection);
        $cred = $this->mongo->findById($id);
        if ($cred && isset($cred['personal_info_id'])) {
            $this->setCollection($this->personalInfoCollection);
            $info = $this->mongo->findById($cred['personal_info_id']);
            $cred['personal_info'] = $info;
        }
        return $cred;
    }

    public function getUser($filter = [])
    {
        $this->setCollection($this->credentialsCollection);
        $cred = $this->mongo->findDocument($filter);
        if ($cred && isset($cred['personal_info_id'])) {
            $this->setCollection($this->personalInfoCollection);
            $info = $this->mongo->findById($cred['personal_info_id']);
            $cred['personal_info'] = $info;
        }
        return $cred;
    }

    public function updateUser($id, $data)
    {
        $this->setCollection($this->credentialsCollection);
        $cred = $this->mongo->findById($id);
        $result = null;
        if ($cred && isset($cred['personal_info_id']) && isset($data['personal_info'])) {
            $this->setCollection($this->personalInfoCollection);
            $result = $this->mongo->updateDocument(['_id' => new ObjectId($cred['personal_info_id'])], $data['personal_info']);
        }
        // Optionally update credentials fields
        $credFields = array_intersect_key($data, array_flip(['username','password','email','type','permissions']));
        if (!empty($credFields)) {
            $this->setCollection($this->credentialsCollection);
            $result = $this->mongo->updateDocument(['_id' => new ObjectId($id)], $credFields);
        }
        if (is_object($result) && method_exists($result, 'getModifiedCount')) {
            return ['modified_count' => $result->getModifiedCount()];
        }
        return $result;
    }

    public function deleteUser($id)
    {
        $this->setCollection($this->credentialsCollection);
        $cred = $this->mongo->findById($id);
        $result = $this->mongo->deleteDocument(['_id' => new ObjectId($id)]);
        if ($cred && isset($cred['personal_info_id'])) {
            $this->setCollection($this->personalInfoCollection);
            $this->mongo->deleteDocument(['_id' => new ObjectId($cred['personal_info_id'])]);
        }
        if (is_object($result) && method_exists($result, 'getDeletedCount')) {
            return ['deleted_count' => $result->getDeletedCount()];
        }
        return $result;
    }
}
