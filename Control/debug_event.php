<?php
// debug_event.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/EventController.php';

echo "Instantiating EventController...\n";
try {
    $controller = new EventController();
    echo "EventController instantiated.\n";
    
    // Use reflection to access private methods for testing
    $reflection = new ReflectionClass('EventController');
    
    // Test getAll first to get an ID
    echo "Testing getAll...\n";
    $getAllMethod = $reflection->getMethod('getAll');
    $getAllMethod->setAccessible(true);
    $allEvents = $getAllMethod->invoke($controller, []);
    
    if (empty($allEvents['events'])) {
        echo "No events found.\n";
    } else {
        $firstEvent = $allEvents['events'][0];
        $id = $firstEvent['id'];
        echo "Found event ID: $id\n";
        
        // Test get
        echo "Testing get($id)...\n";
        $getMethod = $reflection->getMethod('get');
        $getMethod->setAccessible(true);
        $eventDetails = $getMethod->invoke($controller, ['id' => $id]);
        
        echo "Event Details:\n";
        print_r($eventDetails);
    }
    
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
?>
