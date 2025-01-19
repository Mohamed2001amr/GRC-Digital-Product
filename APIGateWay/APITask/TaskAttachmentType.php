<?php
include(__DIR__ . "/../APIConfig/Config.php");
header('Content-Type: application/json');

// Get HTTP method and route request to the right function
$method = $_SERVER['REQUEST_METHOD'];


switch ($method) {
    case 'POST':
        // Insert new TaskTAttachmentype
        $data = json_decode(file_get_contents("php://input"), true);
        echo json_encode(['success' => insertTaskAttachmentType($data['TypeName'], $data['Language'], $conn)]);
        break;


    case 'GET':
        // If no language is specified, default to 'EN'
        $language = !empty($_GET['lang']) ? $_GET['lang'] : 'EN';
        echo json_encode(value: getAllTaskAttachmentTypes($language, $conn));//getbypassingparmeter
        break;

    case 'PUT':
        // Update an existing TaskType
        parse_str(file_get_contents("php://input"), $_PUT);
        $TypeId = $_GET['TypeId'] ?? null;
        $language = !empty($_GET['lang']) ? $_GET['lang'] : 'EN';
        if ($TypeId) {
            echo json_encode(['success' => updateTaskAttachmentType($TypeId, $_PUT['TypeName'], $_PUT['Language'] ?? 'EN', $conn)]);
        } else {
            echo json_encode(['error' => 'TypeId is required for updating']);//getmessagefromapi 
        }
        break;

    case 'DELETE':
        $TypeId = $_GET['TypeId'] ?? null;
        if ($TypeId) {
            echo json_encode(['success' => deleteTaskAttachmentType($TypeId, $conn)]);
        } else {
            echo json_encode(['error' => 'TypeId is required for deletion']);
        }
        break;
}

// Include CRUD functions
function getAllTaskAttachmentTypes($language, $conn) {
    $sql = "SELECT TypeId, TypeName FROM taskattachmenttype WHERE Language = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$language]);  
    return $stmt->fetchAll(PDO::FETCH_ASSOC); 
}

// تعديل في دالة insertTaskType
function insertTaskAttachmentType($TypeName, $language, $conn) {
    $sql = "INSERT INTO taskattachmenttype (TypeName, Language) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$TypeName, $language]);  // تمرير القيم مباشرة
    return true;    
}

// تعديل في دالة updateTaskType
function updateTaskAttachmentType($TypeId, $TypeName, $language, $conn) {
    $sql = "UPDATE taskattachmenttype SET TypeName = ? WHERE TypeId = ? AND Language = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$TypeName, $TypeId, $language]);  
    return true;
}

// تعديل في دالة deleteTaskType
function deleteTaskAttachmentType($TypeId, $conn) {
    $sql = "DELETE FROM taskattachmenttype WHERE TypeId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$TypeId]);  
    return true;
}
?>
