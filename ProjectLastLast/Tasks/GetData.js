APIGateWay = 'http://localhost/APIGateway/APITask/'; 
APIConfig = 'http://localhost/APIGateway/APIConfig/';
language='EN';
document.addEventListener("DOMContentLoaded", function () {
    // Fetch data from the API on page load
    fetchTasks();
    fetchStages();
    fetchCategories();
    fetchStatus();
    fetchType();
});
//////////////////////////////////////////////////////TASKS////////////////////////////////////////////////////////////////
function fetchTasks() {
    fetch(APIGateWay+`Task.php?lang=${language}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
        }) 
        .then(response => response.json())
        .then(data => {
            populateTaskTable(data);
        })
        .catch(error => console.error('Error fetching Tasks', error));
}

function populateTaskTable(tasks) {
    const taskTableBody = document.getElementById("TaskTableBody");
    
    if (!taskTableBody) {
        console.error("Element with id 'TaskTableBody' not found!");
        return;
    }
    
    taskTableBody.innerHTML = ""; // Clear existing rows
    // تحقق من أن البيانات مصفوفة ولها عناصر
    if (!Array.isArray(tasks) || tasks.length === 0) {
        taskTableBody.textContent = 'No Task Found';
        return;
    }
    
    // قم بإنشاء الصفوف من البيانات
    tasks.forEach(task => {
        const row = document.createElement("tr");
        row.innerHTML = `
            <td class="select-task">
                <input type="checkbox" name="selectPerson" onchange="updateRowBackground(this); updateDeleteButton();" />
            </td>
            <td onclick='openNewModal(${task.TaskId})' class="id-task">${task.TaskId}</td>
            <td class="task-identify">
                <span onclick='openNewModal(${task.TaskId})'>${task.TaskName}</span>
            </td>
            <td onclick='openNewModal(${task.TaskId})' class="statue-task">${task.StatusName}</td>
            <td onclick='openNewModal(${task.TaskId})' class="date-task">${task.DueDate || "N/A"}</td>
            <td class="type-task">
                <div class="text"> ${task.TypeName} </div>
                <div class="table-icon">
                    <img src="../Images/new.svg" alt="New" onclick='openModal()'>
                    <img src="../Images/archive.svg" alt="Archive">
                    <img src="../Images/false.svg" alt="Delete" onclick="confirmDelete(${task.TaskId},this)">
                </div>
            </td>
        `;
        taskTableBody.appendChild(row);
    });
}
////////////////////////////////////////////////INSERT TASK/////////////////////////////////////////////////////////
// Collect form data
let selectedPriority = 0; // متغير لتخزين عدد النجوم المختارة

    document.getElementById("insertpriority").addEventListener("click", function (event) {
        if (event.target.classList.contains("star")) {
            const stars = document.querySelectorAll("#insertpriority .star");
            const selectedValue = parseInt(event.target.getAttribute("data-value"), 10); // عدد النجوم المختارة
    
            // تحديث مظهر النجوم بناءً على الاختيار
            stars.forEach((star, index) => {
                if (index < selectedValue) {
                    star.classList.add("selected");
                } else {
                    star.classList.remove("selected");
                }
            });
    
            // تخزين عدد النجوم المختارة
            selectedPriority = selectedValue;
            // هنا يمكنك استدعاء وظيفة لتسجيل القيمة في قاعدة البيانات
        }
    });
    
function collectFormData() {
    const TaskName = document.querySelector('#InsertTaskName').value.trim();
    const TaskCategoryId = parseInt(document.querySelector('#InsertTaskCategory').value);
    const TaskStage = parseInt(document.querySelector('#InsertTaskStage').value);
    const TaskStatus = parseInt(document.querySelector('#InsertTaskStatus').value);
    const PriorityId = selectedPriority;
    const DueDate = document.querySelector('#InsertDueDate').value;
    const TaskType = document.querySelector('#InsertTaskType').value;
    const TaskDescription = document.querySelector('#InsertTaskDescription textarea').value.trim();
    const RefrenceId = document.getElementById('InsertRefrence').value || null;

    if (!TaskName || isNaN(TaskCategoryId) || isNaN(TaskStage) || isNaN(TaskStatus) || isNaN(PriorityId)) {
        fetchAndShowAlert(19, 'EN').then(messageText => {
            showAlert(messageText);
        });
        return null;
    }

    const currentDate = new Date().toISOString().split('T')[0];

    return {
        TaskName,
        TaskDescription,
        CreatedBy: 1,
        CreationDate: currentDate,
        AssignedBy: 1,
        AssignmentDate: currentDate,
        TaskType,
        TaskCategoryId,
        DueDate,
        PriorityId,
        TaskStage,
        TaskStatus,
        RefrenceId,
        StatusDate: currentDate,
    };
}

// Collect attachment data
function collectAttachments(currentDate) {
    const attachments = [];
    const attachmentContainers = document.querySelectorAll('.attachment-preview');
    attachmentContainers.forEach(container => {
        const img = container.querySelector('img.uploadedImage');
        const description = container.querySelector('textarea').value.trim();
        const typeInput = container.querySelector('#insertTypeAttachment'); 
        const type = typeInput ? parseInt(typeInput.value, 10) : null;

        if (img && img.src) {
            attachments.push({
                base64: img.src,
                type: type,
                description: description,
                UploadedBy: 1,
                UploadedDate: currentDate,
            });
        }
    });
    return attachments;
}

function collectAssignments() {
    // Select the active tab (content-3)
    const tabContent = document.querySelector("#content-3");

    if (!tabContent) {
        console.error("Content tab 3 not found!");
        return [];
    }

    // Select all rows with the class "assignRow1" within content-3
    const rows = tabContent.querySelectorAll(".assignRow1");

    const assignments = [];

    rows.forEach(row => {
        const assignmentUserInput = row.querySelector('.assignmentuser input');
        const assignmentReasonInput = row.querySelector('.assignmentReason input');

        // Extract values from inputs, if they exist
        const assignmentUser = assignmentUserInput ? assignmentUserInput.value : '';
        const assignmentReason = assignmentReasonInput ? assignmentReasonInput.value : '';

        // Only include rows where mandatory fields are filled
        if (assignmentUser && assignmentReason) {
            assignments.push({
                AssignedTo: assignmentUser,
                AssignmentReason: assignmentReason,
                AssignmentDate: new Date().toISOString().split('T')[0], // Default assignment date
                AssignedBy: 1, // Replace with dynamic value if necessary

            });
        }
    });

    return assignments;
}
function collectActions() {
    // تحديد التبويب الفعال (content-1)
    const tabContent = document.querySelector("#content-1");

    if (!tabContent) {
        console.error("Content tab 1 not found!");
        return [];
    }

    // تحديد جميع الصفوف ذات الفئة "ActionRow1" داخل content-1
    const rows = tabContent.querySelectorAll(".ActionRow1");

    const actions = [];

    rows.forEach(row => {
        const actionNameInput = row.querySelector('.actionText .editable-text');
        const actionStatusInput = row.querySelector('.actionStatusContainer select[name="insertActionStatus"]');

        // استخراج القيم من الحقول، إذا كانت موجودة
        const actionName = actionNameInput ? actionNameInput.value.trim() : '';
        const actionStatus = actionStatusInput ? actionStatusInput.value : '';

        // تضمين الصفوف التي تحتوي على البيانات المطلوبة فقط
        if (actionName && actionStatus) {
            actions.push({
                ActionDetails: actionName,
                ActionStatus: actionStatus,
                ActionStatusDate: new Date().toISOString().split('T')[0],
                ActionBy: 1, // Default assignment date
            });
        }
    });

    return actions;
}

// Send API request
function sendApiRequest(endpoint, method, bodyData) {
    return fetch(APIGateWay + endpoint, {
        method,
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(bodyData),
    }).then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    });
}

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

// Main function to add a task
function addTask() {
    const bodyData = collectFormData();
    if (!bodyData) return;

    const currentDate = bodyData.CreationDate;
    const attachments = collectAttachments(currentDate);
    const actions = collectActions();
    const assignments = collectAssignments(currentDate);
    bodyData.attachments = attachments;
    bodyData.actions = actions;
    bodyData.assignments = assignments;

     sendApiRequest(`Task.php`, 'POST', bodyData)
        .then(data => {
            if (data.success) {
                const taskId = data.TaskId;
                if (!taskId) {
                    fetchAndShowAlert(20, 'EN').then(messageText => {
                        showAlert(messageText);
                    });
                    return;
                }
                // Call CreateFile API for attachments
                return sendApiRequest(`TaskAttachment.php`, 'POST', {
                    TaskId: taskId,
                    attachments: attachments,
                })
                .then(fileData => {
                    if (fileData.success) {
                        // Call TaskActions API
                        return sendApiRequest(`TaskActions.php`, 'POST', {
                            TaskId: taskId,
                            actions: actions,
                        })
                        .then(actionData => {
                            if (actionData.success) {
                                // After creating the task and actions, send the task assignments
                                return sendApiRequest(`TaskAssignments.php`, 'POST', {
                                    TaskId: taskId,
                                    assignments: assignments,
                                })
                                .then(assignmentData => {
                                    if (assignmentData.success) {
                                        fetchAndShowAlert(1, 'EN').then(messageText => {
                                            showFooterMessage(messageText);
                                        });
                                    } else {
                                        console.error('Failed to create task assignments:', assignmentData.message || 'Unknown error occurred.');
                                    }
                                });
                            } else {
                                console.error('Failed to create task actions:', actionData.message || 'Unknown error occurred.');
                            }
                        });
                    } else {
                        console.error('Failed to create task file:', fileData.message || 'Unknown error occurred.');
                    }
                });
            } else {
                fetchAndShowAlert(2, 'EN').then(messageText => {
                    showFooterMessage(messageText);
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            fetchAndShowAlert(21, 'EN').then(messageText => {
                showAlert(messageText);
            });
        });
} 

////////////////////////////////////////////////////////Stage//////////////////////////////////////////////////////////
function fetchStages() {
    fetch(APIGateWay + `TaskStage.php?lang=${language}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        populateStageDropdown(data);  // ملء القائمة بالبيانات المسترجعة
    })
    .catch(error => {
        console.error('Error fetching task stage:', error);
    });
}
function populateStageDropdown(Stages) {
    const stageSelects = document.querySelectorAll('.TaskStage'); // استخدام querySelectorAll لتحديد كل العناصر
    // التأكد من وجود عناصر select
    if (stageSelects.length === 0) {
        console.error('No select elements with class "TaskStage" found');
        return;
    }

    // مسح الخيارات السابقة من القائمة الجانبية

    // إضافة خيار "حدد المرحلة" الافتراضي إلى جميع القوائم المنسدلة
    stageSelects.forEach(stageSelect => {
        stageSelect.innerHTML = ''; // مسح الخيارات القديمة

     // إضافة الخيار الافتراضي
    });

    // إضافة الخيارات الجديدة المسترجعة من الـ API
    Stages.forEach(Stage => {
        if (Stage.StageId && Stage.StageName) {
            stageSelects.forEach(stageSelect => {
                const option = document.createElement('option');
                option.value = Stage.StageId;
                option.textContent = Stage.StageName;
                stageSelect.appendChild(option); // إضافة الخيار إلى كل قائمة منسدلة
            });

           
        } else {
            console.warn('Invalid Stage object:', Stage);
        }
    });
}
////////////////////////////////////////////////////////Category//////////////////////////////////////////////////////////
function fetchCategories() {
    fetch(APIGateWay+`TaskCategory.php?lang=${language}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        populateCategoryDropdown(data);
    })
    .catch(error => console.error('Error fetching task Category:', error));
}

// Populate the Category dropdown dynamically
function populateCategoryDropdown(Categories) {
    const CategorySelects = document.querySelectorAll('.taskCategory');
    if (CategorySelects.length === 0) {
        console.error('No select elements with class "TaskStage" found');
        return;
    }
    CategorySelects.forEach(CategorySelect => {
        CategorySelect.innerHTML = ''; // مسح الخيارات القديمة
    
       // إضافة الخيار الافتراضي
    });
    // Loop through the Category and add them as options
    Categories.forEach(Category => {
        // Ensure Category object has the necessary properties
        if (Category.CategoryId && Category.CategoryName) {
            CategorySelects.forEach(CategorySelect => {
                const option = document.createElement('option');
                option.value = Category.CategoryId;  // Assuming CategoryID is the value to be sent
                option.textContent = Category.CategoryName;  // Display the name of the Category
                CategorySelect.appendChild(option);
            });
        } else {
            console.warn('Invalid Cayegory object:', Category);
        }
    });
}
////////////////////////////////////////////////////////Status//////////////////////////////////////////////////////////

function fetchStatus(){
    fetch(APIGateWay+`TaskStatus.php?lang=${language}`,{
        method :'GET' ,
        headers : {
            'Content-Type': 'application/json'
        }
        })
    .then (response => response.json())
    .then (data =>
        {
            populateStatusDropdown(data);
        }
    )
}

function populateStatusDropdown(Statuses) {
    const StatusSelects = document.querySelectorAll('.taskStatus'); 
    const StatusList = document.getElementById("StatusFilter"); 
    
    if (StatusSelects.length === 0) {
        console.error('No select elements with class "TaskStage" found');
        return;
    }

    // مسح الخيارات السابقة من القائمة الجانبية
    StatusList.innerHTML = '';
    // إضافة خيار "حدد المرحلة" الافتراضي إلى جميع القوائم المنسدلة
    StatusSelects.forEach(StatusSelect => {
        StatusSelect.innerHTML = ''; // مسح الخيارات القديمة

         // إضافة الخيار الافتراضي
    });
    // إضافة الـ options للقائمة المنسدلة
    Statuses.forEach(status => {
        if (status.StatusId && status.StatusName) {
            StatusSelects.forEach(StatusSelect => {
            // إضافة Option للقائمة المنسدلة
                const option = document.createElement('option');
                option.value = status.StatusId;
                option.textContent = status.StatusName;
                StatusSelect.appendChild(option);
            });

            // إضافة Checkbox للقائمة الجانبية (nested list)
            const li = document.createElement("li");

            const checkbox = document.createElement("input");
            checkbox.type = "checkbox";
            checkbox.name = "selectStatus";
            checkbox.value = status.StatusId; // قيمة الـ checkbox هي الـ StatusId
            li.appendChild(checkbox);

            const span = document.createElement("span");
            span.textContent = status.StatusName; // النص الذي سيتم عرضه بجانب الـ checkbox
            li.appendChild(span);

            StatusList.appendChild(li);
        } else {
            console.warn('Invalid status object:', status);
        }
    });
}


////////////////////////////////////////////////USER/////////////////////////////////////////////////////////////////////////
/*
document.addEventListener("DOMContentLoaded", function () {
    fetchUsers();
});

function fetchUsers() {
    fetch(APIConfig + `User.php`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        },
    })
        .then((response) => response.json())
        .then((data) => {
            populateUserDropdown(data);
        })
        .catch((error) => console.error('Error fetching users:', error));
}

function populateUserDropdown(users) {
    const dropdownList = document.getElementById("dropdownList");
    dropdownList.innerHTML = ""; // تنظيف القائمة الحالية

    // إنشاء العناصر الجديدة بناءً على البيانات
    users.forEach((user) => {
        if (user.UserID && user.UserName) {
            const label = document.createElement("label");
            const radio = document.createElement("input");

            radio.type = "radio";
            radio.name = "selesctUser";
            radio.value = user.UserID; // قيمة الـ Radio هي UserID

            label.appendChild(radio);
            label.appendChild(document.createTextNode(user.UserName)); // عرض اسم المستخدم
            dropdownList.appendChild(label);
        } else {
            console.warn("Invalid user object:", user);
        }
    });

    // إضافة زر الحفظ
    const saveButton = document.createElement("button");
    saveButton.className = "save-button";
    saveButton.textContent = "Save";
    saveButton.onclick = saveSelection; /// مفيش حاجه بيعمهلها 
    dropdownList.appendChild(saveButton);
}

function saveSelection() {
    const selectedUser = document.querySelector('input[name="selesctUser"]:checked');
    if (selectedUser) {
        console.log("Selected User ID:", selectedUser.value);
        // يمكنك إرسال القيمة إلى الخادم أو التعامل معها حسب الحاجة
    } else {
        alert("Please select a user!");
    }
}
*/
////////////////////////////////////////////////////////Type//////////////////////////////////////////////////////////
function fetchType(){
    fetch(APIGateWay+`TaskType.php?lang=${language}`,{
        method :'GET' ,
        headers : {
            'Content-Type': 'application/json'
        }
        })
    .then (response => response.json())
    .then (data =>
        {
            populateTypeDropdown(data);
        }
    )
}
function populateTypeDropdown(Types)
{
    const TypeSelects = document.querySelectorAll('.taskType')
    const TypeList = document.getElementById('TypeFilter')
    if (TypeSelects.length === 0) {
        console.error('No select elements with class "TaskType" found');
        return;
    }

    TypeList.innerHTML='';

    TypeSelects.forEach(TypeSelect => {
        TypeSelect.innerHTML = ''; // مسح الخيارات القديمة
    
         // إضافة الخيار الافتراضي
    });

    Types.forEach(Type=>{
        if (Type.TypeId && Type.TypeName) {
            TypeSelects.forEach(TypeSelect => {
            const option = document.createElement('option');
            option.value = Type.TypeId;  // Assuming CategoryID is the value to be sent
            option.textContent = Type.TypeName;  // Display the name of the Category
            TypeSelect.appendChild(option);
            });

            const li = document.createElement("li");

            const checkbox = document.createElement("input");
            checkbox.type = "checkbox";
            checkbox.name = "selectType";
            checkbox.value = Type.TypeId; // قيمة الـ checkbox هي الـ StatusId
            li.appendChild(checkbox);
            const span = document.createElement("span");
            span.textContent = Type.TypeName; // النص الذي سيتم عرضه بجانب الـ checkbox
            li.appendChild(span);
            TypeList.appendChild(li);
        } else {
            console.warn('Invalid Type object:', Type);
        }
    });
}

///////////////////////////////////////////////////////AttachmentType//////////////////////////////////////
function fetchAttachmentType(){
    fetch(APIGateWay+`TaskAttachmentType.php?lang=${language}`,{
        method :'GET' ,
        headers : {
            'Content-Type': 'application/json'
        }
        })
    .then (response => response.json())
    .then (data =>
        {
            populateAttachmentTypeDropdown(data);
        }
    )
}

function populateAttachmentTypeDropdown(Types) {
    const TypeLists = document.querySelectorAll('.taskAttachmentType')


    if (TypeLists.length === 0) {
        console.error('No select elements with class "TypeAttachment" found');
        return;
    }
    TypeLists.forEach(TypeList => {
        TypeList.innerHTML = ''; // مسح الخيارات القديمة
    
       // إضافة الخيار الافتراضي
    });
    Types.forEach(Type => {
        if (Type.TypeId && Type.TypeName) {
            TypeLists.forEach(TypeList => {
                const option = document.createElement('option');
                option.value = Type.TypeId;  
                option.textContent = Type.TypeName;  
                TypeList.appendChild(option);
            });
        } else {
            console.warn('Invalid Type object:', Type);
        }
    });
}
////////////////////////////////////////ActionStatus//////////////////////////////////////////////////////////////
let actionStatuses = [];

// جلب الحالات من API وتخزينها
function fetchActionStatuses() {
    fetch(`${APIGateWay}TaskActionStatus.php?lang=${language}`, {
        method: 'GET',
        headers: { 'Content-Type': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        actionStatuses = data;
        populateActionStatusDropdown(); // تحديث جميع القوائم الحالية
    })
    .catch(error => {
        console.error("Error fetching action statuses:", error);
    });
}

// ملء قائمة الاختيارات بالحالات
function populateActionStatusDropdown(specificSelectElement = null) {
    const statusSelectElements = specificSelectElement
        ? [specificSelectElement]
        : document.querySelectorAll('.insertActionStatus');

    statusSelectElements.forEach(selectElement => {
        selectElement.innerHTML = ''; // تفريغ الخيارات الحالية
        actionStatuses.forEach(status => {
            const option = document.createElement('option');
            option.value = status.StatusId;
            option.textContent = status.StatusName;
            selectElement.appendChild(option);
        });
    });
}
//////////////////////////////////////////////////////////////////////send to tasks.js/////////////////////////////////////////////////////
document.addEventListener("DOMContentLoaded", function () {
    // استدعاء الوظيفة باستخدام ID أو class
    setTodayDate('#CreationDate');
    setTodayDate('#StatusDate'); // باستخدام ID
});

// تعريف الوظيفة لدعم المعرف أو الفئة
function setTodayDate(selector) {
    // البحث عن العناصر باستخدام selector (ID أو class)
    const dateInputs = document.querySelectorAll(selector);
    
    // التكرار على كل عنصر تم العثور عليه
    dateInputs.forEach(dateInput => {
        // التحقق من وجود العنصر
        if (dateInput) {
            // الحصول على تاريخ اليوم بتنسيق YYYY-MM-DD
            const today = new Date().toISOString().split('T')[0];
            
            // تعيين قيمة الحقل إلى تاريخ اليوم
            dateInput.value = today;
        }
    });
}
///////////////////////////////////////////////////
function openNewModal(taskId) {
    const ThisTaskId = taskId;
    document.getElementById("newModal").style.display = "flex";

    // Fetch Task Details
    fetch(APIGateWay + `Task.php?lang=${language}&taskId=${taskId}`, {
        method: "GET",
        headers: { "Content-Type": "application/json" },
    })
    .then(response => response.json())
    .then(TaskDetails => {

        // Populate task details
        document.getElementById("taskName").value = TaskDetails.TaskName || "No Name Provided";
        document.getElementById("TaskDescription").value = TaskDetails.TaskDescription || "No Description";
        SetTaskStage(TaskDetails.StageName);
        SetTaskCategory(TaskDetails.CategoryName);
        SetTaskStatus(TaskDetails.StatusName);
        SetTaskType(TaskDetails.TypeName);
        document.getElementById("Refrence").value = TaskDetails.RefrenceId || "N/A";
        document.getElementById("statusDate").value = TaskDetails.StatusDate || "N/A";
        document.getElementById("DueDate").value = TaskDetails.DueDate || "N/A";
        document.getElementById("CreatedBy").value = TaskDetails.CreatedBy || "Unknown";
        document.getElementById("CreationDate").value = TaskDetails.CreationDate || "N/A";
        document.getElementById("AssignedBy").value = TaskDetails.AssignedBy || "Unknown";
        document.getElementById("AssignmentDate").value = TaskDetails.AssignmentDate || "N/A";
        document.getElementById("GetTaskId").value = ThisTaskId;
        renderPriorityStars(TaskDetails.PriorityId);
        document.getElementById("deleteTask").setAttribute("data-taskid", taskId);

        document.getElementById("saveChanges").setAttribute("data-taskid", taskId);

        // Fetch Task Attachments
        fetchAttachments(ThisTaskId);
        fetchAssignments(ThisTaskId);
        fetchActions(ThisTaskId)
    })
    .catch(error => {
        console.error("Error fetching Task Details", error);
        fetchAndShowAlert(7, 'EN').then(messageText => {
            showAlert(messageText);
        });
    });
}
function fetchActions(taskId) {
    const checklistContainer = document.getElementById("getchecklistItemaction");
    if (!checklistContainer) return;
    checklistContainer.innerHTML = ""; // تفريغ المحتوى الحالي

    fetch(`${APIGateWay}TaskActions.php?taskId=${taskId}`, {
        method: "GET",
        headers: { "Content-Type": "application/json" },
    })
    
    .then(response => response.json())
    .then(actions => {
        if (actions && actions.length > 0) {
            actions.forEach((action, index) => {
                const actionRow = document.createElement('table');
                actionRow.classList.add('actiontable', 'ActionDeafult');

                actionRow.innerHTML = `
                    <tbody>
                        <tr data-action-id="${action.ActionId}" class="ActionRow">
                            <td class="actionCheckHeader">
                                <input type="text" name="ActionId" value="${index + 1}" class="taskin" readonly />
                            </td>
                            <td class="actionText">
                                <input type="text" class="inserteditable-text" value="${action.ActionDetails}" onfocus="this.select()" />
                            </td>
                            <td class="actionStatusContainer">
                                <select id="ActionStatus-${index + 1}" class="insertActionStatus" onchange="markActionAsModified(this, ${action.ActionId} )"></select>
                            </td>
                            <td class="actionDateContainer">
                                ${new Date(action.ActionStatusDate).toLocaleDateString()}
                                <div class="table-icon">
                                    <img src="../Images/new.svg" alt="New" onclick="addContent()">
                                    <img src="../Images/archive.svg" alt="archive">
                                    <img src="../Images/false.svg" alt="Delete" onclick="deleteActionsaved(this, ${action.ActionId})">
                                </div>
                            </td>
                        </tr>
                    </tbody>
                `;
                checklistContainer.appendChild(actionRow);

                // تحديث الحالة للأكشن
                const selectElement = actionRow.querySelector(`#ActionStatus-${index + 1}`);
                populateActionStatusDropdownn(selectElement).then(() => {
                    selectElement.value = action.StatusName;
                });
            });
        } else {
            checklistContainer.innerHTML = `<p>No actions found.</p>`;
        }
    })
    .catch(error => {
        console.error("Error fetching actions:", error);
        checklistContainer.innerHTML = `<p>Error loading actions. Please try again.</p>`;
    });
}
function markActionAsModified(element) {
    const row = element.closest('.ActionRow');
    if (row) {
        row.setAttribute('data-modified', 'true');
    }
}
function populateActionStatusDropdownn(selectElement) {
    return new Promise((resolve, reject) => {
        fetch(`${APIGateWay}TaskActionStatus.php?lang=${language}`, {
            method: "GET",
            headers: { "Content-Type": "application/json" },
        })
        .then(response => response.json())
        .then(statuses => {
            selectElement.innerHTML = ""; // تفريغ القائمة الحالية
            statuses.forEach(status => {
                const option = document.createElement("option");
                option.value = status.StatusName;
                option.textContent = status.StatusName;
                selectElement.appendChild(option);
            });
            resolve(); // اكتمال تعبئة القائمة
        })
        .catch(error => {
            console.error("Error fetching statuses:", error);
            reject(error); // حدث خطأ
        });
    });
}

