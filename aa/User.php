<?php
include 'Config.php';
header('Content-Type: application/json');

// Get HTTP method and route request to the right function
$method = $_SERVER['REQUEST_METHOD'];


switch ($method) {
    case 'POST':
        // Insert new TaskType
        $data = json_decode(file_get_contents("php://input"), true);
        echo json_encode(['success' => insertUser($data['UserName'], $data['Email'],$data['Role'],$conn)]);
        break;


    case 'GET':
        echo json_encode(value: getAllUser($conn));//getbypassingparmeter
        break;

    case 'PUT':
        // Update an existing TaskType
        parse_str(file_get_contents("php://input"), $_PUT);
        $UserID = $_GET['UserID'] ?? null;
        if ($UserID) {
            echo json_encode(['success' => updateUser($UserID, $_PUT['UserName'], $_PUT['Email'],$_PUT['Role'], $conn)]);
        } else {
            echo json_encode(['error' => 'UserID is required for updating']);//getmessagefromapi 
        }
        break;

    case 'DELETE':
        $TypeId = $_GET['UserID'] ?? null;
        if ($TypeId) {
            echo json_encode(['success' => deleteUser($UserID, $conn)]);
        } else {
            echo json_encode(['error' => 'UserID is required for deletion']);
        }
        break;
}

// Include CRUD functions
function getAllUser($conn) {
    $sql = "SELECT UserID, UserName FROM user ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();  
    return $stmt->fetchAll(PDO::FETCH_ASSOC); 
}

// تعديل في دالة InsertNewUser
function insertUser($UserName, $Email,$Role, $conn) {
    $sql = "INSERT INTO user (UserName,Email,Role) VALUES (?, ?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$UserName, $Email,$Role]);  // تمرير القيم مباشرة
    return true;    
}

// تعديل في دالة UpdateCurrentUser
function updateUser($UserID, $UserName, $Email,$Role, $conn) {
    $sql = "UPDATE user SET UserName = ?, Email = ?, Role = ? WHERE UserID = ?";    
    $stmt = $conn->prepare($sql);
    $stmt->execute([$UserName, $Email, $Role, $UserID]);
    return true;
}

// تعديل في دالة DeleteCurrentUser
function deleteUser($UserID, $conn) {
    $sql = "DELETE FROM user WHERE UserID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$UserID]);  
    return true;
}
?>
