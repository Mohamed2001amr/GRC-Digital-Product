<?php
include(__DIR__ . "/../APIConfig/Config.php");
header('Content-Type: application/json');

// Get HTTP method and route request to the right function
$method = $_SERVER['REQUEST_METHOD'];


switch ($method) {
    case 'POST':
        // Insert new TaskType
        $data = json_decode(file_get_contents("php://input"), true);
        echo json_encode(['success' => insertTaskType($data['TypeName'], $data['Language'], $conn)]);
        break;


    case 'GET':
        // If no language is specified, default to 'EN'
        $language = !empty($_GET['lang']) ? $_GET['lang'] : 'EN';
        echo json_encode(value: getAllTaskTypes($language, $conn));//getbypassingparmeter
        break;

    case 'PUT':
        // Update an existing TaskType
        parse_str(file_get_contents("php://input"), $_PUT);
        $TypeId = $_GET['TypeId'] ?? null;
        $language = !empty($_GET['lang']) ? $_GET['lang'] : 'EN';
        if ($TypeId) {
            echo json_encode(['success' => updateTaskType($TypeId, $_PUT['TypeName'], $_PUT['Language'] ?? 'EN', $conn)]);
        } else {
            echo json_encode(['error' => 'TypeId is required for updating']);//getmessagefromapi 
        }
        break;

    case 'DELETE':
        $TypeId = $_GET['TypeId'] ?? null;
        if ($TypeId) {
            echo json_encode(['success' => deleteTaskType($TypeId, $conn)]);
        } else {
            echo json_encode(['error' => 'TypeId is required for deletion']);
        }
        break;
}

// Include CRUD functions
function getAllTaskTypes($language, $conn) {
    $sql = "SELECT TypeId, TypeName FROM tasktype WHERE Language = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$language]);  
    return $stmt->fetchAll(PDO::FETCH_ASSOC); 
}

// تعديل في دالة insertTaskType
function insertTaskType($TypeName, $language, $conn) {
    $sql = "INSERT INTO tasktype (TypeName, Language) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$TypeName, $language]);  // تمرير القيم مباشرة
    return true;    
}

// تعديل في دالة updateTaskType
function updateTaskType($TypeId, $TypeName, $language, $conn) {
    $sql = "UPDATE tasktype SET TypeName = ? WHERE TypeId = ? AND Language = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$TypeName, $TypeId, $language]);  
    return true;
}

// تعديل في دالة deleteTaskType
function deleteTaskType($TypeId, $conn) {
    $sql = "DELETE FROM tasktype WHERE TypeId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$TypeId]);  
    return true;
}
?>
