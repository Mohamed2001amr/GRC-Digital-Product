<?php
include(__DIR__ . "/../APIConfig/Config.php");
header('Content-Type: application/json');

// Get HTTP method and route request to the right function
$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents("php://input"), true);


switch ($method) {
    case 'POST':
        // Insert new TaskType
        $data = json_decode(file_get_contents("php://input"), true);
        $results = []; // To store the result of each assignment insertion
    
        if (isset($data['assignments']) && is_array($data['assignments'])) {
            foreach ($data['assignments'] as $assignment) {
                $AssignedTo = $assignment['AssignedTo'];
                $AssignmentReason = $assignment['AssignmentReason'];
                $AssignmentDate = $assignment['AssignmentDate'];
                $AssignedBy = $assignment['AssignedBy'];
    
                // Convert the date to MySQL format (YYYY-MM-DD)
                $AssignmentDate = date('Y-m-d', strtotime($AssignmentDate));
    
                // Call the insert function for each assignment and collect the result
                $results[] = [
                    'AssignedTo' => $AssignedTo,
                    'success' => insertTaskAssigsment(
                        $data['TaskId'],
                        $AssignedTo,
                        $AssignedBy,
                        $AssignmentDate,
                        $AssignmentReason,
                        $conn
                    )
                ];
            }
        }
    
        // Return the results of all insert operations
        echo json_encode(['success' => true, 'results' => $results]);
        break;
    
    case 'GET':
        if (!isset($_GET['taskId'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid or missing TaskId']);
            exit;
        }
    
        // Sanitize the taskId to ensure it's an integer
        $taskId = intval($_GET['taskId']);  
        // Call the function with the correct variable name
        echo json_encode(getAllAssignmentByTaskId($taskId, $conn));
        break;

    case 'PUT':
        // Update an existing TaskType
        parse_str(file_get_contents("php://input"), $_PUT);
        $AssignmentId = $_GET['AssignmentId'] ?? null;
        if ($AssignmentId) {
            echo json_encode(['success' => updateTaskAssignment($AssignmentId, $_PUT['TaskId'], $_PUT['AssignedTo'],$_PUT['AssignedBy'],$_PUT['AssignmentDate'], $conn)]);
        } else {
            echo json_encode(['error' => 'AssignmentId is required for updating']);//getmessagefromapi 
        }
        break;

    case 'DELETE':
        $TaskId = $data['TaskId'] ?? null; // الحصول على TaskId من البيانات المرسلة
        $assignments = $data['assignments'] ?? [];
    
        if ($TaskId) {
            // حذف جميع التعيينات المتعلقة بـ TaskId
            $success = deleteTaskAssignmentsByTaskId($TaskId, $conn);
            if ($success) {
                echo json_encode(['success' => true, 'message' => 'All assignments for the task have been deleted.']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to delete assignments for the task.']);
            }
        } elseif (!empty($assignments)) {
            // حذف التعيينات بناءً على AssignmentId
            $responses = [];
            foreach ($assignments as $assignment) {
                $AssignmentId = $assignment['AssignmentId'] ?? null;
                if ($AssignmentId) {
                    $responses[] = [
                        'AssignmentId' => $AssignmentId,
                        'success' => deleteTaskAssignment($AssignmentId, $conn),
                    ];
                } else {
                    $responses[] = [
                        'AssignmentId' => null,
                        'success' => false,
                        'error' => 'AssignmentId is missing',
                    ];
                }
            }
            echo json_encode(['success' => true, 'results' => $responses]);
        } else {
            echo json_encode(['error' => 'No TaskId or assignments provided for deletion']);
        }
        break;
        
        
}

// Include CRUD functions
function getAllAssignmentByTaskId($TaskId, $conn) {
    $sql = "SELECT AssignmentId, AssignedTo, AssignedBy, AssignmentDate, AssignmentReason 
            FROM taskassignment WHERE TaskId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$TaskId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// تعديل في دالة insertTaskCategory
function insertTaskAssigsment($TaskId, $AssignedTo, $AssignedBy, $AssignmentDate, $AssignmentReason, $conn) {
    // Prepare SQL query to insert task assignment
    $sql = "INSERT INTO taskassignment (TaskId, AssignedTo, AssignedBy, AssignmentDate, AssignmentReason) VALUES (?, ?, ?, ?, ?)";
    
    // Prepare and execute statement
    $stmt = $conn->prepare($sql);
    $stmt->execute([$TaskId, $AssignedTo, $AssignedBy, $AssignmentDate, $AssignmentReason]);

    return true;

}



// تعديل في دالة updateTaskCategory
function updateTaskAssignment($AssignmentId,$TaskId, $AssignedTo, $AssignedBy , $AssignmentDate, $conn) {
    $sql = "UPDATE taskassignment SET TaskId = ? ,AssignedTo = ?, AssignedBy = ?, AssignmentDate = ? WHERE AssignmentId = ? AND Language = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$TaskId,$AssignedTo, $AssignedBy , $AssignmentDate, $AssignmentId]);  
    return true;
}

// تعديل في دالة deleteTaskCategory
function deleteTaskAssignment($AssignmentId, $conn) {
    $sql = "DELETE FROM taskassignment WHERE AssignmentId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$AssignmentId]);  
    return true;
}
function deleteTaskAssignmentsByTaskId($TaskId, $conn) {
    $sql = "DELETE FROM taskassignment WHERE TaskId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$TaskId]);
    return true;
}

?>
