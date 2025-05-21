<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Libraries\MongoDBLibrary;
use MongoDB\BSON\ObjectId;

class FeedModel extends Model
{
    protected $table            = 'feeds';
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
        $collection = 'feeds';
        $this->mongo = new MongoDBLibrary($uri, $dbName, $collection);
    }

    // Create a new feed post
    public function createPost($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = $data['created_at'];
        $data['comments'] = [];
        $data['likes'] = [];
        return $this->mongo->insertDocument($data);
    }

    // Get a single post by ID
    public function getPostById($id)
    {
        return $this->mongo->findById($id);
    }

    // Get all posts (optionally paginated)
    public function getPosts($filter = [], $options = ['sort' => ['created_at' => -1]])
    {
        return $this->mongo->listDocuments($filter, $options);
    }

    // Add a comment to a post
    public function addComment($postId, $comment)
    {
        $comment['created_at'] = date('Y-m-d H:i:s');
        $comment['id'] = uniqid('comment_', true);
        return $this->mongo->updateDocument(
            ['_id' => new ObjectId($postId)],
            ['comments' => ['$each' => [$comment], '$position' => 0]] // Add to start
        );
    }

    // Add a like to a post
    public function addLike($postId, $userId)
    {
        return $this->mongo->updateDocument(
            ['_id' => new ObjectId($postId)],
            ['likes' => ['$each' => [$userId], '$position' => 0]]
        );
    }

    // Remove a like from a post
    public function removeLike($postId, $userId)
    {
        return $this->mongo->updateDocument(
            ['_id' => new ObjectId($postId)],
            ['$pull' => ['likes' => $userId]]
        );
    }

    // Update a post
    public function updatePost($postId, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->mongo->updateDocument(['_id' => new ObjectId($postId)], $data);
    }

    // Delete a post
    public function deletePost($postId)
    {
        return $this->mongo->deleteDocument(['_id' => new ObjectId($postId)]);
    }
}
