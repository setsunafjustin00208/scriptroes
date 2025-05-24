<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SearchModel;

class GlobalSearchController extends BaseController
{
    /**
     * Prepare global search results for API output (post, book, place, events, etc.).
     * @param array $results
     * @return array
     */
    private function prepareGlobalResults(array $results): array
    {
        $items = [];
        foreach ($results as $row) {
            $doc = $row['document'];
            $type = $row['collection'] ?? 'unknown';
            $id = (string)($doc['_id'] ?? '');
            $title = $doc['title'] ?? ($doc['name'] ?? ($doc['event_name'] ?? 'Untitled'));
            $desc = $doc['description'] ?? ($doc['summary'] ?? '');
            $url = base_url("/{$type}/$id");
            $items[] = [
                'type' => $type,
                'id' => $id,
                'title' => $title,
                'description' => $desc,
                'url' => $url
            ];
        }
        return $items;
    }

    public function index()
    {
        $request = service('request');
        $term = $request->getGet('q') ?? '';
        if (empty($term)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Missing search term.'
            ])->setStatusCode(400);
        }
        $searchModel = new SearchModel();
        $results = $searchModel->searchAll($term, [
            'fields' => ['title', 'name', 'event_name', 'description', 'summary'],
            'limit' => 10
        ]);
        $items = $this->prepareGlobalResults($results);
        return $this->response->setJSON([
            'status' => 'success',
            'results' => $items
        ]);
    }
}
