<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);
require_once 'DatabaseBuilder.php';

try {
    
    // Get the action type and the table name
    $action = $_GET['action'] ?? '';
    $table = $_GET['table'] ?? '';

    switch ($action) {
        // List of available json tables 
        case 'getTables':
            $tables = array_map(function ($file) {
                return pathinfo($file, PATHINFO_FILENAME);
            }, glob(__DIR__ . '/databases/*.json'));
            echo json_encode($tables);
            break;

        // Present the current json table data
        case 'getTableStructure':
            if (empty($table)) {
                echo json_encode(['error' => 'Table name is required']);
                break;
            }
            $db = new DatabaseBuilder("databases/{$table}");
            echo json_encode($db->getStructure());
            break;

        // Create a new json file - table
        case 'createTable':
            if (empty($table)) {
                echo json_encode(['success' => false, 'error' => 'Table name is required']);
                break;
            }
            
            // Validate table name format (small english letter withput space)
            if (!preg_match('/^[a-z]+$/', $table)) {
                echo json_encode(['success' => false, 'error' => 'Table name must contain only lowercase English letters without spaces']);
                break;
            }
            
            $filePath = __DIR__ . "/databases/{$table}.json";
            if (file_exists($filePath)) {
                echo json_encode(['success' => false, 'error' => 'Table already exists']);
            } else {
                file_put_contents($filePath, json_encode([]));
                echo json_encode(['success' => true]);
            }
            break;

        // Get all records from a specified table 
        case 'get':
            if (empty($table)) {
                echo json_encode(['error' => 'Table name is required']);
                break;
            }
            $db = new DatabaseBuilder("databases/{$table}");
            echo json_encode($db->get());
            break;

        // Find a specific record from a table based on item ID
        case 'find':
            if (empty($table)) {
                echo json_encode(['error' => 'Table name is required']);
                break;
            }
            $id = $_GET['id'] ?? 0;
            $db = new DatabaseBuilder("databases/{$table}");
            echo json_encode($db->find($id));
            break;

        // Inserts a new json item
        case 'insert':
            if (empty($table)) {
                echo json_encode(['error' => 'Table name is required']);
                break;
            }
            $data = json_decode(file_get_contents('php://input'), true);
            $db = new DatabaseBuilder("databases/{$table}");
            $newId = $db->insert($data);
            echo json_encode(['id' => $newId]);
            break;

        // Update an existing json item
        case 'update':
            if (empty($table)) {
                echo json_encode(['error' => 'Table name is required']);
                break;
            }
            $id = $_GET['id'] ?? 0;
            $data = json_decode(file_get_contents('php://input'), true);
            $db = new DatabaseBuilder("databases/{$table}");
            $success = $db->update($id, $data);
            echo json_encode(['success' => $success]);
            break;

        
        // Delete an existing json item
        case 'delete':
            if (empty($table)) {
                echo json_encode(['error' => 'Table name is required']);
                break;
            }
            $id = $_GET['id'] ?? 0;
            $db = new DatabaseBuilder("databases/{$table}");
            $success = $db->delete($id);
            echo json_encode(['success' => $success]);
            break;

        // Add a new field the table
        case 'addField':
            if (empty($table)) {
                echo json_encode(['success' => false, 'error' => 'Table name is required']);
                break;
            }

            $data = json_decode(file_get_contents('php://input'), true);
            $fieldName = $data['fieldName'] ?? '';
            if (empty($fieldName)) {
                echo json_encode(['success' => false, 'error' => 'Field name is required']);
                break;
            }
             // Validate name format (small english letter withput space)
            if (!preg_match('/^[a-z]+$/', $fieldName)) {
                echo json_encode(['success' => false, 'error' => 'Field name must contain only lowercase English letters without spaces']);
                break;
            }
            
            $db = new DatabaseBuilder("databases/{$table}");
            $success = $db->addField($fieldName);
            echo json_encode(['success' => $success]);
            break;

        // Search on a table based on the provided term (name or ID)
        case 'search':
            if (empty($table)) {
                echo json_encode(['error' => 'Table name is required']);
                break;
            }
            $term = $_GET['term'] ?? '';
            $db = new DatabaseBuilder("databases/{$table}");
            $results = $db->search($term);
            echo json_encode($results);
            break;

        default:
            echo json_encode(['error' => 'Invalid action']);
    }
}
    catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
