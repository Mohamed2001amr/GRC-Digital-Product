<?php
include(__DIR__ . "/../APIConfig/Config.php");
header('Content-Type: application/json');

// Get HTTP method and route request to the right function
$method = $_SERVER['REQUEST_METHOD'];


switch ($method) {
    case 'POST':
        // Insert new TaskType
        $data = json_decode(file_get_contents("php://input"), true);
        echo json_encode(['success' => insertTaskStage($data['StageName'], $data['Language'], $conn)]);
        break;


    case 'GET':
        // If no language is specified, default to 'EN'
        $language = !empty($_GET['lang']) ? $_GET['lang'] : 'EN';
        echo json_encode(value: getAllTaskStage($language, $conn));//getbypassingparmeter
        break;

    case 'PUT':
        // Update an existing TaskType
        parse_str(file_get_contents("php://input"), $_PUT);
        $TypeId = $_GET['StageId'] ?? null;
        $language = !empty($_GET['lang']) ? $_GET['lang'] : 'EN';
        if ($TypeId) {
            echo json_encode(['success' => updateTaskStage($TypeId, $_PUT['StageName'], $_PUT['Language'] ?? 'EN', $conn)]);
        } else {
            echo json_encode(['error' => 'StatusId is required for updating']);//getmessagefromapi 
        }
        break;

    case 'DELETE':
        $TypeId = $_GET['StageId'] ?? null;
        if ($TypeId) {
            echo json_encode(['success' => deleteTaskStatus($TypeId, $conn)]);
        } else {
            echo json_encode(['error' => 'StageId is required for deletion']);
        }
        break;
}

// Include CRUD functions
function getAllTaskStage($language, $conn) {
    $sql = "SELECT StageId, StageName FROM taskstage WHERE Language = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$language]);  
    return $stmt->fetchAll(PDO::FETCH_ASSOC); 
}

// تعديل في دالة insertTaskStatus
function insertTaskStage($StageName, $language, $conn) {
    $sql = "INSERT INTO taskstage (StageName, Language) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$StageName, $language]);  // تمرير القيم مباشرة
    return true;
}

// تعديل في دالة updateTaskStatus
function updateTaskStage($StageId, $StageName, $language, $conn) {
    $sql = "UPDATE taskstage SET StageName = ? WHERE StageId = ? AND Language = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$StageName, $StageId, $language]);  
    return true;
}

// تعديل في دالة deleteTaskStatus
function deleteTaskStage($StageId, $conn) {
    $sql = "DELETE FROM taskstage WHERE StageId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$StageId]);  
    return true;
}
?>
