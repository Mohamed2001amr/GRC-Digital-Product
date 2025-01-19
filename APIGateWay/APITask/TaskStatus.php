<?php
include(__DIR__ . "/../APIConfig/Config.php");
header('Content-Type: application/json');

// Get HTTP method and route request to the right function
$method = $_SERVER['REQUEST_METHOD'];


switch ($method) {
    case 'POST':
        // Insert new TaskType
        $data = json_decode(file_get_contents("php://input"), true);
        echo json_encode(['success' => insertTaskStatus($data['StatusName'], $data['Language'], $conn)]);
        break;


    case 'GET':
        // If no language is specified, default to 'EN'
        $language = !empty($_GET['lang']) ? $_GET['lang'] : 'EN';
        echo json_encode(value: getAllTaskStatus($language, $conn));//getbypassingparmeter
        break;

    case 'PUT':
        // Update an existing TaskType
        parse_str(file_get_contents("php://input"), $_PUT);
        $TypeId = $_GET['StatusId'] ?? null;
        $language = !empty($_GET['lang']) ? $_GET['lang'] : 'EN';
        if ($TypeId) {
            echo json_encode(['success' => updateTaskStatus($TypeId, $_PUT['StatusName'], $_PUT['Language'] ?? 'EN', $conn)]);
        } else {
            echo json_encode(['error' => 'StatusId is required for updating']);//getmessagefromapi 
        }
        break;

    case 'DELETE':
        $TypeId = $_GET['StatusId'] ?? null;
        if ($TypeId) {
            echo json_encode(['success' => deleteTaskStatus($TypeId, $conn)]);
        } else {
            echo json_encode(['error' => 'StatusId is required for deletion']);
        }
        break;
}

// Include CRUD functions
function getAllTaskStatus($language, $conn) {
    $sql = "SELECT StatusId, StatusName FROM taskstatus WHERE Language = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$language]);  
    return $stmt->fetchAll(PDO::FETCH_ASSOC); 
}

// تعديل في دالة insertTaskStatus
function insertTaskStatus($StatusName, $language, $conn) {
    $sql = "INSERT INTO taskstatus (StatusName, Language) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$StatusName, $language]);  // تمرير القيم مباشرة
    return true;
}

// تعديل في دالة updateTaskStatus
function updateTaskStatus($StatusId, $StatusName, $language, $conn) {
    $sql = "UPDATE taskstatus SET StatusName = ? WHERE StatusId = ? AND Language = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$StatusName, $StatusId, $language]);  
    return true;
}

// تعديل في دالة deleteTaskStatus
function deleteTaskStatus($StatusId, $conn) {
    $sql = "DELETE FROM taskstatus WHERE StatusId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$StatusId]);  
    return true;
}
?>
