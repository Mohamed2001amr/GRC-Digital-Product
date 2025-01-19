<?php
include(__DIR__ . "/../APIConfig/Config.php");
header('Content-Type: application/json');

// Get HTTP method and route request to the right function
$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true);

switch ($method) {
    case 'POST':
        // Insert new TaskAttachment
        $data = json_decode(file_get_contents("php://input"), true);

        // Validate TaskId
        if (!isset($data['TaskId']) || !is_numeric($data['TaskId']) || $data['TaskId'] <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid or missing TaskId']);
            exit;
        }

        $TaskId = intval($data['TaskId']);
        $rootPath = "C:\\xampp\\htdocs\\APIGateWay\\APITask"; // المسار الكامل إلى مجلد المشروع
        $localPath = "http://localhost/APIGateWay/APITask/tasks";
        $directory = $rootPath . DIRECTORY_SEPARATOR . 'tasks'; // المسار الكامل لمجلد tasks


        // Create tasks directory if it doesn't exist
        if (!is_dir($directory) && !mkdir($directory, 0755, true)) {
            echo json_encode(['success' => false, 'message' => 'Failed to create tasks directory']);
            exit;
        }

        $folderPath = $directory . DIRECTORY_SEPARATOR . "Task_$TaskId";
        $folderlocalpath =$localPath . '/' ."Task_$TaskId";

        // Create task-specific folder if it doesn't exist
        if (!is_dir($folderPath) && !mkdir($folderPath, 0755)) {
            echo json_encode(['success' => false, 'message' => 'Failed to create task folder']);
            exit;
        }

        $savedFiles = [];
        if (isset($data['attachments']) && is_array($data['attachments'])) {
            foreach ($data['attachments'] as $attachment) {
                if (isset($attachment['base64'], $attachment['type'], $attachment['description'])) {
                    $decodedData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $attachment['base64']));
                    $fileName = uniqid("attachment_") . ".jpeg"; // Change extension if necessary
                    $filePath = $folderPath . DIRECTORY_SEPARATOR . $fileName;
                    $fileLocalPath = $folderlocalpath. '/' .$fileName;

                    if (file_put_contents($filePath, $decodedData) !== false) {
                        // Insert attachment info into the database
                        $AttachmentType = $attachment['type'];
                        $AttachmentName = $attachment['description'];
                        $UploadedBy = $attachment['UploadedBy'];  // Assuming the uploaded user info is passed
                        $UploadedDate = $attachment['UploadedDate'];  // Current date and time

                        $attachmentInserted = insertTaskAttachment($TaskId, $AttachmentType, $AttachmentName, $filePath, $UploadedBy, $UploadedDate,$fileLocalPath,$fileName, $conn);
                        
                        if ($attachmentInserted) {
                            $savedFiles[] = [
                                'AttachmentName' => $fileName,
                                'AttachmentPath' => $filePath
                            ];
                        } else {
                            echo json_encode(['success' => false, 'message' => "Failed to insert attachment in database"]);
                            exit;
                        }
                    } else {
                        echo json_encode(['success' => false, 'message' => "Failed to save file: $fileName"]);
                        exit;
                    }
                }
            }
        }

        echo json_encode([
            'success' => true,
            'message' => 'Task folder and attachments created successfully',
            'folderPath' => $folderPath,
            'files' => $savedFiles,
        ]);
        break;

    case 'GET':
        $language = !empty($_GET['lang']) ? $_GET['lang'] : 'EN';
    
        if (!isset($_GET['taskId'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid or missing TaskId']);
            exit;
        }
    
        // Sanitize the taskId to ensure it's an integer
        $taskId = intval($_GET['taskId']);  
        // Call the function with the correct variable name
        echo json_encode(getAllTaskAttachment($taskId,$language, $conn));
        break;
        
        

    case 'PUT':
        // Update an existing TaskAttachment
        parse_str(file_get_contents("php://input"), $_PUT);
        $AttachmentId = $_GET['AttachmentId'] ?? null;
        if ($AttachmentId) {
            echo json_encode(['success' => updateTaskAttachment($AttachmentId, $_PUT['TaskId'], $_PUT['AttachmentType'], $_PUT['AttachmentName'], $_PUT['AttachmentPath'], $_PUT['UploadedBy'], $_PUT['UploadedDate'], $conn)]);
        } else {
            echo json_encode(['error' => 'AttachmentId is required for updating']);
        }
        break;


    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"), true);
        $TaskId = $data['TaskId'] ?? null;
        $attachments = $data['attachments'] ?? [];
        $responseMessages = [];
    
        // الحالة 1: إذا تم تقديم TaskId
        if ($TaskId) {
            $folderPath = "tasks/Task_{$TaskId}";
            $folderDeleteResult = ['success' => true]; // افتراضيًا نجاح
    
            // حذف المجلد والملفات بداخله
            if (is_dir($folderPath)) {
                $folderDeleteResult = deleteFolder($folderPath);
            }
    
            if ($folderDeleteResult['success']) {
                // إذا تم حذف المجلد، احذف السجلات من قاعدة البيانات
                $dbDeleteResult = deleteTaskAttachmentsByTaskId($TaskId, $conn);
    
                if ($dbDeleteResult['success']) {
                    $responseMessages[] = 'Task folder and database records deleted successfully.';
                } else {
                    $responseMessages[] = $dbDeleteResult['message'];
                }
            } else {
                $responseMessages[] = $folderDeleteResult['message'];
            }
        }
    
        // الحالة 2: إذا تم تقديم قائمة المرفقات
        if (!empty($attachments)) {
            foreach ($attachments as $attachment) {
                $AttachmentId = $attachment['AttachmentId'] ?? null;
                $AttachmentPath = $attachment['Attachmentlocalpath'] ?? null;
    
                if ($AttachmentId && $AttachmentPath) {
                    // حذف الملف
                    $fileDeleteResult = deleteFile($AttachmentPath);
    
                    // إذا تم حذف الملف، احذف السجل من قاعدة البيانات
                    if ($fileDeleteResult['success']) {
                        $recordDeleteResult = deleteTaskAttachment($AttachmentId, $conn);
                        if ($recordDeleteResult['success']) {
                            $responseMessages[] = "Attachment ID {$AttachmentId} and file deleted successfully.";
                        } else {
                            $responseMessages[] = $recordDeleteResult['message'];
                        }
                    } else {
                        $responseMessages[] = $fileDeleteResult['message'];
                    }
                } else {
                    $responseMessages[] = 'AttachmentId and AttachmentPath are required for deletion.';
                }
            }
        }
    
        // إذا لم يتم تقديم `TaskId` أو `attachments`
        if (!$TaskId && empty($attachments)) {
            $responseMessages[] = 'TaskId or attachments are required for deletion.';
        }
    
        // إرسال الرد النهائي
        if (!empty($responseMessages)) {
            echo json_encode(['success' => true, 'messages' => $responseMessages]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No deletion actions were performed.']);
        }
        break;
        
    
    
}

// Include CRUD functions
function getAllTaskAttachment($TaskId, $language, $conn) {
    $sql = "SELECT t.*, tat.TypeName 
            FROM taskattachment t 
            LEFT JOIN taskattachmenttype tat 
                ON t.AttachmentType = tat.TypeId AND tat.Language = :language 
            WHERE TaskId = :taskId";  // Using named parameters for both

    $stmt = $conn->prepare($sql);
    $stmt->execute([':language' => $language, ':taskId' => $TaskId]);  // Binding values to named parameters
    $attachments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Modify path to be appropriate for the front-end
    foreach ($attachments as &$attachment) {
        $attachment['AttachmentPath'] = str_replace('\\', '/', $attachment['AttachmentPath']);
    }

    return $attachments;
}



function insertTaskAttachment($TaskId, $AttachmentType, $AttachmentName, $AttachmentPath, $UploadedBy, $UploadedDate,$fileLocalPath,$fileName, $conn) {
    $sql = "INSERT INTO taskattachment (TaskId, AttachmentType, AttachmentName, AttachmentPath, UploadedBy, UploadedDate,Attachmentlocalpath,FileName) VALUES (?, ?, ?, ?, ?, ?,?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$TaskId, $AttachmentType, $AttachmentName, $AttachmentPath, $UploadedBy, $UploadedDate, $fileLocalPath,$fileName]);  
    return true;
}

function updateTaskAttachment($AttachmentId, $TaskId, $AttachmentType, $AttachmentName, $AttachmentPath, $UploadedBy, $UploadedDate, $conn) {
    $sql = "UPDATE taskattachment SET TaskId = ?, AttachmentType = ?, AttachmentName = ?, AttachmentPath = ?, UploadedBy = ?, UploadedDate = ? WHERE AttachmentId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$TaskId, $AttachmentType, $AttachmentName, $AttachmentPath, $UploadedBy, $UploadedDate, $AttachmentId]);  
    return true;
}

function deleteTaskAttachment($AttachmentId, $conn) {
    $sql = "DELETE FROM taskattachment WHERE AttachmentId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$AttachmentId]);

    if ($stmt->rowCount() > 0) {
        return ['success' => true, 'message' => "Attachment record deleted successfully"];
    } else {
        return ['success' => false, 'message' => "Failed to delete attachment record"];
    }
}
function deleteFile($filePath) {
    if (file_exists($filePath)) {
        if (unlink($filePath)) {
            return ['success' => true, 'message' => "File deleted successfully"];
        } else {
            return ['success' => false, 'message' => "Failed to delete file: $filePath"];
        }
    } else {
        return ['success' => false, 'message' => "File not found: $filePath"];
    }
}
function deleteFolder($folderPath) {
    if (!is_dir($folderPath)) {
        return ['success' => false, 'message' => "Folder does not exist: {$folderPath}"];
    }

    $files = array_diff(scandir($folderPath), ['.', '..']);

    foreach ($files as $file) {
        $filePath = $folderPath . DIRECTORY_SEPARATOR . $file;

        if (is_dir($filePath)) {
            // حذف المجلدات الفرعية
            $result = deleteFolder($filePath);
            if (!$result['success']) {
                return $result; // توقف عند أول خطأ
            }
        } else {
            // حذف الملف
            if (!unlink($filePath)) {
                return ['success' => false, 'message' => "Failed to delete file: {$filePath}"];
            }
        }
    }

    // حذف المجلد نفسه
    if (!rmdir($folderPath)) {
        return ['success' => false, 'message' => "Failed to delete folder: {$folderPath}"];
    }

    return ['success' => true, 'message' => "Folder deleted successfully: {$folderPath}"];
}
function deleteTaskAttachmentsByTaskId($TaskId, $conn) {
    try {
        $sql = "DELETE FROM taskattachment WHERE TaskId = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$TaskId]);
        return ['success' => true, 'message' => 'Attachments deleted from database'];
    } catch (Exception $e) {
        error_log("Error deleting task attachments for TaskId $TaskId: " . $e->getMessage());
        return ['success' => false, 'message' => 'Failed to delete attachments from database'];
    }
}

/*
function deleteTaskAttachment($AttachmentId, $conn) {
    $sql = "DELETE FROM taskattachment WHERE AttachmentId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$AttachmentId]);  
    return true;
}
*/

?>

