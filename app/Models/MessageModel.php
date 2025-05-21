<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Libraries\MongoDBLibrary;

class MessageModel extends Model
{
    protected $table            = 'messages';
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

    public function __construct()
    {
        parent::__construct();
        $uri = getenv('database.mongodb.connetion_string') ?: 'mongodb://localhost:27017/?directConnection=true';
        $dbName = 'your_db_name'; // TODO: set your database name here or get from config
        $collection = 'messages';
        $this->mongo = new MongoDBLibrary($uri, $dbName, $collection);
    }

    public function insertMessage($data)
    {
        return $this->mongo->insertDocument($data);
    }

    public function getMessageById($id)
    {
        return $this->mongo->findById($id);
    }

    public function getMessage($filter = [])
    {
        return $this->mongo->findDocument($filter);
    }

    public function getMessages($filter = [], $options = [])
    {
        return $this->mongo->listDocuments($filter, $options);
    }

    public function updateMessage($id, $data)
    {
        return $this->mongo->updateDocument(['_id' => new \MongoDB\BSON\ObjectId($id)], $data);
    }

    public function deleteMessage($id)
    {
        return $this->mongo->deleteDocument(['_id' => new \MongoDB\BSON\ObjectId($id)]);
    }
}
