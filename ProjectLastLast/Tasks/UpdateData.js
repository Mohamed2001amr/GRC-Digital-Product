// دالة لجلب الرسالة من الـ API مع اللغة
async function fetchAndShowAlert(messageId, language) {
    try {
        const response = await fetch(`http://localhost/APIGateWay/APIConfig/Message.php?MessageId=${messageId}&lang=${language}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        const data = await response.json();
        if (data.MessageName) {
            return data.MessageName; // إرجاع الرسالة من البيانات
        } else {
            return 'Message not found'; // في حالة عدم العثور على الرسالة
        }
    } catch (error) {
        console.error('Error fetching message', error);
        return 'An error occurred while fetching the message'; // في حالة حدوث خطأ
    }
}
///////////////////////////////Display Message/////////////////////////////////////
// Function to display the footer message
function showFooterMessage(message) {
    const footerMessage = document.getElementById('footerMessage');
    footerMessage.textContent = message; // Set the message text
    footerMessage.style.display = 'block'; // Show the message

    // Hide the message after 30 seconds
    setTimeout(() => {
        footerMessage.style.display = 'none';
    }, 15000); // 30 seconds
}

///////////////////////////////////////////////////////////////////////////////////
function UpdateTask() {
    const Taskid = document.getElementById("saveChanges").getAttribute("data-taskid");

    // جمع البيانات والتحقق من صحتها
    const formData = collectTaskData();
    if (!formData) return; // وقف التنفيذ إذا فشل التحقق

    // إضافة TaskId إلى البيانات المُرسلة
    formData.TaskId = Taskid;

    // تحديث بيانات المهمة
    updateTaskData(Taskid, formData)
        .then( () => {
            // حفظ التعيينات الجديدة
            saveNewAssignments(Taskid)
                .then(() => console.log('Assignments saved successfully!'))
                .catch(err => console.error('Error saving assignments:', err));

            // حفظ المرفقات الجديدة
            saveNewActions(Taskid)
                .then(() => console.log('Actions saved successfully!'))
                .catch(err => console.error('Error saving Actions:', err));

            saveNewAttachments(Taskid)
                .then(() => console.log('Attachments saved successfully!'))
                .catch(err => console.error('Error saving attachments:', err));

            fetchAndShowAlert(3, 'EN').then(messageText => {
                showFooterMessage(messageText);
            });
            // Uncomment the next line if you want to reload or close modal
            location.reload(); 
            closeNewModal(); 
        })
        .catch( err => {
            fetchAndShowAlert(4, 'EN').then(messageText => {
                showFooterMessage(messageText);
            });
            location.reload(); 
            closeNewModal(); 
            
        });
}

function updateTaskData(Taskid, formData) {
    return fetch(`${APIGateWay}Task.php`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData),
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (!data.success) {
            throw new Error('Failed to update task: ' + (data.message || 'Unknown error occurred.'));
        }
        return data;
    });
}



// Function to collect task data and validate required fields
function collectTaskData() {
    const TaskName = document.querySelector('#taskName').value.trim();
    const TaskCategoryId = parseInt(document.querySelector('#TaskCategory').value);
    const TaskStage = parseInt(document.querySelector('#TaskStage').value);
    const TaskStatus = parseInt(document.querySelector('#TaskStatus').value);
    const PriorityId = getSelectedPriority();
    const DueDate = document.querySelector('#DueDate').value.trim();
    const TaskType = document.querySelector('#TaskType').value.trim();
    const TaskDescription = document.querySelector('#TaskDescription textarea')?.value.trim();
    const RefrenceId = document.getElementById('Refrence').value.trim();
    const TaskId = 0;

    // Validate required fields
    if (!TaskName) return showValidationError('Task Name is required');
    if (!TaskCategoryId) return showValidationError('Task Category is required');
    if (!TaskStage) return showValidationError('Task Stage is required');
    if (!TaskStatus) return showValidationError('Task Status is required');
    if (!DueDate) return showValidationError('Due Date is required');
    if (!PriorityId) return showValidationError('Priority is required');
    

    return {
        TaskName,
        TaskDescription,
        TaskType,
        TaskCategoryId,
        DueDate,
        PriorityId,
        TaskStage,
        TaskStatus,
        RefrenceId,
        TaskId,
    };
}

// Function to display validation errors
function showValidationError(message) {
    alert(message);
    return null;
}

// Function to get selected priority
function getSelectedPriority() {
    const stars = document.querySelectorAll('#priority .star.selected');
    const priority = stars.length;

    if (priority <= 2) return 1; // Low
    if (priority <= 4) return 2; // Medium
    return 3; // High
}
function saveNewAttachments(taskId) {
    const newAttachments = [];
    const previews = document.querySelectorAll(".uploadedImage[data-new='true']");

    previews.forEach(img => {
        const uniqueId = img.id;
        const type = document.getElementById(`insertTypeAttachment-${uniqueId}`).value;
        const description = document.getElementById(`description-${uniqueId}`).value;
        const currentDate = new Date().toISOString().split('T')[0];

        newAttachments.push({
            base64: img.src, // الصورة المشفرة بـ Base64
            type,
            description,
            UploadedBy: 1,
            UploadedDate: currentDate,
        });
    });

    if (newAttachments.length > 0) {
        // إرجاع الوعد
        return fetch(`${APIGateWay}TaskAttachment.php`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ TaskId: taskId, attachments: newAttachments }),
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                // إزالة علامة data-new من الصور المحفوظة
                previews.forEach(img => img.removeAttribute("data-new"));

                // إذا كانت هناك مرفقات محذوفة، قم بحذفها
                if (deletedAttachments.length > 0) {
                    return fetch(`${APIGateWay}TaskAttachment.php`, {
                        method: "DELETE",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify({ TaskId: taskId, attachments: deletedAttachments }),
                    })
                    .then(response => response.json())
                    .then(results => {
                        if (results.success) {
                            fetchAndShowAlert(29, 'EN').then(messageText => {
                                showFooterMessage(messageText);
                            });
                            deletedAttachments = []; // إعادة تعيين قائمة المحذوفات
                        } else {
                            fetchAndShowAlert(22, 'EN').then(messageText => {
                                showFooterMessage(messageText);
                            });
                            return Promise.reject("Failed to delete attachments.");
                        }
                    })
                    .catch(error => {
                        console.error("Error in deletion:", error);
                        throw error;
                    });
                } else {
                    fetchAndShowAlert(23, 'EN').then(messageText => {
                        showFooterMessage(messageText);
                    });
                }
            } else {
                fetchAndShowAlert(24, 'EN').then(messageText => {
                    showFooterMessage(messageText);
                });
                return Promise.reject("Failed to save attachments.");
            }
        })
        .catch(error => {
            console.error("Error in saving:", error);
            throw error;
        });
    } else {
        // إذا لم تكن هناك مرفقات جديدة ولكن هناك محذوفات فقط
        if (deletedAttachments.length > 0) {
            return fetch(`${APIGateWay}TaskAttachment.php`, {
                method: "DELETE",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ TaskId: taskId, attachments: deletedAttachments }),
            })
            .then(response => response.json())
            .then(results => {
                if (results.success) {
                    fetchAndShowAlert(25, 'EN').then(messageText => {
                        showFooterMessage(messageText);
                    });
                    deletedAttachments = []; // إعادة تعيين قائمة المحذوفات
                } else {
                    fetchAndShowAlert(22, 'EN').then(messageText => {
                        showFooterMessage(messageText);
                    });
                    return Promise.reject("Failed to delete attachments.");
                }
            })
            .catch(error => {
                console.error("Error in deletion:", error);
                throw error;
            });
        } else {
            fetchAndShowAlert(26, 'EN').then(messageText => {
                showFooterMessage(messageText);
            });
            return Promise.resolve(); // وعد فوري
        }
    }
}

function createPreviewContainerr(file, uniqueId) {
    const previewContainer = document.createElement("div");
    previewContainer.classList.add("attachment-preview");

    const imgContainer = document.createElement("div");
    imgContainer.classList.add("imgContainer");

    const img = document.createElement("img");
    img.classList.add("uploadedImage");
    img.id = uniqueId;
    img.dataset.new = "true"; // علامة أن الصورة جديدة

    const reader = new FileReader();
    reader.onload = (e) => {
        img.src = e.target.result; // الصورة المشفرة بـ Base64
        img.onclick = () => openImageModal(e.target.result);
    };
    reader.readAsDataURL(file);

    imgContainer.appendChild(img);
    previewContainer.appendChild(imgContainer);

    const table = createDataTablee(uniqueId);
    previewContainer.appendChild(table);

    return previewContainer;
}
function createDataTablee(uniqueId) {
    const table = document.createElement("table");
    table.classList.add("dataTable");

    table.innerHTML = `
        <tr>
            <td class="smallSize"> <span style="color: red;">*</span> Type:</td>
            <td class="largeSize">
                <select name="attachmentTypee" id ="insertTypeAttachment" class="grayColor taskAttachmentType">
                    
                </select>
            </td>
            <td class="mediumSize">Uploaded Date:</td>
            <td class="mixsize mixsizee">${new Date().toLocaleDateString()}</td>
            <td class="mediumSize" >Uploaded By:</td>
            <td><input type="text" name="uploadedByy" value="Uploaded by" class="taskin" readonly></td>
        </tr>
        <tr>
            <td> <span style="color: red;">*</span> Description:</td>
            <td class="textwrite" colspan="10"><textarea placeholder="Write Description"></textarea></td>
                <button type="button" class="attacadd add" onclick="addNewFileRow(this)">
                    <img src="../Images/new.svg" alt="Add New">
                </button> 
                <button type="button" class="attacadd add" data-id="${uniqueId}" onclick="changeImage(this)">
                    <img src="../Images/upload.svg" alt="Upload Image">
                </button>
                <button type="button" class="attacadd delete" onclick="deleteImagenotsaved(this)">
                    <img src="../Images/false.svg" alt="Delete">
                </button>
        </tr>
    `;
    fetchAttachmentType();
    return table;
}

function addNewFileRow(button) {    
    // التحقق من جميع الصفوف، بما في ذلك السطر الافتراضي
    const allRows = document.querySelectorAll(".attachment-preview");

    // تحقق من الحقول المطلوبة في كل صف
    for (const row of allRows) {
        if (!validateFields(row)) {
                fetchAndShowAlert(12, 'EN').then(messageText => {
                    showAlert(messageText);
                });
                return; // توقف إذا لم يتم ملء أحد الصفوف
        }
    }
    // إذا تم ملء جميع الحقول، افتح نافذة اختيار الملفات
    document.getElementById("fileUploadd").click();
}

function validateFields(previewContainer) {
    const inputs = previewContainer.querySelectorAll("input, select, textarea");
    let isValid = true; // افتراض أن جميع الحقول صالحة

    for (const input of inputs) {
        // تحقق إذا كان الحقل فارغًا
        if (input.type !== "hidden" && input.value.trim() === "") {
            isValid = false;
            input.classList.add("error"); // أضف علامة خطأ (مثل حدود حمراء)
        } else {
            input.classList.remove("error"); // أزل علامة الخطأ إذا تم ملء الحقل
        }
    }

    return isValid; // أرجع true إذا كانت جميع الحقول ممتلئة
}

function saveNewAssignments(taskId) {
    const newAssignments = [];
    const assignmentRows = document.querySelectorAll(".checklist-row[data-new='true']");

    assignmentRows.forEach(row => {
        const assignedTo = row.querySelector("input[name='AssignedTo']").value;
        const assignmentReason = row.querySelector("input[name='AssignmentReason']").value;
        const assignedBy = row.querySelector("input[name='AssignedBy']").value;
        const currentDate = new Date().toISOString().split("T")[0];

        newAssignments.push({
            AssignedTo: assignedTo,
            AssignmentReason: assignmentReason,
            AssignedBy: 1,
            AssignmentDate: currentDate,
        });
    });

    // إذا لم تكن هناك تعيينات جديدة ولم يتم حذف أي شيء
    if (newAssignments.length === 0 && (!deletedAssignments || deletedAssignments.length === 0)) {
        return Promise.resolve(); // إرجاع Promise فوري فارغ
    }

    // إذا كانت هناك تعيينات جديدة
    return fetch(`${APIGateWay}TaskAssignments.php`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ TaskId: taskId, assignments: newAssignments }),
    })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                assignmentRows.forEach(row => row.removeAttribute("data-new")); // إزالة العلامة الجديدة

                // إذا كان هناك تعيينات محذوفة
                if (deletedAssignments && deletedAssignments.length > 0) {
                    return deleteAssignmentsFromDB(taskId); // حذف التعيينات المحذوفة
                } else {
                    fetchAndShowAlert(27, 'EN').then(messageText => {
                        showFooterMessage(messageText);
                    });
                }
            } else {
                fetchAndShowAlert(28, 'EN').then(messageText => {
                    showFooterMessage(messageText);
                });
                return Promise.reject("Failed to save assignments."); // رمي خطأ عند الفشل
            }
        })
        .catch(error => {
            console.error("Error in saveNewAssignments:", error);
            throw error; // إعادة رفع الخطأ ليتم التعامل معه في `catch` خارجي
        });
}



function deleteAssignmentsFromDB(taskId) {
    if (!deletedAssignments.length) {
        fetchAndShowAlert(30, 'EN').then(messageText => {
            showFooterMessage(messageText);
        });
        return;
    }

    fetch(`${APIGateWay}TaskAssignments.php`, {
        method: "DELETE",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ TaskId: taskId, assignments: deletedAssignments }),
    })
        .then(response => response.json())
        .then(results => {
            if (results.success) {
                fetchAndShowAlert(31, 'EN').then(messageText => {
                    showFooterMessage(messageText);
                });
                deletedAssignments = [];
            } else {
                fetchAndShowAlert(32, 'EN').then(messageText => {
                    showFooterMessage(messageText);
                });
            }
        })
        .catch(error => console.error("Error in deletion:", error));
}

function saveNewActions(taskId) {
    const newActions = [];
    const modifiedActions = [];

    // جمع العمليات الجديدة
    const actionRows = document.querySelectorAll(".ActionRow1[data-new='true']");
    actionRows.forEach(row => {
        const actionName = row.querySelector("input[name='insertActionName']").value.trim();
        const actionStatus = row.querySelector("select[name='insertActionStatus']").value.trim();
        const currentDate = new Date().toISOString().split("T")[0];

        newActions.push({
            ActionDetails: actionName,
            ActionStatus: actionStatus,
            ActionStatusDate: currentDate,
        });
    });

    // جمع العمليات المعدلة
    const modifiedActionRows = document.querySelectorAll(".ActionRow[data-modified='true']");
    modifiedActionRows.forEach(row => {
        const actionId = row.getAttribute("data-action-id") ?? null;
        const actionName = row.querySelector("input.inserteditable-text").value.trim();
        const actionStatus = row.querySelector("select.insertActionStatus").value.trim();
        const currentDate = new Date().toISOString().split("T")[0];

        modifiedActions.push({
            ActionId: actionId,
            ActionName: actionName,
            ActionStatus: actionStatus,
            ActionDate: currentDate,
        });
    });

    // تحقق من وجود بيانات جديدة أو معدلة
    if (newActions.length === 0 && modifiedActions.length === 0 && (!deletedActions || deletedActions.length === 0)) {
        return Promise.resolve(); // إذا لم يكن هناك أي بيانات للإرسال، أرجع وعدًا فارغًا
    }

    const promises = [];

    // إرسال العمليات الجديدة (POST)
    if (newActions.length > 0) {
        promises.push(
            fetch(`${APIGateWay}TaskActions.php`, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ TaskId: taskId, actions: newActions }),
            })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        actionRows.forEach(row => row.removeAttribute("data-new"));
                        alert("New actions saved successfully!");
                    } else {
                        alert("Failed to save new actions.");
                        return Promise.reject("Failed to save new actions.");
                    }
                })
        );
    }

    // إرسال العمليات المعدلة (PUT)
    if (modifiedActions.length > 0) {
        promises.push(
            fetch(`${APIGateWay}TaskActions.php`, {
                method: "PUT",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ TaskId: taskId, actions: modifiedActions }),
            })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        modifiedActionRows.forEach(row => row.removeAttribute("data-modified"));
                        alert("Modified actions saved successfully!");
                    } else {
                        alert("Failed to save modified actions.");
                        return Promise.reject("Failed to save modified actions.");
                    }
                })
        );
    }

    // حذف العمليات المحذوفة (DELETE)
    if (deletedActions && deletedActions.length > 0) {
        promises.push(deleteActionsFromDB(taskId));
    }

    // تنفيذ جميع العمليات معًا
    return Promise.all(promises)
        .then(() => {
            alert("All actions processed successfully!");
        })
        .catch(error => {
            console.error("Error processing actions:", error);
            throw error;
        });
}



function deleteActionsFromDB(taskId) {
    if (!deletedActions.length) {
        alert("No action to delete.");
        return;
    }

    fetch(`${APIGateWay}TaskActions.php`, {
        method: "DELETE",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ TaskId: taskId, actions: deletedActions }),
    })
        .then(response => response.json())
        .then(results => {
            if (results.success) {
                alert("Deleted action successfully!");
                deletedAssignments = [];
            } else {
                alert("Failed to delete some or all action.");
            }
        })
        .catch(error => console.error("Error in deletion:", error));
}