function fetchAssignments(taskId) {
    const checklistContainer = document.getElementById("getchecklistItem");
    checklistContainer.innerHTML = ""; // Clear the container before appending new data

    fetch(`${APIGateWay}TaskAssignments.php?taskId=${taskId}`, {
        method: "GET",
        headers: { "Content-Type": "application/json" },
    })
        .then(response => response.json()) // Parse JSON from the API response
        .then(assignments => {
            if (assignments && assignments.length > 0) {
                let counter =0;
                assignments.forEach(assignment => {
                    counter++;
                    const assignmentRow = document.createElement("div");
                    assignmentRow.classList.add("checklist-row");
                    assignmentRow.innerHTML = `
                        <table class="actiontable" style="margin-left:0">
                            <tr>
                                <td class="AssignmentID">${counter || 'N/A'}</td>
                                <td class="AssignmentInfo">${new Date(assignment.AssignmentDate).toLocaleDateString()}</td>
                                <td class="AssignmentTo-By">${assignment.AssignedTo}</td>
                                <td class="AssignmentReson">${assignment.AssignmentReason || 'N/A'}</td>
                                <td class="AssignmentUser2">
                                    ${assignment.AssignedBy}
                                    <div class="table-icon" style="width:11%">
                                        <img src="../Images/new.svg" alt="New" onclick="addNewAssignmentRow()">
                                        <img src="../Images/false.svg" alt="Delete" onclick="deleteAssignmentsaved(this, ${assignment.AssignmentId})">
                                    </div>
                                
                                </td>
                                
                            </tr>
                            

                        </table>
                    `;
                    checklistContainer.appendChild(assignmentRow);
                });
            } else {
                const noAssignment = document.createElement("p");
                noAssignment.textContent = "No assignments found.";
                
            }
        })
        .catch(error => {
            console.error("Error fetching assignments:", error);
            const errorMessage = document.createElement("p");
            errorMessage.textContent = "Error loading assignments. Please try again.";
            checklistContainer.appendChild(errorMessage);
        });
}
document.getElementById("fileUploadd").addEventListener("change", handleFileUploadd);

