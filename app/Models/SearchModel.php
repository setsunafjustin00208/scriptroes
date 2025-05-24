<?php

namespace App\Models;

use App\Libraries\MongoDBLibrary;

class SearchModel
{
    protected $mongoLib;
    protected $mongoClient;

    public function __construct($uri = null)
    {
        $this->mongoLib = new MongoDBLibrary($uri ?? getenv('mongo.default.uri'), 'test', 'test'); // db/collection are placeholders
        $this->mongoClient = $this->mongoLib->getClient();
    }

    /**
     * Search for a term in all collections of all databases (except system DBs).
     * @param string $term The search term
     * @param array $options Optional: [ 'fields' => [], 'limit' => 10 ]
     * @return array
     */
    public function searchAll($term, $options = [])
    {
        $results = [];
        $fields = $options['fields'] ?? [];
        $limit = $options['limit'] ?? 10;
        $dbsAndCols = $this->mongoLib->getAllDatabasesAndCollections();
        foreach ($dbsAndCols as $dbName => $collections) {
            foreach ($collections as $collName) {
                $mongoLib = new MongoDBLibrary($this->mongoClient->getManager()->getUri(), $dbName, $collName);
                $query = $this->buildTextQuery($term, $fields);
                $docs = $mongoLib->findDocumentsWithOptions($query, ['limit' => $limit]);
                foreach ($docs as $doc) {
                    $results[] = [
                        'database' => $dbName,
                        'collection' => $collName,
                        'document' => $doc,
                    ];
                }
            }
        }
        return $results;
    }

    /**
     * Build a MongoDB text search query for the given term and fields.
     * @param string $term
     * @param array $fields
     * @return array
     */
    protected function buildTextQuery($term, $fields = [])
    {
        if (empty($fields)) {
            // Use $text if a text index exists
            return ['$text' => ['$search' => $term]];
        }
        // Otherwise, search in specified fields using regex
        $or = [];
        foreach ($fields as $field) {
            $or[] = [$field => ['$regex' => $term, '$options' => 'i']];
        }
        return ['$or' => $or];
    }
}
