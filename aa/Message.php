<?php
include 'Config.php';
header('Content-Type: application/json');

// Get HTTP method and route request to the right function
$method = $_SERVER['REQUEST_METHOD'];


switch ($method) {
    case 'POST':
        // Insert new Message
        $data = json_decode(file_get_contents("php://input"), true);
        echo json_encode(['success' => insertMessage($data['MessageName'], $data['Language'], $conn)]);
        break;


        case 'GET':
            // إذا لم يتم تحديد لغة، افتراضياً تكون 'EN'
        $MessageId = $_GET['MessageId'] ?? null;
        $language = !empty($_GET['lang']) ? $_GET['lang'] : 'EN';
    
        if ($MessageId) {
            // استدعاء الدالة للحصول على رسالة بناءً على ID
            $message = getMessageById($conn, $MessageId, $language);
            if ($message) {
                echo json_encode($message);
            } else {
                echo json_encode(['error' => 'Message not found']);
            }
        } else {
            // استدعاء الدالة للحصول على جميع الرسائل بلغة معينة
            echo json_encode(getAllMessage($language, $conn));
        }
        break;
        

    case 'PUT':
        // Update an existing MessageName
        parse_str(file_get_contents("php://input"), $_PUT);
        $PriorityId = $_GET['MessageId'] ?? null;
        $language = !empty($_GET['lang']) ? $_GET['lang'] : 'EN';
        if ($PriorityId) {
            echo json_encode(['success' => updateMessage($MessageId, $_PUT['MessageName'], $_PUT['Language'] ?? 'EN', $conn)]);
        } else {
            echo json_encode(['error' => 'MessageID is required for updating']);//getmessagefromapi 
        }
        break;

    case 'DELETE':
        $MessageId = $_GET['MessageId'] ?? null;
        if ($MessageId) {
            echo json_encode(['success' => deleteMessage($MessageId, $conn)]);
        } else {
            echo json_encode(['error' => 'MessageId is required for deletion']);
        }
        break;
}

// Include CRUD functions
function getAllMessage($language, $conn) {
    $sql = "SELECT MessageId, MessageName FROM message WHERE Language = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$language]);  
    return $stmt->fetchAll(PDO::FETCH_ASSOC); 
}
///not experment yet

function getMessageById($conn, $messageId, $language) {
    $sql = "SELECT MessageId, MessageName FROM message WHERE MessageId = ? AND Language = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$messageId, $language]);
    return $stmt->fetch(PDO::FETCH_ASSOC); // Return the fetched result
}


// تعديل في دالة insertMessage
function insertMessage($MessageName, $language, $conn) {
    $sql = "INSERT INTO message (MessageName, Language) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$MessageName, $language]);  // تمرير القيم مباشرة
    return true;
}

// تعديل في دالة updateMessage
function updateMessage($MessageId, $MessageName, $language, $conn) {
    $sql = "UPDATE message SET MessageName = ? WHERE MessageId = ? AND Language = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$MessageName, $MessageId, $language]);  
    return true;
}

// تعديل في دالة deleteTaskMessage
function deleteMessage($MessageId, $conn) {
    $sql = "DELETE FROM message WHERE MessageId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$MessageId]);  
    return true;
}
?>


