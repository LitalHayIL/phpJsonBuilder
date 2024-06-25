<?php

require_once 'DatabaseBuilder.php';

function printMenu() {
    echo "\nDatabaseBuilder Test Menu:\n";
    echo "1. Create new table\n";
    echo "2. Select or Change selected table\n";
    echo "3. Get table structure\n";
    echo "4. Insert new item\n";
    echo "5. Get all items\n";
    echo "6. Find item by ID\n";
    echo "7. Update item\n";
    echo "8. Add new field\n";
    echo "9. Search items\n";
    echo "10. Delete item\n";
    echo "11. Exit\n";
    echo "Enter your choice: ";
}

function tableHasItems($db) {
    $items = $db->get();
    return !empty($items);
}

function getAvailableTables() {
    return array_map(function ($file) {
        return pathinfo($file, PATHINFO_FILENAME);
    }, glob(__DIR__ . '/databases/*.json'));
}

function checkIfTableSelected($db) {
    if (!$db) {
        echo "Please create or select a table first.\n";
        return false;
    }
    return true;
}

function checkIfTableHasItems($db) {
    if (empty($db->get())) {
        echo "The table is empty. Please insert an item first.\n";
        return false;
    }
    return true;
}

$db = null;
$tableName = '';

while (true) {
    printMenu();
    $choice = trim(fgets(STDIN));

    switch ($choice) {
        case '1':
            echo "Enter table name: ";
            $tableName = trim(fgets(STDIN));
            $db = new DatabaseBuilder("databases/{$tableName}");
            echo "Table '{$tableName}' created.\n";
            break;

        case '2':
            $availableTables = getAvailableTables();
            if (empty($availableTables)) {
                echo "No tables available. Please create a table first.\n";
                break;
            }
            echo "Available tables:\n";
            foreach ($availableTables as $index => $table) {
                echo ($index + 1) . ". {$table}\n";
            }
            echo "Enter the number of the table you want to select: ";
            $tableChoice = (int)trim(fgets(STDIN));
            if ($tableChoice < 1 || $tableChoice > count($availableTables)) {
                echo "Invalid choice. Please try again.\n";
                break;
            }
            $tableName = $availableTables[$tableChoice - 1];
            $db = new DatabaseBuilder("databases/{$tableName}");
            echo "Table '{$tableName}' selected.\n";
            break;

        case '3':
            if (checkIfTableSelected($db)) {
                $structure = $db->getStructure();
                echo "Table structure: " . print_r($structure, true) . "\n";
            }
            break;

        case '4':
            if (checkIfTableSelected($db)) {
                echo "Enter item data (JSON format): ";
                $itemData = json_decode(trim(fgets(STDIN)), true);
                $newId = $db->insert($itemData);
                echo "New item inserted with ID: {$newId}\n";
            }
            break;

        case '5':
            if (checkIfTableSelected($db) && checkIfTableHasItems($db)) {
                $items = $db->get();
                echo "All items: " . print_r($items, true) . "\n";
            }
            break;

        case '6':
            if (checkIfTableSelected($db) && checkIfTableHasItems($db)) {
                echo "Enter item ID: ";
                $id = trim(fgets(STDIN));
                $item = $db->find($id);
                echo "Found item: " . print_r($item, true) . "\n";
            }
            break;

        case '7':
            if (checkIfTableSelected($db) && checkIfTableHasItems($db)) {
                echo "Enter item ID to update: ";
                $id = trim(fgets(STDIN));
                echo "Enter update data (JSON format): ";
                $updateData = json_decode(trim(fgets(STDIN)), true);
                $result = $db->update($id, $updateData);
                echo "Update result: " . ($result ? "Success" : "Failed") . "\n";
            }
            break;

        case '8':
            if (checkIfTableSelected($db)) {
                echo "Enter new field name: ";
                $fieldName = trim(fgets(STDIN));
                if (!preg_match('/^[a-z]+$/', $fieldName)) {
                    echo 'Table name must contain only lowercase English letters without spaces' . "\n";
                    break;
                }
                $result = $db->addField($fieldName);
                echo "Add field result: " . ($result ? "Success" : "Failed") . "\n";
            }
            break;

        case '9':
            if (checkIfTableSelected($db) && checkIfTableHasItems($db)) {
                echo "Enter search term (name or ID only): ";
                $term = trim(fgets(STDIN));
                $results = $db->search($term);
                echo "Search results: " . print_r($results, true) . "\n";
            }
            break;

        case '10':
            if (checkIfTableSelected($db) && checkIfTableHasItems($db)) {
                echo "Enter item ID to delete: ";
                $id = trim(fgets(STDIN));
                $result = $db->delete($id);
                echo "Delete result: " . ($result ? "Success" : "Failed") . "\n";
            }
            break;

        case '11':
            echo "Exiting...\n";
            exit;

        default:
            echo "Invalid choice. Please try again.\n";
    }
}