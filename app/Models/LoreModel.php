<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Libraries\MongoDBLibrary;
use MongoDB\BSON\ObjectId;

class LoreModel extends Model
{
    protected $table            = 'lores';
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
        $collection = 'lores';
        $this->mongo = new MongoDBLibrary($uri, $dbName, $collection);
    }

    // Create a character
    public function createCharacter($data)
    {
        $data['type'] = 'character';
        return $this->mongo->insertDocument($data);
    }

    // Create an NPC
    public function createNPC($data)
    {
        $data['type'] = 'npc';
        return $this->mongo->insertDocument($data);
    }

    // Create a vehicle
    public function createVehicle($data)
    {
        $data['type'] = 'vehicle';
        return $this->mongo->insertDocument($data);
    }

    // Create a weapon
    public function createWeapon($data)
    {
        $data['type'] = 'weapon';
        return $this->mongo->insertDocument($data);
    }

    // Create an ability (binded to an entity)
    public function createAbility($data, $entityId, $entityType)
    {
        $data['type'] = 'ability';
        $data['binded_to'] = [
            'entity_id' => $entityId,
            'entity_type' => $entityType
        ];
        return $this->mongo->insertDocument($data);
    }

    // Create stats (binded to an entity)
    public function createStats($data, $entityId, $entityType)
    {
        $data['type'] = 'stats';
        $data['binded_to'] = [
            'entity_id' => $entityId,
            'entity_type' => $entityType
        ];
        return $this->mongo->insertDocument($data);
    }

    // Create an event
    public function createEvent($data)
    {
        $data['type'] = 'event';
        return $this->mongo->insertDocument($data);
    }

    // Update an entity by type and id
    public function updateEntity($id, $type, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->mongo->updateDocument([
            '_id' => new \MongoDB\BSON\ObjectId($id),
            'type' => $type
        ], $data);
    }

    // Delete an entity by type and id
    public function deleteEntity($id, $type)
    {
        return $this->mongo->deleteDocument([
            '_id' => new \MongoDB\BSON\ObjectId($id),
            'type' => $type
        ]);
    }

    // Update a character
    public function updateCharacter($id, $data)
    {
        return $this->updateEntity($id, 'character', $data);
    }

    // Delete a character
    public function deleteCharacter($id)
    {
        return $this->deleteEntity($id, 'character');
    }

    // Update an NPC
    public function updateNPC($id, $data)
    {
        return $this->updateEntity($id, 'npc', $data);
    }

    // Delete an NPC
    public function deleteNPC($id)
    {
        return $this->deleteEntity($id, 'npc');
    }

    // Update a vehicle
    public function updateVehicle($id, $data)
    {
        return $this->updateEntity($id, 'vehicle', $data);
    }

    // Delete a vehicle
    public function deleteVehicle($id)
    {
        return $this->deleteEntity($id, 'vehicle');
    }

    // Update a weapon
    public function updateWeapon($id, $data)
    {
        return $this->updateEntity($id, 'weapon', $data);
    }

    // Delete a weapon
    public function deleteWeapon($id)
    {
        return $this->deleteEntity($id, 'weapon');
    }

    // Update an ability
    public function updateAbility($id, $data)
    {
        return $this->updateEntity($id, 'ability', $data);
    }

    // Delete an ability
    public function deleteAbility($id)
    {
        return $this->deleteEntity($id, 'ability');
    }

    // Update stats
    public function updateStats($id, $data)
    {
        return $this->updateEntity($id, 'stats', $data);
    }

    // Delete stats
    public function deleteStats($id)
    {
        return $this->deleteEntity($id, 'stats');
    }

    // Update an event
    public function updateEvent($id, $data)
    {
        return $this->updateEntity($id, 'event', $data);
    }

    // Delete an event
    public function deleteEvent($id)
    {
        return $this->deleteEntity($id, 'event');
    }
}