document.getElementById("fileUploadd").addEventListener("DOMContentLoaded", fetchAttachments);
let originalAttachments = []; // لتخزين المرفقات الأصلية

function fetchAttachments(taskId) {
    const attachmentContainer = document.getElementById("attachments-containerr");
    attachmentContainer.innerHTML = ""; // Clear existing content

    fetch(APIGateWay + `TaskAttachment.php?lang=${language}&taskId=${taskId}`, {
        method: "GET",
        headers: { "Content-Type": "application/json" },
    })
    .then(response => response.json())
    .then(data => {
        if (data && data.length > 0) {
            originalAttachments = data.map(attachment => ({
                AttachmentId: attachment.AttachmentId,
                Attachmentlocalpath: attachment.Attachmentlocalpath,
                TypeName: attachment.TypeName,
                UploadedDate: attachment.UploadedDate,
                UploadedBy: attachment.UploadedBy,
                AttachmentName: attachment.AttachmentName,
                FileName : attachment.FileName,
            }));
            data.forEach(attachment => {
                const previewContainer = createPreviewContainerFromAPI(attachment);
                attachmentContainer.appendChild(previewContainer);
            });
        } else {
            originalAttachments = []; 
            const noAttachments = document.createElement("p");
            attachmentContainer.appendChild(noAttachments);
        }
    })
    .catch(error => console.error("Error fetching attachments:", error));
}
function createPreviewContainerFromAPI(attachment) {
    const previewContainer = document.createElement("div");
    previewContainer.classList.add("attachment-preview");

    // Image container
    const imgContainer = document.createElement("div");
    imgContainer.classList.add("imgContainer");

    // Add image
    const img = document.createElement("img");
    img.classList.add("uploadedImage");
    img.src = attachment.Attachmentlocalpath;
    img.alt = attachment.AttachmentName;
    img.onclick = function () {
        openImageModal(attachment.Attachmentlocalpath);
    };

    imgContainer.appendChild(img);
    previewContainer.appendChild(imgContainer);

    // Add data table
    const table = createDataTableForAPI(attachment);
    previewContainer.appendChild(table);

    return previewContainer;
}

