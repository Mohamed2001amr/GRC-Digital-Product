<?php
include(__DIR__ . "/../APIConfig/Config.php");
header('Content-Type: application/json');

// Get HTTP method and route request to the right function
$method = $_SERVER['REQUEST_METHOD'];


switch ($method) {
    case 'POST':
        // Insert new TaskType
        $data = json_decode(file_get_contents("php://input"), true);
        echo json_encode(['success' => insertTaskCategory($data['CategoryName'], $data['Language'], $conn)]);
        break;


    case 'GET':
        // If no language is specified, default to 'EN'
        $language = !empty($_GET['lang']) ? $_GET['lang'] : 'EN';
        echo json_encode(getAllTaskCategory($language, $conn));
        break;

    case 'PUT':
        // Update an existing TaskType
        parse_str(file_get_contents("php://input"), $_PUT);
        $PriorityId = $_GET['CategoryId'] ?? null;
        $language = !empty($_GET['lang']) ? $_GET['lang'] : 'EN';
        if ($PriorityId) {
            echo json_encode(['success' => updateTaskCategory($CategoryId, $_PUT['CategoryName'], $_PUT['Language'] ?? 'EN', $conn)]);
        } else {
            echo json_encode(['error' => 'CategoryId is required for updating']);//getmessagefromapi 
        }
        break;

    case 'DELETE':
        $PriorityId = $_GET['CategoryId'] ?? null;
        if ($CategoryId) {
            echo json_encode(['success' => deleteTaskPriority($PriorityId, $conn)]);
        } else {
            echo json_encode(['error' => 'PriorityId is required for deletion']);
        }
        break;
}

// Include CRUD functions
function getAllTaskCategory($language, $conn) {
    $sql = "SELECT CategoryId, CategoryName FROM taskcategory WHERE Language = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$language]);  
    return $stmt->fetchAll(PDO::FETCH_ASSOC); 
}

// تعديل في دالة insertTaskCategory
function insertTaskCategory($CategoryName, $language, $conn) {
    $sql = "INSERT INTO taskcategory (CategoryName, Language) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$CategoryName, $language]);  // تمرير القيم مباشرة
    return true;
}

// تعديل في دالة updateTaskCategory
function updateTaskCategory($CategoryId, $CategoryName, $language, $conn) {
    $sql = "UPDATE taskcategory SET CategoryName = ? WHERE CategoryId = ? AND Language = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$CategoryName, $CategoryId, $language]);  
    return true;
}

// تعديل في دالة deleteTaskCategory
function deleteTaskCategory($CategoryId, $conn) {
    $sql = "DELETE FROM taskcategory WHERE CategoryId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$CategoryId]);  
    return true;
}
?>
