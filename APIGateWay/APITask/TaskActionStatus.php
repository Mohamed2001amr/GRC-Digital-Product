<?php
include(__DIR__ . "/../APIConfig/Config.php");
header('Content-Type: application/json');

// Get HTTP method and route request to the right function
$method = $_SERVER['REQUEST_METHOD'];


switch ($method) {
    case 'POST':
        // Insert new TaskTAttachmentype
        $data = json_decode(file_get_contents("php://input"), true);
        echo json_encode(['success' => insertTaskActionStatus($data['StatusName'], $data['Language'], $conn)]);
        break;


    case 'GET':
        // If no language is specified, default to 'EN'
        $language = !empty($_GET['lang']) ? $_GET['lang'] : 'EN';
        echo json_encode(value: getAllTaskActionStatus($language, $conn));//getbypassingparmeter
        break;

    case 'PUT':
        // Update an existing TaskType
        parse_str(file_get_contents("php://input"), $_PUT);
        $StatusId = $_GET['StatusId'] ?? null;
        $language = !empty($_GET['lang']) ? $_GET['lang'] : 'EN';
        if ($StatusId) {
            echo json_encode(['success' => updateTaskActionStatus($StatusId, $_PUT['TypeName'], $_PUT['Language'] ?? 'EN', $conn)]);
        } else {
            echo json_encode(['error' => 'TypeId is required for updating']);//getmessagefromapi 
        }
        break;

    case 'DELETE':
        $TypeId = $_GET['TypeId'] ?? null;
        if ($TypeId) {
            echo json_encode(['success' => deleteTaskActionStatus($TypeId, $conn)]);
        } else {
            echo json_encode(['error' => 'TypeId is required for deletion']);
        }
        break;
}

// Include CRUD functions
function getAllTaskActionStatus($language, $conn) {
    $sql = "SELECT StatusId, StatusName FROM taskactionstatus WHERE Language = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$language]);  
    return $stmt->fetchAll(PDO::FETCH_ASSOC); 
}

// تعديل في دالة insertTaskType
function insertTaskActionStatus($StatusName, $language, $conn) {
    $sql = "INSERT INTO taskactionstatus (StatusName, Language) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$StatusName, $language]);  // تمرير القيم مباشرة
    return true;    
}

// تعديل في دالة updateTaskType
function updateTaskActionStatus($StatusId, $StatusName, $language, $conn) {
    $sql = "UPDATE taskactionstatus SET StatusName = ? WHERE StatusId = ? AND Language = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$StatusName, $StatusId, $language]);  
    return true;
}


function deleteTaskActionStatus($StatusId, $conn) {
    $sql = "DELETE FROM taskactionstatus WHERE StatusId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$StatusId]);  
    return true;
}
?>