function createDataTableForAPI(attachment) {
    const table = document.createElement("table");
    table.classList.add("dataTable");

    table.innerHTML = `
        <tr>
            <td class="smallSize">Type:</td>
            <td class="largeSize ">${attachment.TypeName}</td>
            <td class="mediumSize " >Uploaded Date:</td>
            <td class="mixsize">${new Date(attachment.UploadedDate).toLocaleDateString("en-US")}</td>
            <td class="mediumSize ">Uploaded By:</td>
            <td>${attachment.UploadedBy}</td>
        </tr>
        <tr>
            <td>Description:</td>
            <td class="textwrite " colspan="5">${attachment.AttachmentName}</td>
                <button type="submit" class="attacadd add"  onclick="document.getElementById('fileUploadd').click()"\>
                    <img src="../Images/new.svg" alt="New">
                </button>
                <button type="button" class="attacadd delete" onclick="deleteAttachment(this, ${attachment.AttachmentId}, '${attachment.AttachmentPath}')">
                    <img src="../Images/false.svg" alt="Delete">
                </button>
        </tr>
    `;
    return table;
}

function handleFileUploadd(event) {
    const files = event.target.files;
    if (!files.length) return;

    for (const file of files) {
        if (isValidFile(file)) {
            const uniqueId = generateUniqueId();
            const previewContainer = createPreviewContainerr(file, uniqueId);

            document.getElementById("attachments-containerr").appendChild(previewContainer);
        } else {
            fetchAndShowAlert(17, 'EN').then(messageText => {
                showAlert(messageText);
            });
        }
    }

    event.target.value = ""; // Reset file input to allow re-uploading the same file
}
function showNewPhotoPreview() {
    const fileInput = document.createElement("input");
    fileInput.type = "file";
    fileInput.accept = "image/*"; // Restrict to image files
    fileInput.onchange = function () {
        const file = fileInput.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                // Create a temporary attachment object
                const newAttachment = {
                    Attachmentlocalpath: e.target.result, // Preview image as a Base64 string
                    AttachmentName: file.name,
                    AttachmentType: "Image",
                    UploadedDate: new Date().toISOString(),
                    UploadedBy: "1", // Replace with the actual user if needed
                    AttachmentId: "temp_" + Date.now() // Temporary ID
                };

                // Dynamically add the preview to the attachments container
                const previewContainer = createPreviewContainerFromAPI(newAttachment);
                document.getElementById("attachments-containerr").appendChild(previewContainer);
            };

            reader.readAsDataURL(file); // Convert file to Base64 string for preview
        }
    };

    fileInput.click(); // Trigger the file input dialog
}
function hasAttachmentChanged() {
    const currentAttachments = Array.from(document.querySelectorAll("#attachments-containerr .attachment-preview")).map(container => {
        const imgElement = container.querySelector("img.uploadedImage");
        const description = container.querySelector(".textwrite").innerText.trim();
        const type = container.querySelector(".largeSize").innerText.trim();
        const uploadedDate = container.querySelector(".mixsize").innerText.trim();
        const uploadedBy = container.querySelector(".mediumSize + td").innerText.trim();

        return {
            Attachmentlocalpath: imgElement ? imgElement.src : null,
            TypeName: type,
            UploadedDate: uploadedDate,
            UploadedBy: uploadedBy,
            AttachmentName: description,
        };
    });

    // مقارنة عدد المرفقات
    if (originalAttachments.length !== currentAttachments.length) {
        return true;
    }

    // مقارنة كل خاصية
    for (let i = 0; i < originalAttachments.length; i++) {
        const original = originalAttachments[i];
        const current = currentAttachments[i];

        if (
            original.Attachmentlocalpath !== current.Attachmentlocalpath ||
            original.TypeName !== current.TypeName ||
            original.UploadedDate !== current.UploadedDate ||
            original.UploadedBy !== current.UploadedBy ||
            original.AttachmentName !== current.AttachmentName
        ) {
            return true; // حدث تغيير
        }
    }

    return false; // لا يوجد تغيير
}
/*
function deleteAttachment(attachmentId) {
    if (confirm("Are you sure you want to delete this attachment?")) {
        fetch(APIGateWay + `TaskAttachment.php?AttachmentId=${attachmentId}`, {
            method: "DELETE",
            headers: { "Content-Type": "application/json" },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Attachment deleted successfully.");
                const taskId = document.getElementById("saveChanges").getAttribute("data-taskid");
                fetchAttachments(taskId);
                updateAttachmentCount(document.getElementById("attachmentCount"), -1);
                // Refresh attachments
            } else {
                alert("Failed to delete attachment: " + data.message);
            }
        })
        .catch(error => {
            console.error("Error deleting attachment:", error);
            alert("An error occurred while deleting the attachment.");
        });
    }
}
*/
function openImageModal(imagePath) {
    const modal = document.getElementById("imageModal");
    const modalImg = document.getElementById("modalImage");
    modal.style.display = "block";
    modalImg.src = imagePath;
}

