<?php
include(__DIR__ . "/../APIConfig/Config.php");
header('Content-Type: application/json');

// Get HTTP method and route request to the right function
$method = $_SERVER['REQUEST_METHOD'];


switch ($method) {
    case 'POST':
        // Insert new TaskType
        $data = json_decode(file_get_contents("php://input"), true);
        echo json_encode(['success' => insertTaskPriority($data['PriorityName'], $data['Language'], $conn)]);
        break;


    case 'GET':
        // If no language is specified, default to 'EN'
        $language = !empty($_GET['lang']) ? $_GET['lang'] : 'EN';
        echo json_encode(getAllTaskPriority($language, $conn));
        break;

    case 'PUT':
        // Update an existing TaskType
        parse_str(file_get_contents("php://input"), $_PUT);
        $PriorityId = $_GET['PriorityId'] ?? null;
        $language = !empty($_GET['lang']) ? $_GET['lang'] : 'EN';
        if ($PriorityId) {
            echo json_encode(['success' => updateTaskPriority($PriorityId, $_PUT['PriorityName'], $_PUT['Language'] ?? 'EN', $conn)]);
        } else {
            echo json_encode(['error' => 'PriorityId is required for updating']);//getmessagefromapi 
        }
        break;

    case 'DELETE':
        $PriorityId = $_GET['PriorityId'] ?? null;
        if ($PriorityId) {
            echo json_encode(['success' => deleteTaskPriority($PriorityId, $conn)]);
        } else {
            echo json_encode(['error' => 'PriorityId is required for deletion']);
        }
        break;
}

// Include CRUD functions
function getAllTaskPriority($language, $conn) {
    $sql = "SELECT PriorityId, PriorityName FROM taskpriority WHERE Language = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$language]);  
    return $stmt->fetchAll(PDO::FETCH_ASSOC); 
}

// تعديل في دالة insertTaskStatus
function insertTaskPriority($PriorityName, $language, $conn) {
    $sql = "INSERT INTO taskpriority (PriorityName, Language) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$PriorityName, $language]);  // تمرير القيم مباشرة
    return true;
}

// تعديل في دالة updateTaskStatus
function updateTaskPriority($PriorityId, $PriorityName, $language, $conn) {
    $sql = "UPDATE taskpriority SET PriorityName = ? WHERE PriorityId = ? AND Language = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$PriorityName, $PriorityId, $language]);  
    return true;
}

// تعديل في دالة deleteTaskStatus
function deleteTaskPriority($PriorityId, $conn) {
    $sql = "DELETE FROM taskpriority WHERE PriorityId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$PriorityId]);  
    return true;
}
?>
