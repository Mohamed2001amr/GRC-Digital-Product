//حذف النص عند الإدخال في حقل البحث
// اختيار عناصر الإدخال وعلامة "X"
const searchInput = document.querySelector('.search-input');
const clearIcon = document.querySelector('.deletesearch');

// حدث لإظهار علامة "X" عند الكتابة
searchInput.addEventListener('input', () => {
    if (searchInput.value) {
        clearIcon.style.display = 'inline'; // عرض علامة "X"
    } else {
        clearIcon.style.display = 'none'; // إخفاء علامة "X" إذا كان الحقل فارغًا
    }
});

// حدث لمسح النص عند النقر على علامة "X"
clearIcon.addEventListener('click', () => {
    searchInput.value = '';
    clearIcon.style.display = 'none'; // إخفاء علامة "X" بعد المسح
    searchInput.focus(); // إعادة التركيز إلى مربع البحث
});
///////////////////////////////Display Message/////////////////////////////////////
// Function to display the footer message
function showFooterMessage(message) {
    const footerMessage = document.getElementById('footerMessage');
    footerMessage.textContent = message; // Set the message text
    footerMessage.style.display = 'block'; // Show the message

    // Hide the message after 30 seconds
    setTimeout(() => {
        footerMessage.style.display = 'none';
    }, 15000); // 15 seconds
}
///////////////////////////////Toggle left tab/////////////////////////////////////
function toggleSidebar() {
    const sidebar = document.getElementById('task-filters');
    const mainContent = document.getElementById('main-content2');
    sidebar.classList.toggle('open');
    mainContent.classList.toggle('shrink');
}
///////////////////////////Add Asignment from Assign Icon/////////////////////////////////////////

////////////////////////////////////////////////////////////////////
// إظهار المحتوي الخاص بكل تاب في الصفحة الفرعية
function showTabContent(index) {
    // إخفاء كل المحتوى
    let contents = document.querySelectorAll('.tab-content');
    contents.forEach(content => content.classList.remove('active'));

    // إظهار المحتوى المختار
    document.getElementById(`content-${index}`).classList.add('active');

    // تغيير حالة التاب
    let tabs = document.querySelectorAll('.tab-buttons div');
    tabs.forEach(tab => tab.classList.remove('active'));
    tabs[index].classList.add('active');
}


function showTabContentt(index) {
    // إخفاء كل المحتوى
    let contentss = document.querySelectorAll('.newtab');
    contentss.forEach(contentt => contentt.classList.remove('active'));

    // إظهار المحتوى المختار
    document.getElementById(`contentt-${index}`).classList.add('active');

    // تغيير حالة التاب
    let tabss = document.querySelectorAll('.newtabButton div');
    tabss.forEach(tab => tab.classList.remove('active'));
    tabss[index].classList.add('active');
}
////////////////////////////////////////////////////////////////////
// إظهار الاولوية من خلال شكل النجوم
function setPrioritys(starss, level) {
    starss.forEach((starr, index) => {
    starr.classList.toggle('selected', index < level);
    });
}
const starss = document.querySelectorAll('.starr');

// إظهار الاولوية من خلال شكل النجوم
function setPriority(stars, level) {
    stars.forEach((star, index) => {
    star.classList.toggle('selected', index < level);
    });
}
const stars = document.querySelectorAll('.star');
////////////////////////////////////////////////////////////////////
// وظيفة إغلاق نافذة التكبير
function closeImage() {
    const closeimg = document.getElementById("imageModal");
    closeimg.style.display="none";
}
////////////////////////////////////////////////////////////////////
// وظيفة إغلاق نافذة التكبير
function closeImagee() {
    const closeimg = document.getElementById("imageModall");
    closeimg.style.display="none";
}
////////////////////////////////////////////////////////////////////
/*
اضافة صورة جديدة من خلال زر الاضافة جنب كل جدول خاص بالصورة
حذف صورة خلال زر الحذف جنب كل جدول خاص بالصورة
تحميل صورة من خلال الزرار المخصص لذلك
*/