function closeImageModal() {
    const modal = document.getElementById("imageModal");
    modal.style.display = "none";
}

function renderPriorityStars(priorityId) {
    const stars = document.querySelectorAll("#Taskpriority .starr");

    // تحديث مظهر النجوم بناءً على PriorityId
    stars.forEach((star, index) => {
        if (index < priorityId) {
            star.classList.add("selected");
        } else {
            star.classList.remove("selected");
        }
    });
}


function SetTaskStage(currentStage) {
    const stageSelect = document.getElementById('TaskStage'); // احصل على العنصر select

    // التكرار عبر الخيارات وتحديد الخيار الصحيح
    for (let i = 0; i < stageSelect.options.length; i++) {
        const option = stageSelect.options[i];
        if (option.textContent == currentStage) {
            option.selected = true; // تعيين الخيار المطابق
            break;
        }
    }
}

function SetTaskCategory(currentCategory) {
    const CategorySelect = document.getElementById('TaskCategory'); // احصل على العنصر select

    // التكرار عبر الخيارات وتحديد الخيار الصحيح
    for (let i = 0; i < CategorySelect.options.length; i++) {
        const option = CategorySelect.options[i];
        if (option.textContent == currentCategory) {
            option.selected = true; // تعيين الخيار المطابق
            break;
        }
    }
}
function SetTaskStatus(currentStatus) {
    const StatusSelect = document.getElementById('TaskStatus'); // احصل على العنصر select

    // التكرار عبر الخيارات وتحديد الخيار الصحيح
    for (let i = 0; i < StatusSelect.options.length; i++) {
        const option = StatusSelect.options[i];
        if (option.textContent == currentStatus) {
            option.selected = true; // تعيين الخيار المطابق
            break;
        }
    }
}

function SetTaskType(currentType) {
    const TypeSelect = document.getElementById('TaskType'); // احصل على العنصر select

    // التكرار عبر الخيارات وتحديد الخيار الصحيح
    for (let i = 0; i < TypeSelect.options.length; i++) {
        const option = TypeSelect.options[i];
        if (option.textContent == currentType) {
            option.selected = true; // تعيين الخيار المطابق
            break;
        }
    }
}
//

function closeNewModal() {
    document.getElementById("newModal").style.display = "none";
}

//do