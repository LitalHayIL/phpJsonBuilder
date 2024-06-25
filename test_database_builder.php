<?php

require_once 'DatabaseBuilder.php';

// Function to print test results
function printTestResult($testName, $result) {
    echo $testName . ": " . ($result ? "PASSED" : "FAILED") . "\n";
}

// Create a test table
$testTableName = 'test_table';
$db = new DatabaseBuilder("databases/{$testTableName}");

// Test getStructure
$structure = $db->getStructure();
printTestResult("getStructure", isset($structure['columns']) && in_array('id', $structure['columns']));

// Test insert
$newItemId = $db->insert(['name' => 'Test Item', 'value' => 100]);
printTestResult("insert", $newItemId > 0);

// Test get
$allItems = $db->get();
printTestResult("get", is_array($allItems) && count($allItems) > 0);

// Test find
$foundItem = $db->find($newItemId);
printTestResult("find", $foundItem !== null && $foundItem['id'] == $newItemId);

// Test update
$updateSuccess = $db->update($newItemId, ['value' => 200]);
printTestResult("update", $updateSuccess);

// Test addField
$addFieldSuccess = $db->addField('new_field');
printTestResult("addField", $addFieldSuccess);

// Test search
$searchResults = $db->search('Test');
printTestResult("search", is_array($searchResults) && count($searchResults) > 0);

// Test delete
$deleteSuccess = $db->delete($newItemId);
printTestResult("delete", $deleteSuccess);

// Clean up: delete the test table file
unlink(__DIR__ . "/databases/{$testTableName}.json");
echo "Test table deleted.\n";