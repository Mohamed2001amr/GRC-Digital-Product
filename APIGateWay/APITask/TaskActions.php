<?php
include(__DIR__ . "/../APIConfig/Config.php");
header('Content-Type: application/json');

// Get HTTP method and route request to the right function
$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents("php://input"), true);

switch ($method) {
    case 'POST':
        // Insert new Action
        $results = [];
        if (isset($data['actions']) && is_array($data['actions'])) {
            foreach ($data['actions'] as $action) {
                $ActionDetails = $action['ActionDetails'] ?? '';
                $ActionStatus = $action['ActionStatus'] ?? '';
                $ActionBy = $action['ActionBy'] ?? 1; // Default to 1 or validate
                $ActionStatusDate = $action['ActionStatusDate'] ?? date('Y-m-d');

                // Validate inputs
                if (!$ActionDetails || !$ActionStatus || !$ActionStatusDate) {
                    $results[] = [
                        'ActionDetails' => $ActionDetails,
                        'success' => false,
                        'message' => 'Missing required fields'
                    ];
                    continue;
                }

                // Call the insert function
                $insertSuccess = insertAction($data['TaskId'], $ActionDetails, $ActionStatus, $ActionBy, $ActionStatusDate, $conn);
                $results[] = [
                    'ActionDetails' => $ActionDetails,
                    'success' => $insertSuccess,
                    'message' => $insertSuccess ? 'Action inserted successfully' : 'Failed to insert action'
                ];
            }
        }
        echo json_encode(['success' => true, 'results' => $results]);
        break;

    case 'GET':
        $language = !empty($_GET['lang']) ? $_GET['lang'] : 'EN';
        if (!isset($_GET['taskId'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid or missing TaskId']);
            exit;
        }

        $taskId = intval($_GET['taskId']);
        echo json_encode(getAllActionsByTaskId($taskId,$language, $conn));
        break;

    case 'PUT':
    // قراءة بيانات الطلب
        $data = json_decode(file_get_contents("php://input"), true);
        $actions = $data['actions'] ?? [];

        if (!empty($actions)) {
            $responses = [];
            foreach ($actions as $action) {
                $ActionId = $action['ActionId'] ?? null;
                $ActionDetails = $action['ActionName'] ?? '';
                $ActionStatusName = $action['ActionStatus'] ?? '';
                $ActionBy = $action['ActionBy'] ?? 1; // القيمة الافتراضية
                $ActionStatusDate = $action['ActionStatusDate'] ?? date('Y-m-d');

                if ($ActionId) {
                    $success = updateAction(
                        $ActionId,
                        $data['TaskId'],
                        $ActionDetails,
                        $ActionStatusName,
                        $ActionBy,
                        $ActionStatusDate,
                        $conn
                    );
                    $responses[] = [
                        'ActionId' => $ActionId,
                        'success' => $success,
                    ];
                } else {
                    $responses[] = [
                        'ActionId' => null,
                        'success' => false,
                        'error' => 'ActionId is missing',
                    ];
                }
            }

            // إرسال الاستجابة النهائية
            echo json_encode([
                'responses' => $responses,
                'success' => !in_array(false, array_column($responses, 'success')),
            ]);
        } else {
            echo json_encode(['error' => 'No actions provided for update']);
        }
        break;  

        
    case 'DELETE':
        $TaskId = $data['TaskId'] ?? null;
        $actions = $data['actions'] ?? [];
    
        if (!empty($TaskId) && empty($actions)) {
            // حذف جميع الأكشنات المرتبطة بـ TaskId
            if (!is_numeric($TaskId)) {
                echo json_encode(['success' => false, 'error' => 'Invalid TaskId']);
                exit;
            }
    
            $success = deleteActionsByTaskId($TaskId, $conn);
            if ($success) {
                echo json_encode(['success' => true, 'message' => 'All actions for the task deleted successfully.']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to delete actions for the task.']);
            }
        } elseif (!empty($actions)) {
            // حذف الأكشنات باستخدام ActionId
            $responses = [];
            foreach ($actions as $action) {
                $ActionId = $action['ActionId'] ?? null;
                if ($ActionId) {
                    $responses[] = [
                        'ActionId' => $ActionId,
                        'success' => deleteAction($ActionId, $conn),
                    ];
                } else {
                    $responses[] = [
                        'ActionId' => null,
                        'success' => false,
                        'error' => 'ActionId is missing',
                    ];
                }
            }
            echo json_encode(['success' => true, 'results' => $responses]);
        } else {
            echo json_encode(['success' => false, 'error' => 'No TaskId or actions provided for deletion']);
        }
        break;
}

// Include CRUD functions
function getAllActionsByTaskId($TaskId,$language, $conn) {
    $sql = "SELECT t.* , s.StatusName
            FROM taskaction t
            LEFT JOIN taskactionstatus s 
                ON t.ActionStatus = s.StatusId AND s.Language = :language
            WHERE t.TaskId = :taskId";

    $stmt = $conn->prepare($sql);
    $stmt->execute([':taskId' => $TaskId, ':language' => $language]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function insertAction($TaskId, $ActionDetails, $ActionStatus, $ActionBy, $ActionStatusDate, $conn) {
    $sql = "INSERT INTO taskaction (TaskId, ActionDetails, ActionStatus, ActionBy, ActionStatusDate) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$TaskId, $ActionDetails, $ActionStatus, $ActionBy, $ActionStatusDate]);
    return true;
}


function updateAction($ActionId, $TaskId, $ActionDetails, $ActionStatusName, $ActionBy, $ActionStatusDate, $conn) {
    // الخطوة 1: الحصول على StatusID بناءً على StatusName
    $statusSql = "SELECT StatusID FROM taskactionStatus WHERE StatusName = ?";
    $statusStmt = $conn->prepare($statusSql);
    $statusStmt->execute([$ActionStatusName]);
    $statusRow = $statusStmt->fetch(PDO::FETCH_ASSOC);

    if ($statusRow && isset($statusRow['StatusID'])) {
        $ActionStatusID = $statusRow['StatusID'];

        // الخطوة 2: تحديث جدول taskaction باستخدام StatusID
        $sql = "UPDATE taskaction SET TaskId = ?, ActionDetails = ?, ActionStatus = ?, ActionBy = ?, ActionStatusDate = ? WHERE ActionId = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$TaskId, $ActionDetails, $ActionStatusID, $ActionBy, $ActionStatusDate, $ActionId]);
        return true;
    } 
}



// Function to delete all actions for a specific TaskId
function deleteActionsByTaskId($TaskId, $conn) {
    $sql = "DELETE FROM taskaction WHERE TaskId = ?";
    $stmt = $conn->prepare($sql);
    return $stmt->execute([$TaskId]);
}

// Function to delete a single action by ActionId
function deleteAction($ActionId, $conn) {
    $sql = "DELETE FROM taskaction WHERE ActionId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$ActionId]);
    return true;
}

?>