document.getElementById("fileUpload").addEventListener("change", handleFileUpload);

function handleFileUpload(event) {
    const files = event.target.files;
    if (!files.length) return;

    const attachmentCount = document.getElementById("getattachmentCount");
    updateAttachmentCount(attachmentCount, files.length);

    for (const file of files) {
        if (isValidFile(file)) {
            const uniqueId = generateUniqueId();
            const previewContainer = createPreviewContainer(file, uniqueId);

            document.getElementById("attachments-container").appendChild(previewContainer);
        } else {
            fetchAndShowAlert(17, 'EN').then(messageText => {
                showAlert(messageText);
            });
            return;
        }
    }

    event.target.value = ""; // Reset file input to allow re-uploading the same file
}

function createPreviewContainer(file, uniqueId) {
    const previewContainer = document.createElement("div");
    previewContainer.classList.add("attachment-preview");

    const imgContainer = document.createElement("div");
    imgContainer.classList.add("imgContainer");

    const img = document.createElement("img");
    img.classList.add("uploadedImage");
    img.id = uniqueId;

    const reader = new FileReader();
    reader.onload = (e) => {
        img.src = e.target.result;
        img.onclick = () => openImageModal(e.target.result);
    };
    reader.readAsDataURL(file);

    imgContainer.appendChild(img);
    previewContainer.appendChild(imgContainer);

    const table = createDataTable(uniqueId);
    previewContainer.appendChild(table);

    return previewContainer;
}

