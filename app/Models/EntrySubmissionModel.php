<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Libraries\MongoDBLibrary;
use MongoDB\BSON\ObjectId;

class EntrySubmissionModel extends Model
{
    protected $table            = 'entrysubmissions';
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
        $collection = 'entrysubmissions';
        $this->mongo = new MongoDBLibrary($uri, $dbName, $collection);
    }

    // Submit a new story
    public function submitStory($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = $data['created_at'];
        $data['status'] = $data['status'] ?? 'pending';
        return $this->mongo->insertDocument($data);
    }

    // Get a story by ID
    public function getStoryById($id)
    {
        return $this->mongo->findById($id);
    }

    // Get all stories (optionally filter by status)
    public function getStories($filter = [], $options = ['sort' => ['created_at' => -1]])
    {
        return $this->mongo->listDocuments($filter, $options);
    }

    // Update a story
    public function updateStory($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->mongo->updateDocument(['_id' => new \MongoDB\BSON\ObjectId($id)], $data);
    }

    // Delete a story
    public function deleteStory($id)
    {
        return $this->mongo->deleteDocument(['_id' => new \MongoDB\BSON\ObjectId($id)]);
    }
}
