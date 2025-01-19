<?php
include(__DIR__ . "/../APIConfig/Config.php");
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
// Get HTTP method and route request to the right function
$method = $_SERVER['REQUEST_METHOD'];
switch ($method) {
    case 'POST':
        // Insert new TaskType
        if (isset($data['TaskName'], $data['TaskDescription'], $data['CreatedBy'], $data['CreationDate'], 
                  $data['TaskType'], $data['TaskCategoryId'], $data['PriorityId'], $data['TaskStage'], $data['TaskStatus'], $data['StatusDate'])) {
            $TaskId =  insertTask(
                $data['TaskName'],
                $data['TaskDescription'],
                $data['CreatedBy'],
                $data['CreationDate'],
                $data['AssignedBy']??null,
                $data['AssignmentDate']??null,
                $data['AssignedTo']??null, 
                $data['TaskType'],
                $data['TaskCategoryId'],
                $data['RefrenceId']??null,
                $data['DueDate']??null,
                $data['PriorityId'],
                $data['TaskStage'],
                $data['TaskStatus'],
                $data['StatusDate'],
                $conn
            );
            echo json_encode(['success' => true, 'TaskId' => $TaskId]);
        } else {
            echo json_encode(['error' => 'Missing required fields']);
        }
        break;
        


    case 'GET':
        $language = !empty($_GET['lang']) ? $_GET['lang'] : 'EN';
    
        if (!empty($_GET['taskId'])) {
            // إذا كان يوجد taskId، استرجاع تفاصيل المهمة بناءً على الـ taskId
            $taskId = $_GET['taskId'];
            echo json_encode(getTaskById($conn, $taskId, $language));
        } else {
            // إذا لم يتم تمرير taskId، استرجاع كل المهام
            echo json_encode(getAllTask($conn, $language));
        }
        break;

    case 'PUT':
        // Parse input data
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['TaskId'])) {
            echo json_encode(['error' => 'TaskId is required']);
            exit;
        }
    
        // Extract variables with validation
        $TaskId = $data['TaskId'];
        $TaskName = !empty($data['TaskName']) && strlen($data['TaskName']) <= 255 ? $data['TaskName'] : null;
        $TaskDescription = $data['TaskDescription'] ?? null;
        $TaskType = $data['TaskType'] ?? null;
        $TaskCategoryId = $data['TaskCategoryId'] ?? null;
        $RefrenceId = $data['RefrenceId'] ?? null;
        $DueDate = $data['DueDate'] ?? null;
        $PriorityId = $data['PriorityId'] ?? null;
        $TaskStage = $data['TaskStage'] ?? null;
        $TaskStatus = $data['TaskStatus'] ?? null;
        $StatusDate = $data['StatusDate'] ?? null;
    
        // Check required fields
        if (!$TaskName) {
            echo json_encode(['error' => 'TaskName is invalid or missing']);
            exit;
        }
    
        // Call update function
        $success = updateTask(
            $TaskId,
            $TaskName,
            $TaskDescription,
            $TaskType,
            $TaskCategoryId,
            $RefrenceId,
            $DueDate,
            $PriorityId,
            $TaskStage,
            $TaskStatus,
            $StatusDate,
            $conn
        );
    
        echo json_encode(['success' => $success]);
        break;
        
  
    case 'DELETE':
        $input = json_decode(file_get_contents('php://input'), true);
        $TaskId = $input['TaskId'] ;  // قراءة TaskId من جسم الطلب
        if ($TaskId) {
            echo json_encode(['success' => deleteTask($TaskId, $conn)]);
        } else {
            echo json_encode(['error' => 'TaskId is required for deletion']);
        }
        break;

    default:
        echo json_encode(['error' => 'Invalid HTTP method']);
        break;
    
}

