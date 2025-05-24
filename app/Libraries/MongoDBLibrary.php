<?php

namespace App\Libraries;

// Impoort MongoDB client
use MongoDB\Client as MongoDBClient;

// Import the MongoDB BSON ObjectId class
use MongoDB\BSON\ObjectId;

class MongoDBLibrary
{
    protected $client;
    protected $database;
    protected $collection;

    public function __construct($uri, $dbName, $collectionName)
    {
        // Initialize MongoDB client
        $this->client = new MongoDBClient($uri);
        $this->database = $this->client->$dbName;
        $this->collection = $this->database->$collectionName;
    }

    
    public function insertDocument($data)
    {
        return $this->collection->insertOne($data);
    }

    public function findDocument($filter)
    {
        return $this->collection->findOne($filter);
    }

    public function updateDocument($filter, $update)
    {
        return $this->collection->updateOne($filter, ['$set' => $update]);
    }

    public function deleteDocument($filter)
    {
        return $this->collection->deleteOne($filter);
    }
    public function listDocuments($filter = [], $options = [])
    {
        return $this->collection->find($filter, $options);
    }
    public function countDocuments($filter = [])
    {
        return $this->collection->countDocuments($filter);
    }
    public function aggregate($pipeline, $options = [])
    {
        return $this->collection->aggregate($pipeline, $options);
    }
    public function createIndex($keys, $options = [])
    {
        return $this->collection->createIndex($keys, $options);
    }
    public function dropIndex($indexName)
    {
        return $this->collection->dropIndex($indexName);
    }
    public function dropCollection()
    {
        return $this->database->dropCollection($this->collection->getCollectionName());
    }
    public function getCollectionName()
    {
        return $this->collection->getCollectionName();
    }
    public function findDocumentsWithOptions($filter = [], $options = [])
    {
        $cursor = $this->collection->find($filter, $options);
        $results = [];
        foreach ($cursor as $doc) {
            // Convert BSON ObjectId to string for easier handling
            if (isset($doc['_id']) && is_object($doc['_id']) && get_class($doc['_id']) === 'MongoDB\\BSON\\ObjectId') {
                $doc['_id'] = (string) $doc['_id'];
            }
            $results[] = $doc;
        }
        return $results;
    }

    public function updateDocuments($filter, $update, $options = [])
    {
        return $this->collection->updateMany($filter, ['$set' => $update], $options);
    }

    public function deleteDocuments($filter, $options = [])
    {
        return $this->collection->deleteMany($filter, $options);
    }

    public function findById($id)
    {
        if (!$id instanceof ObjectId) {
            try {
                $id = new ObjectId($id);
            } catch (\Exception $e) {
                return null;
            }
        }
        $doc = $this->collection->findOne(['_id' => $id]);
        if ($doc && isset($doc['_id']) && $doc['_id'] instanceof ObjectId) {
            $doc['_id'] = (string) $doc['_id'];
        }
        return $doc;
    }

    //insert multiple documents
    public function insertManyDocuments($data)
    {
        return $this->collection->insertMany($data);
    }

    // ping database
    public function ping()
    {
        try {
            $this->client->listDatabases();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get all non-system databases and their collections.
     * @return array [ dbName => [collection1, collection2, ...], ... ]
     */
    public function getAllDatabasesAndCollections()
    {
        $systemDbs = ['admin', 'local', 'config'];
        $result = [];
        foreach ($this->client->listDatabases() as $dbInfo) {
            $dbName = $dbInfo['name'];
            if (in_array($dbName, $systemDbs)) continue;
            $db = $this->client->selectDatabase($dbName);
            $result[$dbName] = [];
            foreach ($db->listCollections() as $collectionInfo) {
                $result[$dbName][] = $collectionInfo->getName();
            }
        }
        return $result;
    }

    /**
     * Get the underlying MongoDB client instance.
     * @return \MongoDB\Client
     */
    public function getClient()
    {
        return $this->client;
    }
}