function createDataTable(uniqueId) {
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
            <td><input type="text" name="uploadedByy" value="User Name" class="taskin" readonly></td>
        </tr>
        <tr>
            <td> Description:</td>
            <td class="textwrite" colspan="10"><textarea placeholder="Write Description"></textarea></td>
                <button type="button" class="attacadd add" onclick="addNewFileRoww(this)">
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

function addNewFileRoww(button) {    
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
    document.getElementById("fileUpload").click();
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

 function changeImage(button) {
    const targetImageId = button.getAttribute("data-id"); // Get the unique image ID
    const targetImage = document.getElementById(targetImageId); // Find the corresponding image

    if (!targetImage) {
        console.error(`Image element with ID '${targetImageId}' not found.`);
        return;
    }

    const fileInput = document.createElement("input");
    fileInput.type = "file";
    fileInput.accept = "image/*";
    fileInput.style.display = "none";

    fileInput.onchange = (event) => {
        const file = event.target.files[0];
        if (isValidFile(file)) {
            const reader = new FileReader();
            reader.onload = (e) => {
                targetImage.src = e.target.result; // Update the image source directly
            };
            reader.readAsDataURL(file);
        } else {
            fetchAndShowAlert(17, 'EN').then(messageText => {
                showAlert(messageText);
            });
            return;
        }
    };

    document.body.appendChild(fileInput); // Add the input temporarily
    fileInput.click(); // Trigger the file picker
    document.body.removeChild(fileInput); // Remove the input element after use
}
let deletedAttachments = [];

function deleteImagenotsaved(button) {
    const preview = button.closest(".attachment-preview");
    if (preview) {
        preview.remove();
        updateAttachmentCount(document.getElementById("attachmentCount"), -1);
    }
}

function deleteAttachment(button,AttachmentId,Attachmentlocalpath) {
    const preview = button.closest(".attachment-preview");

    // إضافة الصورة إلى قائمة الحذف
    deletedAttachments.push({
        Attachmentlocalpath: Attachmentlocalpath, // المسار الفعلي للصورة
        AttachmentId: AttachmentId,               // المعرف الفريد للصورة
    });
    // إزالة العنصر من واجهة المستخدم
    preview.remove();
}

function openImageModal(src) {
    const modal = document.getElementById("imageModal");
    const modalImg = document.getElementById("modalImage");
    modalImg.src = src;
    modal.style.display = "block";
}

function isValidFile(file) {
    const maxSize = 5 * 1024 * 1024; // 5 MB
    const validTypes = ["image/jpeg", "image/png", "image/gif"];
    return validTypes.includes(file.type) && file.size <= maxSize;
}

function generateUniqueId() {
    return `uploadedImage_${Date.now()}_${Math.random().toString(36).slice(2, 11)}`;
}

////////////////////////////////////////////////////////////////////
//فاتح الصفحة الفرعية عند اختيار بيانات من الجدول الرئيسي
function openModal() {
    document.getElementById("myModal").style.display = "flex";
    fetchActionStatuses();
}

function closeModal() {
    document.getElementById("myModal").style.display = "none";
}

// إغلاق النافذة عند النقر خارجها
window.onclick = function(event) {
    var modal = document.getElementById("myModal");
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

////////////////////////////////////////////////////////////////////
function addContent() {
    // الحصول على التبويب المفتوح حالياً
    const activeTab = document.querySelector(".tab-content.active");
    // التحقق من التبويب المفتوح وإضافة محتوى مخصص
    // التحقق من التبويب المفتوح وإضافة محتوى مخصص
    if (activeTab.id === "content-1") {
        // البحث عن الجدول الافتراضي
        const defaultTable = activeTab.querySelector(".checklist-items.DeafultTable tbody");

        if (!validateFieldsBeforeAddingRow(defaultTable, "ActionRow1")){
            return;
        }
        else {
            const newRow = document.createElement("tr");
            newRow.classList.add("ActionRow1");

            // بناء محتوى الصف الجديد
            newRow.innerHTML = `
                <td class="actionCheckHeader">
                    <input type="text" name="ActionId" placeholder="" class="taskin NotSelect readonly" readonly ">
                </td>
                <td class="actionText">
                    <span style="color: red;">*</span>
                    <input type="text" class="inserteditable-text" placeholder="New CheckList Item" onfocus="this.select()">
                </td>
                <td class="actionStatusContainer">
                    <span style="color: red;">*</span>
                    <select name="insertActionStatus" class="taskin insertActionStatus">
                        <option>Loading...</option>
                    </select>
                </td>
                <td class="actionDateContainer">
                    <input type="date" name="task-dates" value="${new Date().toISOString().split('T')[0]}" class="insertDeafultDate readonly" readonly>
                    <div class="table-icon">
                        <img src="../Images/new.svg" alt="New" onclick="addContent()">
                        <img src="../Images/false.svg" alt="Delete" onclick="deleteAction(this)">
                    </div>
                </td>
            `;

            // إضافة الصف الجديد تحت الجدول الافتراضي
            defaultTable.appendChild(newRow);

            // جلب حالات القائمة المنسدلة
            populateActionStatuses(newRow.querySelector("select.insertActionStatus"));
        }; 
        }else if (activeTab.id === "content-5") {
            
            addNewActionRow();

    }
    //
     else if (activeTab.id === "content-3") {
        const checklistContainer = document.getElementById('checklistItem');
        const defaultAssignmentTable = activeTab.querySelector(".checklist-item.DeafultAssign tbody");
        if (!validateFieldsBeforeAddingRow(defaultAssignmentTable, "assignRow1")) return;
        const newrow = document.createElement("tr");
        newrow.classList.add("assignRow1");
      
        newrow.innerHTML = `
            <td class="actionCheckHeader">
                <input type="text" name="ActionId" placeholder="" class="taskin NotSelect" readonly >
            </td>
            <td class="actionDateContainer">${new Date().toLocaleDateString("en-US")}</td>
            <td class="assignmentuser">
                <span style="color: red;">*</span>
                <input type="text" name="ActionId" placeholder="The person's name">
            </td>
            <td class="assignmentReason">
                <span style="color: red;">*</span>
                <input type="text" name="ActionId" placeholder="Enter Assigenment Reason">
            </td>
            <td class="assignmentuser2">
                <input type="text" name="ActionId" value="User Name">
                <div class="table-icon">
                    <img src="../Images/new.svg" alt="New" onclick="addContent()">
                    <img src="../Images/false.svg" alt="Delete" onclick="deleteAssignmentnew(this)">
                </div>
            </td>
        `;
        defaultAssignmentTable.appendChild(newrow); 
        
        
    }else if (activeTab.id === "content-7") {
        
        const defaultAssignmentTable = document.getElementById("getchecklistItem");
        if (!validateFieldsBeforeAddingRow(defaultAssignmentTable, "assignRow1"))
            { 
                return;

            }
        else{ // تحقق قبل إضافة صف جديد
            addNewAssignmentRow();
        }
    }
   

    // إضافة المحتوى الجديد إلى التبويب المفتوح
}
function addNewActionRow() {
    const activeTab = document.querySelector(".tab-content.active");
    const defaultTable = activeTab.querySelector(".checklist-items.DeafultTable tbody");



    if (!validateFieldsBeforeAddingRow(defaultTable, "ActionRow1")) return; // تحقق قبل إضافة صف جديد

    // تحديد الحاوية المناسبة
    if (!defaultTable) {
        alert("No default table found in the active tab.");
        return;
    }
    

    // إنشاء صف جديد
    const NewActionRow = document.createElement("tr");
    NewActionRow.classList.add("ActionRow1");
    NewActionRow.setAttribute("data-new", "true");

    // محتوى الصف الجديد
    NewActionRow.innerHTML = `
        <td class="actionCheckHeader">
            <input type="text" name="ActionId" placeholder="" class="taskin NotSelect" readonly">
        </td>
        <td class="actionText">
            <span style="color: red;">*</span>
            <input type="text" name = "insertActionName"class="inserteditable-text" placeholder="New CheckList Item" onfocus="this.select()">
        </td>
        <td class="actionStatusContainer">
            <span style="color: red;">*</span>
            <select name="insertActionStatus" class="taskin insertActionStatus">
                <option>Loading...</option>
            </select>
        </td>
        <td class="actionDateContainer">
            <input type="date" name="task-dates" value="${new Date().toISOString().split('T')[0]}" class="insertDeafultDate readonly" readonly>
            <div class="table-icon">
                <img src="../Images/new.svg" alt="New" onclick="addNewActionRow()">
                <img src="../Images/false.svg" alt="Delete" onclick="deleteAction(this)">
            </div>
        </td>
    `;

    // إضافة الصف الجديد إلى الجدول
    defaultTable.appendChild(NewActionRow);

    // تعبئة القائمة المنسدلة
    populateActionStatuses(NewActionRow.querySelector("select.insertActionStatus"));
}


function populateActionStatuses(selectElement) {
    // جلب الخيارات من مصدر البيانات (مثال: API)
    fetch(`${APIGateWay}TaskActionStatus.php`, {
        method: "GET",
        headers: { "Content-Type": "application/json" },
    })
        .then(response => response.json())
        .then(statuses => {
            if (statuses && statuses.length > 0) {
                selectElement.innerHTML = ""; // تفريغ الخيارات السابقة
                statuses.forEach(status => {
                    const option = document.createElement("option");
                    option.value = status.StatusId; // القيمة
                    option.textContent = status.StatusName; // النص الظاهر
                    selectElement.appendChild(option);
                });
            } else {
                selectElement.innerHTML = `<option value="">No statuses found</option>`;
            }
        })
        .catch(error => {
            console.error("Error fetching statuses:", error);
            selectElement.innerHTML = `<option value="">Error loading statuses</option>`;
        });
}
let deletedActions = [];

function deleteAction(button) {
    button.closest(".ActionRow1").remove();
}
function deleteActionsaved(button, ActionId) {
    deletedActions.push({ ActionId: ActionId });
    button.closest(".ActionRow").remove();
}


let deletedAssignments = [];

// حذف تعيين جديد (لم يتم حفظه بعد)
function deleteAssignmentnew(button) {
    button.closest(".checklist-row").remove();
}

// حذف تعيين محفوظ (تم جلبه من قاعدة البيانات)
function deleteAssignmentsaved(button, assignmentId) {
    deletedAssignments.push({ AssignmentId: assignmentId });
    button.closest(".checklist-row").remove();
}
function addNewAssignmentRow() {
    const checklistContainer = document.getElementById("getchecklistItem");

    const newAssignmentRow = document.createElement("div");
    newAssignmentRow.classList.add("checklist-row");
    newAssignmentRow.setAttribute("data-new", "true");

    newAssignmentRow.innerHTML = `
        
        <table class="actiontable">
            <tr>
                <td class="AssignmentID">
                    <input type="text"  placeholder="Id" class="taskin NotSelect" readonly ">
                </td>
                <td class="AssignmentInfo">${new Date().toLocaleDateString("en-US")}</td>
                <td class="assignmentuser">
                    <span style="color: red;">*</span>
                    <input type="text" name="AssignedTo" placeholder="The person's name">
                </td>
                <td class="AssignmentReson">
                    <span style="color: red;">*</span>
                    <input type="text" name="AssignmentReason" placeholder="Enter Assignment Reason">
                </td>
                <td class="AssignmentUser2">
                    <input type="text" name="AssignedBy" placeholder="Assigned By" style="width:75%">
                    <div class="table-icon style="width:11%">
                        <img src="../Images/new.svg" alt="New" onclick="addContent()">
                        <img src="../Images/false.svg" alt="Delete" onclick="deleteAssignmentnew(this)">
                    </div>
                </td>
                
            </tr>
        </table>
    `;

    checklistContainer.appendChild(newAssignmentRow);
}
////////////////////////////////////////////////////////////////////
 // تحديث زر الحذف بناءً على حالة مربعات الاختيار
 function updateDeleteButton() {
    const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]');
    const deleteButton = document.getElementById('deleteButton');
    const subDeleteButton = document.getElementById('subDeleteButton');
    const anyChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);

    if (anyChecked) {
        deleteButton.disabled = false;
        subDeleteButton.disabled = false;
        deleteButton.classList.add('enabled');
        subDeleteButton.classList.add('enabled');
    } else {
        deleteButton.disabled = true;
        subDeleteButton.disabled = true;
        deleteButton.classList.remove('enabled');
        subDeleteButton.classList.remove('enabled');
    }
}

// تغيير خلفية الصف بناءً على حالة مربع الاختيار
function updateRowBackground(checkbox) {
    const row = checkbox.closest('tr');
    if (checkbox.checked) {
        row.classList.add('selected');
    } else {
        row.classList.remove('selected');
    }
}

// حذف الصفوف المحددة
function deleteSelectedRows() {
    const rows = document.querySelectorAll('tbody tr');

    rows.forEach(row => {
        const checkbox = row.querySelector('input[type="checkbox"]');
        if (checkbox.checked) {
            confirmDelete();
        }
    });

    updateDeleteButton(); // تحديث حالة الزر بعد الحذف
}
////////////////////////////Filter////////////////////////////////////////
function toggleDropdown(id) {
    const dropdown = document.getElementById(id);
    // Close all dropdowns
    document.querySelectorAll('.dropdownListFilter').forEach(d => {
        if (d !== dropdown) d.style.display = 'none';
    });
    // Toggle the current dropdown
    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
}

// Close dropdowns when clicking outside
window.addEventListener('click', function(e) {
    if (!e.target.matches('.dropdown-button')) {
        document.querySelectorAll('.dropdownListFilter').forEach(d => d.style.display = 'none');
    }
});
////////////////////////////Add Comment to Activity////////////////////////////////////////
// اضافة الكومنت في حقل الاكتيفتي
document.getElementById("addCommentBtn").addEventListener("click", function() {
    // جلب قيمة التعليق من حقل الإدخال
    const comment = document.getElementById("commentInput").value;

    // التأكد من أن الحقل ليس فارغًا
    if (comment.trim() === "") {
        fetchAndShowAlert(18, 'EN').then(messageText => {
            showAlert(messageText);
        });
        return;
    }

    // جلب الجدول الخاص بالتعليقات
    const table = document.getElementById("commentsTable").getElementsByTagName('tbody')[0];

    // إنشاء صف جديد
    const newRow = table.insertRow();

    // إضافة البيانات للصف الجديد
    const commentCell = newRow.insertCell(0);
    const dateCell = newRow.insertCell(1);

    commentCell.textContent = comment;
    dateCell.textContent = new Date().toLocaleDateString();

    // مسح حقل الإدخال
    document.getElementById("commentInput").value = "";
});


// اضافة الكومنت في حقل الاكتيفتي
document.getElementById("addCommentBtnn").addEventListener("click", function() {
    // جلب قيمة التعليق من حقل الإدخال
    const comment = document.getElementById("commentInputt").value;

    // التأكد من أن الحقل ليس فارغًا
    if (comment.trim() === "") {
        fetchAndShowAlert(18, 'EN').then(messageText => {
            showAlert(messageText);
        });
        return;
    }

    // جلب الجدول الخاص بالتعليقات
    const table = document.getElementById("commentsTablee").getElementsByTagName('tbody')[0];

    // إنشاء صف جديد
    const newRow = table.insertRow();

    // إضافة البيانات للصف الجديد
    const commentCell = newRow.insertCell(0);
    const dateCell = newRow.insertCell(1);

    commentCell.textContent = comment;
    dateCell.textContent = new Date().toLocaleDateString();

    // مسح حقل الإدخال
    document.getElementById("commentInputt").value = "";
});
///////////////////////////Default Date Value/////////////////////////////////////////
document.addEventListener("DOMContentLoaded", () => {
    const dateFields = document.querySelectorAll(".insertDeafultDate");
    const currentDate = new Date().toISOString().split('T')[0];

    dateFields.forEach(field => {
        field.value = currentDate;
    });
});
///////////////////////////////Alert Mesaage//////////////////////////////////////////
function showAlert(message) {
    const alertOverlay = document.getElementById('customAlert');
    const alertMessage = document.getElementById('alertMessage');
    alertMessage.textContent = message; // تغيير الرسالة
    alertOverlay.style.display = 'flex'; // عرض النافذة
}

// دالة لإغلاق النافذة
function closeAlert() {
    const alertOverlay = document.getElementById('customAlert');
    alertOverlay.style.display = 'none'; // إخفاء النافذة
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

// دالة للتحقق من الحقول قبل إضافة صف جديد
// دالة للتحقق من الحقول قبل إضافة صف جديد
function validateFieldsBeforeAddingRow(container, rowClass) {
    // حدد جميع الأسطر الحالية في التبويب النشط
    const rows = container.querySelectorAll(`.${rowClass}`);
    if (rows.length === 0) return true; // إذا لم تكن هناك أسطر، يمكن إضافة سطر جديد

    // تحقق من أن جميع الحقول المطلوبة ليست فارغة
    for (const row of rows) {
        const inputs = row.querySelectorAll("input:not([type='hidden']):not(.NotSelect), select, textarea");
        for (const input of inputs) {
            if (input.value.trim() === "") {
                fetchAndShowAlert(11, 'EN').then(messageText => {
                    showAlert(messageText);
                });
                return false; // إذا كان هناك أي حقل فارغ، أوقف العملية
            } else {
                // أزل التمييز إذا كان الحقل ممتلئًا
                input.style.border = "";
            }
        }
    }
    return true; // جميع الحقول ممتلئة
}