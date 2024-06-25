<?php

class DatabaseBuilder
{
    private $tableName;
    private $filePath;

    public function __construct($tableName)
    {
        // initial the table name and file path.
        $this->tableName = $tableName;
        $this->filePath = __DIR__ . "/{$tableName}.json";
        
        // create json file table if it dosen't exist
        if (!file_exists($this->filePath)) {
            file_put_contents($this->filePath, json_encode([]));
        }
    }

    // present the current table data
    public function getStructure()
    {
        $data = $this->readData();
        // check if table is empty
        // if table is empty, will return only 'id' coulmn for maintaining a consistent structure across all tables and providing a starting point structure to work with new table. 

        if (empty($data)) {
            return ['columns' => ['id']];
        }
        return ['columns' => array_keys($data[0])];
    }

    
    public function get($columns = null)
    {
        $data = $this->readData();
        
        if ($columns === null || $columns === ['*']) {
            return $data;
        }
        
        // filter the data and return only the requested columns 
        return array_map(fn($item) => array_intersect_key($item, array_flip($columns)), $data);
    }

    public function find($id, $columns = null)
    {
        $data = $this->get($columns);
        return array_values(array_filter($data, function($item) use ($id) {
            return $item['id'] == $id;
        }))[0] ?? null;
    }

    // create a new json item
    public function insert($data)
    {
        if (!is_array($data)) {
            throw new InvalidArgumentException("Data must be an array");
        }
        $allData = $this->readData();
        $data['id'] = $this->getNextId($allData);
        $allData[] = $data;
        $this->writeData($allData);
        return $data['id'];
    }


    // update existing json item
    public function update($id, $data)
    {
        $allData = $this->readData();
        $updated = false;
        foreach ($allData as &$item) {
            if ($item['id'] == $id) {
                $item = array_merge($item, $data);
                $updated = true;
                break;
            }
        }
        if ($updated) {
            $this->writeData($allData);
        }
        return $updated;
    }

    public function delete($id)
    {
        $allData = $this->readData();
        $filtered = array_filter($allData, function($item) use ($id) {
            return $item['id'] != $id;
        });
        
        if (count($allData) !== count($filtered)) {
            $this->writeData(array_values($filtered));
            return true;
        }
        
        return false;
    }

    // Adds a new field and if item exist add an empty string.
    public function addField($fieldName)
    {
        $data = $this->get();

        // Check if the field already exists in the first record
        if (!empty($data) && isset($data[0][$fieldName])) {
            return false;
        }

        // Add the new field to each record
        foreach ($data as &$record) {
            $record[$fieldName] = null;
        }

        file_put_contents($this->filePath, json_encode($data));
        return true;
    }

    // Read data from json file
    private function readData()
    {
        $content = file_get_contents($this->filePath);
        if ($content === false) {
            throw new RuntimeException("Unable to read file: {$this->filePath}");
        }
        $data = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException("Invalid JSON in file: {$this->filePath}");
        }
        return $data ?: [];
    }

    // Write data from json file
    private function writeData($data)
    {
        $json = json_encode($data, JSON_PRETTY_PRINT);
        if ($json === false) {
            throw new RuntimeException("Unable to encode data to JSON");
        }
        if (file_put_contents($this->filePath, $json) === false) {
            throw new RuntimeException("Unable to write to file: {$this->filePath}");
        }
    }

    // Check what is the next avaliavble ID to create a new json item with the insert function
    private function getNextId($data)
    {
        $maxId = 0;
        foreach ($data as $item) {
            $maxId = max($maxId, $item['id'] ?? 0);
        }
        return $maxId + 1;
    }

    // Search the data with a query string (ID or name)
    public function search($q)
    {
        $data = $this->readData();
        $results = array_filter($data, function($item) use ($q) {
            // search by id
            if (isset($item['id']) && $item['id'] == $q) {
                return true;
            }
            // search by name
            if (isset($item['name']) && stripos($item['name'], $q) !== false) {
                return true;
            }
            return false;
        });
        // To make sure the result will be always an array
        return array_values($results);
    }



}