
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tasks</title>
    <link rel="stylesheet" href="tasks.css">

    <!-- رابط مكتبة Select2 وjQuery -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
</head>
<body>
    <header>
        <img src="../Images/header-logo.png" alt="Logo" class="logo">
        <nav class="header-tabs">
            <ul>
                <li><a href="#"> Tasks </a> </li> 
                <li><a href="#"> Report </a> </li>
            </ul>
            <div class="dropdown">
                <img src="../Images/bar.png" alt="Logo" class="bar">
                <div class="dropdown-content">
                    <ul class="dropdown-content-li">
                        <li><a href="#"> Performance </a></li>
                        <li><a href="#"> Compliance </a></li>
                        <li><a href="#"> Assets </a></li>
                        <li><a href="#"> Crisis </a></li>
                        <li><a href="#"> Risk </a></li>
                        <li><a href="#">BCM </a></li>
                    </ul>
                </div>
            </div>
            <img src="../Images/notification.svg" alt="Logo" class="not">
        </nav>
        <div class="dropdown-user">
            <img src="../Images/user.svg" alt="Logo" class="arrow">
            <div class="dropdown-content-user">
                <ul class="dropdown-content-li">
                    <li> <a href="#"> Setting </a></li>
                    <li> <a href="#"> SignOut </a></li>
                    <li class="lang" for="languageSelect"> <a href="#"> language </a>
                        <ul class="lang-sett" id="languageSelect">
                            <li> <a href="?lang=english">English</a> </li>
                            <li> <a href="?lang=arabic">العربية</a> </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="main-content3">
            <div class="container filter">
                <div class="serachcon">
                    <div class="search-bar">
                        <form method="GET" action="">
                            <img src="../Images/search.svg" alt="Logo" class="serach">
                            <input type="text" name="search_id" placeholder="Search" class="search-input"/>
                            <img src="../Images/sliders.svg" alt="Logo" class="slider">
                            <img src="../Images/false.svg" alt="delete" class="deletesearch">
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="task-filters">
            <div class="hamburger-menu" onclick="toggleSidebar()">
                ☰
            </div>
            <!-- <ul id="task-filters">
            <li>
                <a href="#"> <img src="../Images/todolist.jpg" alt="Logo">
                    <span>
                        <input type="checkbox" name="Home" value="Home">
                        Home
                    </span>
                </a>
            </li>

            <li>
                <a href="#"> <img src="../Images/todolist.jpg" alt="Logo">
                    <span>
                        <input type="checkbox" name="Tasks" value="Tasks">
                        Tasks
                    </span>
                </a>
            </li>

            <li>
                <a href="#"> <img src="../Images/todolist.jpg" alt="Logo"> 
                    <span>
                        <input type="checkbox" name="Documents" value="Documents">
                        Documents
                    </span>
                </a>
            </li>

            <li>
                <a href="#"> <img src="../Images/todolist.jpg" alt="Logo"> 
                    <span>
                        <input type="checkbox" name="Calendar" value="Calendar">
                        Calendar
                    </span>
                </a>
            </li>

            <li>
                <a href="#"> <img src="../Images/todolist.jpg" alt="Logo"> 
                    <span>
                        <input type="checkbox" name="Alerts" value="Alerts">
                        Alerts
                    </span>
                </a>
            </li>
        </ul> -->
        </div>

        <!-- main container 2 -->                 
        <div class="main-content2">
            <div class="table-header">
                <nav class="header-content "></nav>
                <nav class="header-content header-id">ID</nav>
                <nav class="header-content header-name">Task</nav>
                <nav class="header-content header-status">
                    Status
                    <button class="dropdown-button" onclick="toggleDropdown('StatusFilter')">▼</button>
                    <ul id ="StatusFilter" class="dropdownListFilter">
                        <!-- Fil From DataBase -->
                    </ul>
                </nav>
                <nav class="header-content header-date">Due Date</nav>
                <nav class="header-content header-type">
                    Type
                    <button class="dropdown-button" onclick="toggleDropdown('TypeFilter')">▼</button>
                    <ul id = "TypeFilter" class="dropdownListFilter">
                        <!-- Fil From DataBase -->
                    </ul>
                </nav>
            </div>
            <div class="table"> 
                <table id="TaskTableBody">
                    <tbody>
                <!-- fill from database    -->
                    </tbody>
                </table>
                
                <div class="modal" id="myModal">
                    <div class="modal-content">
                        <div class="modaltab">
                            <div class="modaltabR">
                                <span class="close" onclick="closeModal()">&times;</span>
                                <img src="../Images/ellipsis.svg" alt="Logo" class="icon1">
                                <button type="button" class="icon1">Share</button>
                                <img src="../Images/assignp.svg" alt="Logo" class="icon1" onclick="addAssignment()">

                                <div class="dropdown-list" id="dropdownList">
                                    <!-- Fill From Data -->
                                </div>

                                <h3>Activity</h3>

                                <div class="stage-line" id="stageLine">
                                    <h4>Stage-Status <span></span></h4>
                                </div>
                            </div>

                            <div class="modaltabL">
                                <span><input type="text" name="task dates" id="InsertTaskName" placeholder="Task Name"></span>                                
                            </div>
                        </div><hr style="height:0.5px;border-width:0;color:gray;background-color:#6f6f702e">        
                        
                        <div class="task-info">
                            <div class="task-attachment">

                                <div class="tab-buttons">
                                    <div onclick="showTabContent(0)" class="tabdiv active">Task</div>
                                    <div onclick="showTabContent(1)" class="tabdiv">Actions</div>
                                    <div onclick="showTabContent(2)" class="tabdiv">Attachments </div>
                                    <div onclick="showTabContent(3)" class="tabdiv">Assignments</div>
                                </div>

                                <div id="content-0" class="tab-content active">
                                    <div> 
                                        <table class="taskInUpDes">
                                            <tr>
                                                <td class="taskFiled">Category</td>
                                                <td>
                                                    <select name="task Category" class="taskCategory" id="InsertTaskCategory"  >
                                                        <!-- fill from database    -->
                                                    </select>
                                                </td>
                                                <td class="Middletask">Refrence</td>
                                                <td><input type="text" name="task dates" class="taskin readonly" id="InsertRefrence" placeholder="ID" value="1" readonly></td>

                                                <td class="Middletask">Department</td>
                                                <td>
                                                    <input type="text" name="task dates" class="taskin readonly" id="InsertRefrence" value="Tasks" readonly>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Stage</td>
                                                <td>
                                                    <select name="task Stage"  class="TaskStage" id="InsertTaskStage">
                                                        <!-- Fill From DataBase -->
                                                    </select>
                                                </td>
                                                <td class="Middletask">Status</td>
                                                <td>
                                                    <select  class="taskStatus" id="InsertTaskStatus">
                                                        <!-- Fill From DataBase -->
                                                    </select>
                                                </td>
                                                <td class="taskbfil">Status Date</td>
                                                <td><input type="Date" name="task dates" placeholder="Empty" class="TaskDate" id="StatusDate" readonly></td>
                                            </tr>
                                            <tr>
                                                <td>Priority</td>
                                                <td>
                                                    <div id="insertpriority">
                                                        <span class="star" data-value="1">&#9733;</span>
                                                        <span class="star" data-value="2">&#9733;</span>
                                                        <span class="star" data-value="3">&#9733;</span>
                                                        <span class="star" data-value="4">&#9733;</span>
                                                        <span class="star" data-value="5">&#9733;</span>
                                                    </div>
                                                </td>
                                                <td class="Middletask">Due Date</td>
                                                <td><input type="Date" name="task dates" class="TaskDate insertDeafultDate" id="InsertDueDate"></td>
                                                <td>Type</td>
                                                <td>
                                                    <select name="task Type" class="taskType" id ="InsertTaskType" >
                                                        <!-- Fill From DataBase -->
                                                    </select>
                                                </td>
                                            </tr>
                                        </table>       
                                            
                                        <div class="task-description"id = "InsertTaskDescription">
                                            <textarea placeholder="Add Description"></textarea>
                                        </div>
                                            <table class="taskInUnderDes">
                                                <tr>
                                                    <td class="taskName">Created By:</td>
                                                    <td><input type="text" name="task dates" placeholder="Name Display Automatic" class="taskin" readonly></td>
                                                    <td class="taskdate">Creation Date:</td>
                                                    <td class="dateCreation">
                                                        <input type="date" name="task_dates" class="TaskDate2" id="CreationDate" readonly>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td class="taskName">Assigned By:</td>
                                                    <td><input type="text" name="task dates" placeholder="Name Display Automatic" class="taskin" readonly></td>
                                                    <td class="taskdate">Assignment Date</td>
                                                    <td><input type="date" name="task dates" class="TaskDate2" id="CreationDate" readonly></td>
                                                </tr>
                                            </table>
                                    </div>
                                </div>

                                <div id="content-2" class="tab-content">

                                    <div id="upload-section">
                                        <!-- رفع الملف -->
                                        <div class="upload-container" onclick="document.getElementById('fileUpload').click()">
                                            <p><span>upload</span></p>
                                            <input type="file" id="fileUpload" multiple />
                                        </div>
                                        <!-- مكان عرض الصور والبيانات -->
                                        <div id="attachments-container">
                                            <input type="file" id="fileInput" accept="image/*" style="display: none;" onchange="updateImage(this)">

                                            <!-- نافذة التكبير -->
                                            <div id="imageModal" class="image-modal" onclick="closeImage()">
                                                <span class="close-modal" onclick="closeImage()">&times;</span>
                                                <img id="modalImage" class="image-modal-content">
                                            </div>

                                            <div class="attachment-preview">
                                                <div class="imgContainer">
                                                    <img class="uploadedImage" id="defaultAttacId" src="../Images/1.png">
                                                </div>
                                                <table class="dataTable">
                                                    <tbody>
                                                        <tr>
                                                            <td class="smallSize"> <span style="color: red;">*</span> Type:</td>
                                                            <td class="largeSize">
                                                                <select name="attachmentTypee" class="grayColor taskAttachmentType">
                                                                    <option value="Contract">Contract</option>
                                                                    <option value="Power-of-Attorney">Power of Attorney</option>
                                                                    <option value="Identity-Proof">Identity Proof</option>
                                                                </select>
                                                            </td>
                                                            <td class="mediumSize">Uploaded Date:</td>
                                                            <td class="mixsize mixsizee">
                                                                <input type="date" name="attachmentDate" class="insertDeafultDate readonly" readonly/>
                                                            </td>
                                                            <td class="mediumSize" >Uploaded By:</td>
                                                            <td>
                                                                <input type="text" name="uploadedByy" placeholder="Uploaded by" class="taskin readonly" value="User Name" readonly>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td> <span style="color: red;">*</span> Description:</td>
                                                            <td class="textwrite" colspan="10"><textarea class="DeafultDes" placeholder="Write Description"></textarea></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <button type="button" class="attacadd add" onclick="addNewFileRow(this)">
                                                    <img src="../Images/new.svg" alt="Add New">
                                                </button> 
                                                <button type="button" class="attacadd add" data-id="defaultAttacId" onclick="changeImage(this)">
                                                    <img src="../Images/upload.svg" alt="Upload Image">
                                                </button>
                                                <button type="button" class="attacadd delete" onclick="deleteImage(this)">
                                                    <img src="../Images/false.svg" alt="Delete">
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="content-1" class="tab-content">
                                    <div class="checklist-container">
                                        <!-- Checklist box -->
                                        <div class="checklist-box" id="checklist1">
                                            <div class="checklist-header">
                                                <table class="actionTableHeader">
                                                    <tr>
                                                        <td class="actionCheckHeader">Id</td>
                                                        <td class="actionNameHeader">Name</td>
                                                        <td class="actionStatusHeader">Status</td>
                                                        <td>Status Date</td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="checklist-items DeafultTable">
                                                <table class="actiontable ActionDeafult">
                                                    <tbody>
                                                        <tr class="ActionRow1">
                                                            <td class="actionCheckHeader">
                                                                <input type="text" name="ActionId" placeholder="" class="taskin NotSelect readonly"  readonly >
                                                            </td>
                                                            <td class="actionText">
                                                                <span style="color: red;">*</span>
                                                                <input type="text" class="editable-text" id ="insertactiondetails" placeholder="New CheckList Item" onfocus="this.select()">
                                                            </td>
                                                            <td class="actionStatusContainer">
                                                                <span style="color: red;">*</span>
                                                                <select name="insertActionStatus"  class="taskin insertActionStatus">
                                                        
                                                                </select>
                                                            </td>
                                                            <td class="actionDateContainer">
                                                                <input type="Date" name="task-dates" class="insertDeafultDate readonly" readonly>
                                                                <div class="table-icon">
                                                                    <img src="../Images/new.svg" alt="New" onclick="addContent()">
                                                                    <img src="../Images/false.svg" alt="Delete" onclick="deleteAction(this)">
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="content-3" class="tab-content">
                                    <div class="checklist-container">
                                        <!-- Checklist box -->
                                        <div class="checklist-box" id="assignment11">
                                            <div class="checklist-header">
                                                <table class="actionTableHeader">
                                                    <tr>
                                                        <td class="AssignmentID">Id</td>
                                                        <td class="AssignmentInfo">Assigned Date</td>
                                                        <td class="AssignmentTo-By">Assigned To</td>
                                                        <td class="AssignmentReson">Assigned Reason</td>
                                                        <td>Assigned By</td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="checklist-item DeafultAssign" id="checklist-item-assign">
                                                <table class="actiontable assignmenttale">
                                                    <tbody id="userTableBody">
                                                        <tr class="assignRow1">
                                                            <td class="actionCheckHeader">
                                                                <input type="text" name="ActionId" placeholder="" class="taskin NotSelect" >
                                                            </td>
                                                            <td class="actionDateContainer assDate">
                                                                <input type="Date" name="task-dates"  class="insertDeafultDate" readonly>
                                                            </td>
                                                            <td class="assignmentuser">
                                                                <span style="color: red;">*</span>
                                                                <input type="text" name="ActionId" placeholder="The person's name">
                                                            </td>
                                                            <td class="assignmentReason">
                                                                <span style="color: red;">*</span>
                                                                <input type="text" name="ActionId" placeholder="Enter Assigenment Reason">
                                                            </td>
                                                            <td class="assignmentuser2">
                                                                <input type="text" name="ActionId" placeholder="The person's name" value="User Name" readonly>
                                                                <div class="table-icon">
                                                                    <img src="../Images/new.svg" alt="New" onclick="addContent()">
                                                                    <img src="../Images/false.svg" alt="Delete" onclick="deleteAction(this)">
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="icon">
                            <div class="activity-info">
                                <ul>
                                    <li><span><img src="../Images/activity.svg" alt="Logo" class="task-icon"></span></li>
                                    <li><span><img src="../Images/link.svg" alt="Logo" class="task-icon"></span></li>
                                <ul>
                                <div class="submit-form">
                                    <button type="submit" class="new" id="actionButton" onclick="addContent()"> <img src="../Images/new.svg" alt="New"></button>
                                    <button type="submit" class="delete" onclick="addTask()"> <img src="../Images/save.svg" alt="Delete"> </button>
                                    <button type="submit" class="delete" id="subDeleteButton"> <img src="../Images/false.svg" alt="Delete" onclick="confirmDelete(this)"> </button>
                                </div>
                            </div>
                        </div>

                        <div class="activity">                                
                            <div class="activity-info">
                                <table class="commentsTable" id="commentsTable">
                                    <tbody>
                                        <tr>
                                            <td>Ahmed created this task</td>
                                            <td>10/11/2022</td>
                                        </tr>
                                        <tr>
                                            <td>Ahmed created this task</td>
                                            <td>10/11/2022</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="comment">
                                <input type="text" name="comment" placeholder="Write a Comment" class="comment-input" id="commentInput"/>
                                <button type="submit" class="comment-button" id="addCommentBtn">Add</button>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- Mohamed screen-->
                <div class="modal" id="newModal">
                    <div class="modal-content">
                        <div class="modaltab">
                            <div class="modaltabR">
                                <span class="close" onclick="closeNewModal()">&times;</span>
                                <img src="../Images/ellipsis.svg" alt="Logo" class="icon1">
                                <button type="button" class="icon1">Share</button>
                                <img src="../Images/assignp.svg" alt="Logo" class="icon1" onclick="addAssignment()">

                                <h3>Activity</h3>

                                <div class="stage-line">
                                    <h4>Stage-Status <span></span></h4>
                                </div>
                            </div>

                            <div class="modaltabL">
                                <span><input type="text" id="taskName" name="task dates"  placeholder="Task Name"></span>
                            </div>

                        </div><hr style="height:0.5px;border-width:0;color:gray;background-color:#6f6f702e">        
                        
                        <div class="task-info">
                            <div class="task-attachment">

                                <div class="tab-buttons newtabButton">
                                    <div onclick="showTabContent(4)" class="tabdiv active">Task</div>
                                    <div onclick="showTabContent(5)" class="tabdiv">Actions</div>
                                    <div onclick="showTabContent(6)" class="tabdiv">Attachments</div>
                                    <div onclick="showTabContent(7)" class="tabdiv">Assignments</div>
                                </div>

                                <div id="content-4" class="tab-content newtab">
                                    <div> 
                                        <table class="taskInUpDes">
                                            <tr>
                                                <td class="taskFiled">Category</td>
                                                <td>
                                                    <select  class="taskCategory" id="TaskCategory">
        
                                                    </select>
                                                </td>
                                                <td class="Middletask" >Refrence</td>  
                                                <td colspan="3"><input type="text" name="task dates" class="taskin" placeholder="Empty" id="Refrence"></td>
                                            </tr>
                                            <tr>
                                                <td>Stage</td>
                                                <td>
                                                    <select id="TaskStage" name="task Stage" class="TaskStage"></select>
                                                </td>
                                                <td class="Middletask">Status</td>
                                                <td>
                                                    <select class="taskStatus" id ="TaskStatus">
                                                        
                                                    </select>
                                                </td>
                                                <td class="taskbfil">Status Date</td>
                                                <td><input type="Date" id="statusDate"  name="task dates" placeholder="Empty" class="TaskDate" readonly></td>
                                            </tr>
                                            <tr>
                                                <td>Priority</td>
                                                <td>
                                                    <div id="Taskpriority">
                                                        <span class="starr" onclick="setPrioritys(starss, 1)">&#9733;</span>
                                                        <span class="starr" onclick="setPrioritys(starss, 2)">&#9733;</span>
                                                        <span class="starr" onclick="setPrioritys(starss, 3)">&#9733;</span>
                                                        <span class="starr" onclick="setPrioritys(starss, 4)">&#9733;</span>
                                                        <span class="starr" onclick="setPrioritys(starss, 5)">&#9733;</span>
                                                    </div>
                                                </td>
                                                <td class="Middletask">Due Date</td>
                                                <td><input type="Date" name="task dates" class="TaskDate" id = "DueDate" ></td>
                                                <td>Type</td>
                                                <td>
                                                    <select  class="taskType" id ="TaskType">
                                                        
                                                    </select>
                                                </td>
                                            </tr>
                                        </table>       
                                            
                                        <div class="task-description">
                                            <textarea placeholder="Add Description" id="TaskDescription"></textarea>
                                        </div>
                                            <table class="taskInUnderDes">
                                                <tr>
                                                    <td class="taskName">Created By:</td>
                                                    <td><input type="text" name="task dates" class="taskin" id="CreatedBy" readonly></td>
                                                    <td class="taskdate">Creation Date:</td>
                                                    <td class="dateCreation"><input type="date" name="task dates" id="CreationDate" class="TaskDate2" readonly></td>
                                                </tr>

                                                <tr>
                                                    <td class="taskName">Assigned By:</td>
                                                    <td><input type="text" name="task dates" placeholder="Name Display Automatic" id="AssignedBy"class="taskin" readonly></td>
                                                    <td class="taskdate">Assignment Date</td>
                                                    <td><input type="date" name="task dates" class="TaskDate2" id="AssignmentDate" placeholder="Not Assign Yet "readonly></td>
                                                </tr>
                                            </table>
                                    </div>
                                </div>
                                
                                <div id="content-6" class="tab-content newtab">

                                    <div id="upload-section">
                                        <!-- رفع الملف -->
                                        <div class="upload-container" onclick="document.getElementById('fileUploadd').click()">
                                            <p><span>upload</span></p>
                                            <input type="file" id="fileUploadd" multiple />
                                        </div>
                                        <!-- مكان عرض الصور والبيانات -->
                                        <div id="attachments-containerr">

                                            <!-- نافذة التكبير -->
                                            <div id="imageModall" class="image-modal" onclick="closeImagee()">
                                                <span class="close-modal" onclick="closeImagee()">&times;</span>
                                                <img id="modalImagee" class="image-modal-content" src="">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="content-5" class="tab-content newtab">
                                    <div class="checklist-container">

                                        <!-- Checklist box -->
                                        <div class="checklist-box" id="checklist1">
                                            <div class="checklist-header">
                                                <table class="actionTableHeader">
                                                    <tr>
                                                        <td class="actionCheckHeader">Id</td>
                                                        <td class="actionNameHeader">Name</td>
                                                        <td class="actionStatusHeader">Status</td>
                                                        <td>Status Date</td>
                                                    </tr>
                                                </table>
                                                
                                            </div>
                                            <div id="getchecklistItemaction" class="checklist-items DeafultTable"></div>
                                        </div>
                                    </div>
                                </div>

                                <div id="content-7" class="tab-content newtab">
                                    <div class="checklist-container">
                                        <!-- Checklist box -->
                                        <div class="checklist-box" id="assignment1">
                                            <div class="checklist-header">
                                                <table class="actionTableHeader">
                                                    <tr>
                                                        <td class="AssignmentID">Id</td>
                                                        <td class="AssignmentInfo">Assigned Date</td>
                                                        <td class="AssignmentTo-By">Assigned To</td>
                                                        <td class="AssignmentReson">Assigned Reason</td>
                                                        <td>Assigned By</td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div id="getchecklistItem" class="checklist-items"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="icon">
                            <div class="activity-info">
                                <ul>
                                    <li><span><img src="../Images/activity.svg" alt="Logo" class="task-icon"></span></li>
                                    <li><span><img src="../Images/link.svg" alt="Logo" class="task-icon"></span></li>
                                <ul>
                                <div class="submit-form">
                                    <button type="submit" class="new" id="actionButton" onclick="addContent()"> <img src="../Images/new.svg" alt="New"></button>
                                    <button type="submit" class="delete" id="saveChanges" onclick="UpdateTask()"> <img src="../Images/save.svg" alt="Delete"> </button>
                                    <button type="submit" class="delete" id="deleteTask" onclick="confirmDelete(this)"> <img src="../Images/false.svg" alt="Delete" onclick="confirmDelete(this)"> </button>
                                </div>
                                <div class="task-slider">
                                    <input type="text" name="comment" placeholder="taskID" id="GetTaskId" />
                                </div>
                            </div>
                        </div>

                        <div class="activity">                                
                            <div class="activity-info">
                                <table class="commentsTable" id="commentsTablee">
                                    <tbody>
                                        <tr>
                                            <td>Ahmed created this task</td>
                                            <td>10/11/2022</td>
                                        </tr>
                                        <tr>
                                            <td>Ahmed created this task</td>
                                            <td>10/11/2022</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="comment">
                                <input type="text" name="comment" placeholder="Write a Comment" class="comment-input" id="commentInputt"/>
                                <button type="submit" class="comment-button" id="addCommentBtnn">Add</button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="my-icon">
                <img src="../Images/todolist.jpg" alt="Logo">
                <img src="../Images/report.jpg" alt="Logo">
                <img src="../Images/document.jpg" alt="Logo">
                <img src="../Images/calender.jpg" alt="Logo">
                <img src="../Images/risk.jpg" alt="Logo">
            </div>
            <div class="submit-form">
                <button type="submit" class="new" id="myButton" onclick='openModal()'> <img src="../Images/new.svg" alt="New"> </button>
                <button type="submit" class="delete" id="deleteButton" onclick="confirmDelete(this)"> <img src="../Images/false.svg" alt="Delete"> </button>
            </div>
        </div>
        <div class="footer-message" id="footerMessage">
            This message appears for 30 seconds and then disappears.
        </div>
    </div>
    <div id="customAlert" class="alert-overlay">
        <div class="alert-box">
            <p id="alertMessage">This is a custom alert message.</p>
            <button onclick="closeAlert()">OK</button>
        </div>
    </div>

    <script src="tasks.js"></script>
    <script src="GetData.js"></script>
    <script src="DeleteData.js"></script>
    <script src="UpdateData.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
