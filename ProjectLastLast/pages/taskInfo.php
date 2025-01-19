<div class="modal" id="myModal">
                    <div class="modal-content">
                        <div class="modaltab">
                            <div class="modaltabR">
                                <span class="close" onclick="closeModal()">&times;</span>                                
                                <img src="star.svg" alt="Logo" class="icon1">
                                <img src="ellipsis.svg" alt="Logo" class="icon1">
                                <button type="button" class="icon1">Share</button>
                                <img src="assignp.svg" alt="Logo" class="icon1">
                                <span class="icon1">Created on Oct 27</span>
                                <h3>Activity</h3>

                                <div class="stage-line">
                                    <div id="not-assign" class="stage active"></div>
                                    <div id="assign" class="stage"></div>
                                    <div id="in-progress" class="stage"></div>
                                    <div id="under-review" class="stage"></div>
                                    <div id="completed" class="stage"></div>
                                </div>
                            </div>

                            <div class="modaltabL">
                                <span><h3>Create To Do List</h3></span>                                
                            </div>
                        </div><hr style="height:0.5px;border-width:0;color:gray;background-color:#6f6f702e">        
                        
                        <div class="task-info">
                            <div class="task-attachment">

                                <div class="tab-buttons">
                                    <div onclick="showTabContent(0)" class="tabdiv active">Task</div>
                                    <div onclick="showTabContent(1)" class="tabdiv">Actions</div>
                                    <div onclick="showTabContent(2)" class="tabdiv">Attachments</div>
                                    <div onclick="showTabContent(3)" class="tabdiv">Assignments</div>
                                    <script>
                                        // المراحل المتاحة
                                        const stages = ['not-assign', 'assign', 'in-progress', 'under-review', 'completed'];

                                        // دالة لتحديث المرحلة الفعالة
                                        function activateStage(stageId) {
                                            // إزالة استايل الفعالية من جميع المراحل
                                            stages.forEach(stage => {
                                                document.getElementById(stage).classList.remove('active');
                                            });
                                            // تفعيل المرحلة المحددة
                                            document.getElementById(stageId).classList.add('active');
                                        }

                                        // إضافة مستمعات للنقر على المراحل لتحديثها
                                        stages.forEach(stage => {
                                            document.getElementById(stage).addEventListener('click', function() {
                                                activateStage(stage);
                                            });
                                        });
                                    </script>
                                </div>

                                <div id="content-0" class="tab-content active">
                                    <div> 
                                        <table>
                                            <tr>
                                                <td>Category</td>
                                                <td><input type="text" name="task dates" class="TaskCategoryId" placeholder="Empty"></td>
                                                <td>Refrence</td>
                                                <td colspan="3"><input type="text" name="task dates" class="TaskCategoryId" placeholder="Empty"></td>
                                            </tr>
                                            <tr>
                                                <td>Stage</td>
                                                <td><input type="text" name="task dates" class="TaskCategoryId" placeholder="Empty"></td>
                                                <td>Status</td>
                                                <td><input type="text" name="task dates" class="TaskCategoryId" placeholder="Empty"></td>
                                                <td>Status Date</td>
                                                <td><input type="Date" name="task dates" class="TaskCategoryId" placeholder="Empty"></td>
                                            </tr>
                                            <tr>
                                                <td>Priority</td>
                                                <td>
                                                    <div id="priority">
                                                        <span class="star" onclick="setPriority(stars, 1)">&#9733;</span>
                                                        <span class="star" onclick="setPriority(stars, 2)">&#9733;</span>
                                                        <span class="star" onclick="setPriority(stars, 3)">&#9733;</span>
                                                        <span class="star" onclick="setPriority(stars, 4)">&#9733;</span>
                                                        <span class="star" onclick="setPriority(stars, 5)">&#9733;</span>
                                                    </div>
                                                </td>
                                                <td>Due Date</td>
                                                <td><input type="Date" name="task dates" class="TaskCategoryId" placeholder="Empty"></td>
                                                <td>Type</td>
                                                <td><input type="text" name="task dates" class="TaskCategoryId" placeholder="Empty"></td>
                                            </tr>
                                        </table>                                   
                                        <div class="info">
                                            <div class="task-status">
                                                <label> Category </label>
                                                <input type="text" name="task dates" class="TaskCategoryId" placeholder="Empty">
                                            </div>

                                            <div class="task-status">
                                                <label> Stage </label>
                                                <input type="text" name="task dates" class="TaskCategoryId" placeholder="Empty">
                                            </div>

                                        </div>

                                        <div class="info inf2">
                                            <div class="task-status">
                                                <label> Refrence </label>
                                                <input type="text" name="task dates">
                                            </div>

                                            <div class="task-dates">
                                                <label> Due Date</label>
                                                <input type="date" name="task dates">
                                            </div>

                                            <script>
                                                function setPriority(stars, level) {
                                                    stars.forEach((star, index) => {
                                                    star.classList.toggle('selected', index < level);
                                                    });
                                                }
                                            </script>

                                            <div class="task-dates">
                                                <label> Priority</label>
                                                <div id="priority">
                                                    <span class="star" onclick="setPriority(stars, 1)">&#9733;</span>
                                                    <span class="star" onclick="setPriority(stars, 2)">&#9733;</span>
                                                    <span class="star" onclick="setPriority(stars, 3)">&#9733;</span>
                                                    <span class="star" onclick="setPriority(stars, 4)">&#9733;</span>
                                                    <span class="star" onclick="setPriority(stars, 5)">&#9733;</span>
                                                </div>
                                                <script>
                                            const stars = document.querySelectorAll('.star');
                                        </script>
                                                <input type="text" name="task dates">
                                            </div>
                                        </div>

                                        <div class="info info3">
                                            <div class="task-type ty">
                                                <label>Stage</label>
                                                <input type="text" name="task Assignees" value="Empty">
                                            </div>

                                            <div class="task-Assignees ta">
                                                <label>Status</label>
                                                <input type="text" name="task Assignees" value="Empty">
                                            </div>

                                            <div class="task-Assignees ta">
                                                <label>Status Date</label>
                                                <input type="date" name="task Assignees" value="Empty">
                                            </div>
                                        </div>

                                        <div class="task-description">
                                            <textarea placeholder="Add Description"></textarea>
                                        </div>

                                        <div class="info">
                                            <div class="task-status des">
                                                <label> Created By:</label>
                                                <input type="text" name="task dates" placeholder="Name">
                                            </div>

                                            <div class="task-status des">
                                                <label> Assigned By:</label>
                                                <input type="text" name="task dates" placeholder="Name">
                                            </div>
                                        </div>

                                        <div class="info inf2">
                                            <div class="task-dates">
                                                <label>Creation Date</label>
                                                <input type="date" name="task dates" class="des-date">
                                            </div>

                                            <div class="task-dates">
                                                <label>Assignment Date</label>
                                                <input type="date" name="task dates" class="des-date">
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div id="content-2" class="tab-content">
                                    <div class="upload-container" onclick="document.getElementById('file-input').click()">
                                        <p>Drop your files here to <span>upload</span></p>
                                        <input type="file" id="file-input" multiple>
                                    </div>
                                    <div class="preview">
                                        <div class="img-details" id="preview"></div>
                                    </div>
                                    <script>
                                        const fileInput = document.getElementById('file-input');
                                        const preview = document.getElementById('preview');

                                        fileInput.addEventListener('change', () => {
                                            Array.from(fileInput.files).forEach(file => {
                                            const reader = new FileReader();
                                                reader.onload = (e) => {
                                                    const img = document.createElement('img');
                                                    img.src = e.target.result;
                                                    preview.appendChild(img);
                                                };
                                                reader.readAsDataURL(file);
                                            });
                                        });
                                    </script>
                                </div>

                                <div id="content-1" class="tab-content">
                                    <div class="checklist-container">

                                        <!-- Checklist box -->
                                        <div class="checklist-box" id="checklist1">
                                            <div class="checklist-header">
                                                <span class="check">CheckList (<span class="item-count">0</span>) <span>
                                            </div>
                                            <div class="checklist-items"></div>
                                            <div class="checklist-footer" onclick="addChecklistItem('checklist1')">+ New CheckList Item</div>
                                        </div>

                                        <!-- Completed section -->
                                        <div class="checklist-box" id="checklist2">
                                            <div class="checklist-header">
                                                <span class="check">CheckList (<span class="item-count">0</span>) <span>
                                            </div>
                                            <div class="checklist-items"></div>
                                            <div class="checklist-footer" onclick="addChecklistItem('checklist2')">+ New CheckList Item</div>
                                        </div>
                                    </div>

                                    <script>
                                        function addChecklistItem(checklistId) {
                                            // الحصول على العنصر الذي يحتوي على العناصر الفرعية
                                            const checklistBox = document.getElementById(checklistId);
                                            const checklistItems = checklistBox.querySelector('.checklist-items');
                                            const itemCountElement = checklistBox.querySelector('.item-count');

                                            // إنشاء عنصر تحقق جديد مع مربع نص قابل للتحرير
                                            const newItem = document.createElement("div");
                                            newItem.className = "checklist-item";
                                            newItem.innerHTML = `
                                                <input type="checkbox" class="checkbox" onclick="completeTask(this, '${checklistId}')">
                                                <input type="text" class="editable-text" value="New CheckList Item" onfocus="this.select()">
                                            `;

                                            // إضافة العنصر الجديد إلى القائمة
                                            checklistItems.appendChild(newItem);

                                            // تحديث عدد العناصر في العنوان
                                            const itemCount = checklistItems.querySelectorAll('.checklist-item:not(.hidden)').length;
                                            itemCountElement.textContent = itemCount;
                                        }

                                        function completeTask(checkbox, checklistId) {
                                            const checklistBox = document.getElementById(checklistId);
                                            const checklistItems = checklistBox.querySelector('.checklist-items');
                                            const itemCountElement = checklistBox.querySelector('.item-count');

                                            // إخفاء العنصر المحدد
                                            const checklistItem = checkbox.parentElement;
                                            checklistItem.classList.add('hidden');

                                            // تحديث العدد في العنوان
                                            const itemCount = checklistItems.querySelectorAll('.checklist-item:not(.hidden)').length;
                                            itemCountElement.textContent = itemCount;
                                        }
                                    </script>
                                </div>

                                <script>
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
                                </script>

                                <div id="content-3" class="tab-content">
                                    <div>                                    
                                        <div class="info">
                                            <div class="task-status">
                                                <label> Status </label>
                                                <select name="task status" class="infostatus">
                                                    <option value="Empty">Empty</option>
                                                    <option value="Active"> Active </option>
                                                    <option value="Holding"> Holding </option>
                                                    <option value="Archive"> Archive </option>
                                                    <option value="Cancelled"> Cancelled </option>
                                                    <option value="Rejected"> Rejected </option>
                                                </select>
                                            </div>

                                            <div class="task-status">
                                                <label> Stage </label>
                                                <select name="task status" class="infostatus">
                                                    <option value="Empty">Empty</option>
                                                    <option value="Not-Assign"> Not-Assign </option>
                                                    <option value="Assign"> Assign </option>
                                                    <option value="In-Progress"> In-Progress </option>
                                                    <option value="Under-Review"> Under-Review </option>
                                                    <option value="Completed"> Completed </option>
                                                </select>
                                            </div>

                                            <div class="task-type">
                                                <label> Type </label>
                                                <select name="task type" class="infotype">
                                                    <option value="Empty">Empty</option>
                                                    <option value="Personal"> Personal </option>
                                                    <option value="Work"> Work </option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="info inf2">
                                            <div class="task-dates">
                                                <label>Due Dates</label>
                                                <input type="date" name="task dates">
                                            </div>

                                            <div class="task-dates">
                                                <label>Status Dates</label>
                                                <input type="date" name="task dates">
                                            </div>
                                        </div>

                                        <div class="info inf2">
                                            <div class="task-type ty">
                                                <label>Create by</label>
                                                <input type="email" name="task Assignees" value="Empty">
                                            </div>

                                            <div class="task-Assignees ta">
                                                <label>Assign by</label>
                                                <input type="email" name="task Assignees" value="Empty">
                                            </div>
                                        </div>

                                        <div class="task-description">
                                            <textarea placeholder="Add Description"></textarea>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="icon">
                                <div class="activity-info">
                                    <ul>
                                        <li><span><img src="activity.svg" alt="Logo" class="task-icon"></span></li>
                                        <li><span><img src="link.svg" alt="Logo" class="task-icon"></span></li>
                                    <ul>
                                    <div class="submit-form">
                                        <button type="submit" class="new"  onclick='openModal()'> <img src="new.svg" alt="New"></button>
                                        <button type="submit" class="delete"> <img src="save.svg" alt="Delete"> </button>
                                        <button type="submit" class="delete"> <img src="false.svg" alt="Delete"> </button>
                                    </div>
                                    <div class="task-slider">
                                        <img src="arrowup.svg" alt="Logo" class="icon2">
                                        <img src="arrowdown.svg" alt="Logo" class="icon2">
                                    </div>
                                </div>
                        </div>

                        <div class="activity">
                            <div class="activity-info">
                                <ul>
                                    <li>
                                        <span>Ahmed created this task</span> 
                                        <span class="date">10/11/2022</span>
                                    </li><br>
                                    <li>
                                        <span>Ahmed created this task</span> 
                                        <span class="date">10/11/2022</span>
                                    </li><br>
                                    <li>
                                        <span>Ahmed created this task</span> 
                                        <span class="date">10/11/2022</span>
                                    </li><br>
                                    <li>
                                        <span>Ahmed created this task</span> 
                                        <span class="date">10/11/2022</span>
                                    </li><br>
                                    <li>
                                        <span>Ahmed created this task</span> 
                                        <span class="date">10/11/2022</span>
                                    </li><br>
                                </ul>
                                <div class="comment">
                                    <input type="text" name="comment" placeholder="Write a Comment" class="comment-input"/>
                                    <button type="submit" class="comment-button" disabled>Send</button>
                                    <script>
                                        // تفعيل أو تعطيل زر الإرسال بناءً على محتوى حقل الإدخال
                                        const inputField = document.querySelector('.comment-input');
                                        const sendButton = document.querySelector('.comment-button');

                                        inputField.addEventListener('input', () => {
                                            sendButton.disabled = inputField.value.trim() === '';
                                        });
                                    </script>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function openModal() {
                        document.getElementById("myModal").style.display = "flex";
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
                </script>
                </div>