// Include CRUD functions
function getAllTask($conn, $language) {
    
    $sql = "SELECT t.*, s.StatusName ,tc.CategoryName, tp.TypeName,ts.StageName,tpr.PriorityName 
            FROM task t
            LEFT JOIN 
                taskstatus s 
                ON t.TaskStatus = s.StatusId AND s.Language = :language
            LEFT JOIN 
                taskcategory tc 
                ON t.TaskCategoryId = tc.CategoryId AND tc.Language = :language
            LEFT JOIN 
                taskpriority tpr 
                ON t.PriorityId = tpr.PriorityId AND tpr.Language = :language

            LEFT JOIN 
                taskstage ts
                ON t.TaskStage = ts.StageId AND ts.Language = :language
            LEFT JOIN 
                tasktype tp
                ON t.TaskType = tp.TypeId AND tp.Language = :language";
             // إضافة شرط اللغة في الانضمام
             
    $stmt = $conn->prepare($sql);
    // تمرير المتغيرات مباشرة إلى execute
    $stmt->execute([':language' => $language]);  
    return $stmt->fetchAll(PDO::FETCH_ASSOC); 
}
function getTaskById($conn, $taskId, $language) {
    // استعلام لجلب تفاصيل المهمة بناءً على الـ TaskId
    $sql = "SELECT t.*, s.StatusName, tc.CategoryName, tp.TypeName, ts.StageName, tpr.PriorityName 
            FROM task t
            LEFT JOIN 
                taskstatus s 
                ON t.TaskStatus = s.StatusId AND s.Language = :language
            LEFT JOIN 
                taskcategory tc 
                ON t.TaskCategoryId = tc.CategoryId AND tc.Language = :language
            LEFT JOIN 
                taskpriority tpr 
                ON t.PriorityId = tpr.PriorityId AND tpr.Language = :language
            LEFT JOIN 
                taskstage ts
                ON t.TaskStage = ts.StageId AND ts.Language = :language
            LEFT JOIN 
                tasktype tp
                ON t.TaskType = tp.TypeId AND tp.Language = :language
            WHERE t.TaskId = :taskId"; // إضافة شرط لتحديد الـ TaskId
            
    // تحضير الاستعلام
    $stmt = $conn->prepare($sql);
    // تمرير المتغيرات مباشرة إلى execute
    $stmt->execute([':language' => $language, ':taskId' => $taskId]);  
    
    // إرجاع النتيجة إذا كانت موجودة
    return $stmt->fetch(PDO::FETCH_ASSOC); 
}

function insertTask($TaskName, $TaskDescription, $CreatedBy,$CreationDate,$AssignedBy, $AssignmentDate, $AssignedTo, $TaskType, $TaskCategoryId, 
                    $RefrenceId, $DueDate, $PriorityId, $TaskStage, $TaskStatus, 
                    $StatusDate, $conn) {
    $sql = "INSERT INTO task (TaskName, TaskDescription, CreatedBy,CreationDate,AssignedBy, AssignmentDate, AssignedTo, TaskType, TaskCategoryId,
                              RefrenceId, DueDate, PriorityId, TaskStage, TaskStatus, StatusDate) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    
    $stmt->execute([$TaskName, $TaskDescription, $CreatedBy,$CreationDate,$AssignedBy, $AssignmentDate, $AssignedTo, $TaskType, $TaskCategoryId, 
                    $RefrenceId, $DueDate, $PriorityId, $TaskStage, $TaskStatus, 
                    $StatusDate]);
    
    return $conn->lastInsertId();
}


function updateTask($TaskId, $TaskName, $TaskDescription, $TaskType, $TaskCategoryId, 
                    $RefrenceId, $DueDate, $PriorityId, $TaskStage, $TaskStatus, 
                    $StatusDate, $conn) {
    $sql = "UPDATE task 
    SET TaskName = ?, TaskDescription = ?, TaskType = ?, TaskCategoryId = ?, RefrenceId = ?, 
        DueDate = ?, PriorityId = ?, TaskStage = ?, TaskStatus = ?, StatusDate = ?
    WHERE TaskId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$TaskName, $TaskDescription, $TaskType, $TaskCategoryId, $RefrenceId, 
                    $DueDate, $PriorityId, $TaskStage, $TaskStatus, $StatusDate, $TaskId]);
    
    return true;
}

function deleteTask($TaskId, $conn) {
    $sql = "DELETE FROM task WHERE TaskId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$TaskId]);  
    return true;
}
?>

