<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "risk_management";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add a new risk
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_risk'])) {
    $scenario = $_POST['scenario'];
    $source = $_POST['source'];
    $threats = $_POST['threats'];
    $vulnerabilities = $_POST['vulnerabilities'];
    $type = $_POST['type'];
    $function = $_POST['function'];
    $site = $_POST['site'];
    $initiative = $_POST['initiative'];
    $initiative_level = $_POST['initiative_level'];
    $status = $_POST['status'];
    $status_date = $_POST['status_date'];
    $reported_by = $_POST['reported_by'];
    $reporting_date = $_POST['reporting_date'];

    $sql = "INSERT INTO risks (scenario, source, threats, vulnerabilities, type, function, site, initiative, initiative_level, status, status_date, reported_by, reporting_date)
            VALUES ('$scenario', '$source', '$threats', '$vulnerabilities', '$type', '$function', '$site', '$initiative', '$initiative_level', '$status', '$status_date', '$reported_by', '$reporting_date')";

    if ($conn->query($sql) === TRUE) {
        echo "";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Delete a risk
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM risks WHERE id=$id";
    $conn->query($sql);
}

// Fetch risks to display
$sql = "SELECT * FROM risks";
$result = $conn->query($sql);

// التحقق من اللغة المختارة في الرابط أو الجلسة
if (isset($_GET['lang'])) {
    $language = $_GET['lang'];
    $_SESSION['lang'] = $language;
  }elseif (isset($_SESSION['lang'])) {
    $language = $_SESSION['lang'];
  }else {
    $language = 'english'; // اللغة الافتراضية
  }

// تحميل ملف اللغة المناسب
// $lang = include("lang/{$english}.php");
$lang = include("{$language}.php");
?>

<!DOCTYPE html>
<html lang="<?php echo $language; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Risk Management</title>
    <link rel="stylesheet" href="main.css">
    <style>
        body {
            direction: <?php echo $language == 'ar' ? 'rtl' : 'ltr'; ?>;
            text-align: <?php echo $language == 'ar' ? 'right' : 'left'; ?>;
        }
    </style>
</head>
<body>
    <header>
        <img src="logo.jpeg" alt="Logo" class="logo">
        <nav class="header-tabs">
            <ul>
                <li><a href="#"> <?php echo $lang['Register']; ?> </a> </li> 
                <li><a href="#"> <?php echo $lang['Evaluation']; ?> </a> </li>
                <li><a href="#"> <?php echo $lang['Reports']; ?> </a> </li>
                <li><a href="#"> <?php echo $lang['myRisks']; ?> </a> </li>
                <li><a href="#"> <?php echo $lang['myActions']; ?> </a> </li>
            </ul>
            <div class="dropdown">
                <img src="bar.png" alt="Logo" class="bar">
                <div class="dropdown-content">
                    <ul class="dropdown-content-li">
                        <li><a href="#"> <?php echo $lang['Performance']; ?> </a></li>
                        <li><a href="#"> <?php echo $lang['Compliance']; ?> </a></li>
                        <li><a href="#"> <?php echo $lang['Assets']; ?> </a></li>
                        <li><a href="#"> <?php echo $lang['Crisis']; ?> </a></li>
                        <li><a href="#"> <?php echo $lang['Risk']; ?> </a></li>
                        <li><a href="#"> <?php echo $lang['BCM']; ?> </a></li>
                    </ul>
                </div>
            </div>
            <img src="notification.svg" alt="Logo" class="not">
        </nav>
        <div class="dropdown-user">
            <img src="user.svg" alt="Logo" class="arrow">
            <div class="dropdown-content-user">
                <ul class="dropdown-content-li">
                    <li> <a href="#"> <?php echo $lang['Notification']; ?> </a></li>
                    <li> <a href="#"> <?php echo $lang['Setting']; ?>  </a></li>
                    <li> <a href="#"> <?php echo $lang['SignOut']; ?> </a></li>
                    <li class="lang"> <a href="#"> <?php echo $lang['language']; ?> </a>
                        <ul class="lang-sett">
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
            <div class="serachcon">
                <div class="search-bar">
                    <form method="GET" action="">
                        <img src="search.svg" alt="Logo" class="serach">
                        <input type="text" name="search_id" placeholder="Search" />
                        <img src="sliders.svg" alt="Logo" class="slider">
                    </form>
                </div>
            </div>
            <div class="searchcontent">
                    <div id="dynamicDiv_id"> ID </div>
                    <div id="dynamicDiv_senario"> Senario </div>
            </div>
            <div class="container filter">
                <!-- Grouping Selector -->
                <div class="grouping-bar">
                    <label for="groupSelect">Group by:</label>
                    <select id="groupSelect">
                        <option value="default">No Grouping</option>
                        <option value="sector">Sector</option>
                        <option value="status">Status</option>
                    </select>
                </div>
                <!-- Sorting Selector -->
                <div class="sorting-bar">
                    <label for="sortSelect">Sort by:</label>
                    <select id="sortSelect">
                        <option value="default">No Sorting</option>
                        <option value="alphabetical">Alphabetical (A-Z)</option>
                        <option value="reverse">Alphabetical (Z-A)</option>
                    </select>
                </div>
                <!-- View Selector -->
                <div class="view-bar">
                    <label for="viewSelect">View:</label>
                    <select id="viewSelect">
                        <option value="default">select</option>
                        <option value="tree">Tree</option>
                        <option value="table">Table</option>
                    </select>
                </div>
                <!-- Tree View Container -->
                <div class="tree-container" id="treeContainer">
                    <div id="treeView"></div>
                </div>

                <!-- Table View Container -->
                <div class="table-container" id="tableContainer">
                    <table id="dataTable">
                        <thead>
                            <tr>
                                <th>Project</th>
                                <th>Sector</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.12/jstree.min.js"></script>
            <script>
                var treeData = [
                    { "text": "Project A", "sector": "Corporate", "status": "New" },
                    { "text": "Project B", "sector": "Corporate", "status": "Waiting Approval" },
                    { "text": "Project C", "sector": "Sector", "status": "Implementation" },
                    { "text": "Project D", "sector": "Sector", "status": "Archived" }
                ];

                function groupTreeData(data, groupBy) {
                    if (groupBy === 'default') return data;

                    var groupedData = {};
                    data.forEach(function(item) {
                        var groupValue = item[groupBy];
                        if (!groupedData[groupValue]) {
                            groupedData[groupValue] = [];
                        }
                        groupedData[groupValue].push(item);
                    });

                    var result = [];
                    for (var key in groupedData) {
                        result.push({
                            "text": key,
                            "state": { "opened": true },
                            "children": groupedData[key].map(function(item) {
                                return { "text": item.text };
                            })
                        });
                    }

                    return result;
                }

                function sortTreeData(data, sortBy) {
                    if (sortBy === 'default') return data;

                    data.forEach(function(group) {
                        if (group.children) {
                            group.children.sort(function(a, b) {
                                if (sortBy === 'alphabetical') {
                                    return a.text.localeCompare(b.text); // A-Z
                                } else if (sortBy === 'reverse') {
                                    return b.text.localeCompare(a.text); // Z-A
                                }
                            });
                        }
                    });

                    return data;
                }

                function loadTree(groupBy = 'default', sortBy = 'default') {
                    var groupedData = groupTreeData(treeData, groupBy);
                    var sortedData = sortTreeData(groupedData, sortBy);

                    // Destroy the current tree view before loading a new one
                    $('#treeView').jstree("destroy").empty();

                    // Initialize jstree with grouped and sorted data
                    $('#treeView').jstree({
                        'core': {
                            'data': sortedData
                        }
                    });
                }

                function loadTable() {
                    var tableBody = $('#dataTable tbody');
                    tableBody.empty();

                    treeData.forEach(function(item) {
                        var row = `<tr>
                            <td>${item.text}</td>
                            <td>${item.sector}</td>
                            <td>${item.status}</td>
                        </tr>`;
                        tableBody.append(row);
                    });
                }

                $(document).ready(function() {
                    // Load initial tree view
                    loadTree();

                    // Handle search functionality
                    $('#searchInput').on('keyup', function() {
                        var searchString = $(this).val();
                        $('#treeView').jstree('search', searchString);
                    });

                    // Handle view switching between Tree and Table
                    $('#viewSelect').on('change', function() {
                        var view = $(this).val();
                        if (view === 'tree') {
                                    $('#treeContainer').show();
                                    $('#tableContainer').hide();
                                    loadTree($('#groupSelect').val(), $('#sortSelect').val());
                                } else {
                                    $('#treeContainer').hide();
                                    $('#tableContainer').show();
                                    loadTable();
                                }
                            });

                    // Handle grouping functionality
                    $('#groupSelect').on('change', function() {
                        var groupBy = $(this).val();
                        var sortBy = $('#sortSelect').val();
                        if ($('#viewSelect').val() === 'tree') {
                            loadTree(groupBy, sortBy);
                        }
                    });

                    // Handle sorting functionality
                    $('#sortSelect').on('change', function() {
                        var sortBy = $(this).val();
                        var groupBy = $('#groupSelect').val();
                        if ($('#viewSelect').val() === 'tree') {
                            loadTree(groupBy, sortBy);
                        }
                    });
                });
            </script>
        </div>

        <!-- main container 2 -->
        <?php 
            $search_id = isset($_GET['search_id']) ? $_GET['search_id'] : '';
            $sql = "SELECT id,scenario,status FROM risks";

            if ($search_id) {
                $sql .= " WHERE id = $search_id";
                }
            $result = $conn->query($sql);
            ?>
        <script>
            function updateDiv(id, scenario) {
                var dynamicDiv_id = document.getElementById("dynamicDiv_id");
                var dynamicDiv_scenario = document.getElementById("dynamicDiv_senario");

                dynamicDiv_id.innerHTML = id;
                dynamicDiv_scenario.innerHTML = scenario;
            }
        </script>
          
        <div class="main-content2">
            <div class="table-header">
                <nav class="header-content header1"><?php echo $lang['ID']; ?></nav>
                <nav class="header-content header2"><?php echo $lang['Risk Scenario']; ?></nav>
            </div>
            <div class="table">
                <table>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td class='id'>
                                        <a href='#' onclick=\"updateDiv('" . $row['id'] . "', '" . $row['scenario'] . "')\">" . $row['id'] . "</a>
                                    </td>";
                                echo "<td class='scenario'>" 
                                    . $row['scenario'] .
                                    "<button type='submit' class='table-icon'> <img src='recycle-bin.png' alt='Save'>  </button>" . 
                                    "<button type='submit' class='table-icon'> <img src='archive.svg' alt='Save'>  </button>" . 
                                "</td>";
                            }         
                        } else {
                            echo "<tr><td colspan='4'>No risks found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table> 
            </div>
        </div>

        <div id="Risk" class="tabcontent">
            <div class="tab_fixed">
                <button class="tablinks" onclick="openTab(event, 'Risk')" id="defaultOpen"> <?php echo $lang['Risk']; ?> </button>
                <button class="tablinks" onclick="openTab(event, 'Score')"> <?php echo $lang['Score']; ?> </button>
                <button class="tablinks" onclick="openTab(event, 'Strategy')"> <?php echo $lang['Strategy']; ?> </button>
                <button class="tablinks" onclick="openTab(event, 'Treatment')"> <?php echo $lang['Treatment']; ?> </button>
                <button class="tablinks" onclick="openTab(event, 'Status')"> <?php echo $lang['Status']; ?> </button>
            </div>
            <div class="main-content">
                <form action="" method="POST" class="">
                
                    <input type="hidden" name="add_risk" value="1">
                    <label> <?php echo $lang['Scenario']; ?> </label>
                    <input class="text_area" type="text" name="scenario" require><br>

                    <label> <?php echo $lang['Threats']; ?> </label>
                    <input class="text"type="text" name="threats"><br>

                    <label> <?php echo $lang['Vulnerabilities']; ?> </label>
                    <input class="text" type="text" name="vulnerabilities"><br>

                    <label> <?php echo $lang['Existing Controls']; ?> </label>
                    <input class="text" type="text" name="existing-Controls"><br>

                    <label> <?php echo $lang['Initiative']; ?> </label>
                    <input class="text" type="text" name="initiative"><br>

                    <label> <?php echo $lang['Source']; ?> </label>
                    <input class="text" type="text" name="source"><br>

                    <label> <?php echo $lang['Type']; ?> </label>
                    <input class="text" type="text" name="type"><br>

                    <label> <?php echo $lang['Function']; ?> </label>
                    <input class="text" type="text" name="function"><br>

                    <label> <?php echo $lang['Site']; ?> </label>
                    <input class="text" type="text" name="site"><br>

                    <label> <?php echo $lang['Reported By']; ?> </label>
                    <input class="text" type="text" name="reported_by"><br>

                    <label> <?php echo $lang['Initiative Level']; ?> </label>
                    <select name="initiative_level" class="multiselect">
                        <option value="Project"> <?php echo $lang['Project']; ?> </option>
                        <option value="Program"> <?php echo $lang['Program']; ?> </option>
                        <option value="Portfolio"> <?php echo $lang['Portfolio']; ?> </option>
                    </select>

                    <label class="select-option"> <?php echo $lang['Status']; ?></label>
                    <select name="status" class="multiselect1">
                        <option value="Open"> <?php echo $lang['Open']; ?> </option>
                        <option value="Closed"> <?php echo $lang['Closed']; ?> </option>
                        <option value="In Progress"> <?php echo $lang['In Progress']; ?> </option>
                    </select>

                    <label class="multiselect mul"> <?php echo $lang['Status Date']; ?> </label>
                    <input type="date" name="status_date" class="select-date">

                    <label class="reportdate"> <?php echo $lang['Reporting Date']; ?> </label>
                    <input type="date" name="reporting_date" class="report-input"><br>
                </form>
                <div class="my-icon">
                    <img src="todolist.jpg" alt="Logo">
                    <img src="report.jpg" alt="Logo">
                    <img src="document.jpg" alt="Logo">
                    <img src="calender.jpg" alt="Logo">
                    <img src="risk.jpg" alt="Logo">
                </div>
                <div class="submit-form">
                    <button type="submit" class="new"> <img src="new.svg" alt="New"> </button>
                    <button type="submit" class="save"> <img src="save.svg" alt="Save">  </button>
                    <button class="delete" type="submit"> <img src="false.svg" alt="Delete"> </button>
                </div>
            </div>
        </div>

        <script type="text/javascript">
            function addFields_score() {
                var container = document.getElementById("fieldContainer");
                var div = document.createElement("div");
                div.innerHTML = `
                <div class="section" id="section">
                    <label>
                        <input type="checkbox" id="check-delete" name="check-delete" value="check-delete" class="check-delete"  onclick="changeContainerBackground(this)">
                        <?php echo $lang['Reputation']; ?> 
                    </label>
                    <input class="text" type="text" name="Reputation"><br>

                    <label> <?php echo $lang['Compliance']; ?> </label>
                    <input class="text" type="text" name="Compliance"><br>

                    <label> <?php echo $lang['Customers']; ?> </label>
                    <input class="text" type="text" name="Customers"><br>

                    <label> <?php echo $lang['Financial']; ?> </label>
                    <input class="text" type="text" name="Financial"><br>

                    <label> <?php echo $lang['HSSE']; ?> </label>
                    <input class="text" type="text" name="HSSE"><br>

                    <label> <?php echo $lang['impact score']; ?> </label>
                    <input class="text" type="text" name="impact-score"><br>

                    <label> <?php echo $lang['Analysis Team']; ?> </label>
                    <input class="text" type="text" name="Analysis-team"><br>

                    <label> <?php echo $lang['Analysis Date']; ?> </label>
                    <input type="date" name="Analysis Date" class="select-date-score"><br> <hr>
                </div>                
                `;
                container.appendChild(div);
            }
        </script>

        <div id="Score" class="tabcontent">
            <div class="tab_fixed">
                <button class="tablinks" onclick="openTab(event, 'Risk')" id="defaultOpen"> <?php echo $lang['Risk']; ?> </button>
                <button class="tablinks" onclick="openTab(event, 'Score')"> <?php echo $lang['Score']; ?> </button>
                <button class="tablinks" onclick="openTab(event, 'Strategy')"> <?php echo $lang['Strategy']; ?> </button>
                <button class="tablinks" onclick="openTab(event, 'Treatment')"> <?php echo $lang['Treatment']; ?> </button>
                <button class="tablinks" onclick="openTab(event, 'Status')"> <?php echo $lang['Status']; ?> </button>
            </div>
            <div class="main-content">
                <form method="POST">
                    <div id = "fieldContainer">
                        <div class="section" id="section">
                            <label>
                                <input type="checkbox" id="check-delete" name="check-delete" value="check-delete" class="check-delete"  onclick="changeContainerBackground(this)">
                                <?php echo $lang['Reputation']; ?>
                            </label>
                            <input class="text" type="text" name="Reputation1" id="Reputation1"><br>

                            <label> <?php echo $lang['Compliance']; ?> </label>
                            <input class="text" type="text" name="Compliance1" id="Compliance1"><br>

                            <label> <?php echo $lang['Customers']; ?> </label>
                            <input class="text" type="text" name="Customers1" id="Customers1"><br>

                            <label> <?php echo $lang['Financial']; ?> </label>
                            <input class="text" type="text" name="Financial1" id="Financial1"><br>

                            <label> <?php echo $lang['HSSE']; ?> </label>
                            <input class="text" type="text" name="HSSE1" id="HSSE1"><br>

                            <label> <?php echo $lang['impact score']; ?> </label>
                            <input class="text" type="text" name="impact-score1" id="impact-score1"><br>

                            <label> <?php echo $lang['Analysis Team']; ?> </label>
                            <input class="text" type="text" name="Analysis-team1" id="Analysis-team1"><br>

                            <label> <?php echo $lang['Analysis Date']; ?> </label>
                            <input type="date" name="Analysis Date1" class="select-date-score" id="Analysis Date1"><br><hr>
                        </div>
                    </div>
                </form>
                <div class="my-icon">
                    <img src="todolist.jpg" alt="Logo">
                    <img src="report.jpg" alt="Logo">
                    <img src="document.jpg" alt="Logo">
                    <img src="calender.jpg" alt="Logo">
                    <img src="risk.jpg" alt="Logo">
                </div>
                <div class="submit-form">
                    <button type="submit" onclick="addFields_score()" class="new"> <img src="new.svg" alt="New"> </button>
                    <button type="submit" class="save"> <img src="save.svg" alt="Save">  </button>
                    <button class="delete" type="submit" onclick="myFunction(this)"> <img src="false.svg" alt="Delete"> </button>
                </div>
            </div>
        </div>

        <script type="text/javascript">
            function addField() {
                var container = document.getElementById("strategyContainer");
                var div = document.createElement("div");
                div.innerHTML = `
                <div class="section" id="section">
                    <label>
                        <input type="checkbox" id="check-delete" name="check-delete" value="check-delete" class="check-delete"  onclick="changeContainerBackground(this)">
                        <?php echo $lang['Strategy']; ?>
                    </label>
                    <input class="text_area" type="text" name="Strategy"><br>

                    <label> <?php echo $lang['Control']; ?> </label>
                    <input class="text" type="text" name="Control"><br>

                    <label> <?php echo $lang['Approved budget']; ?> </label>
                    <input class="text" type="text" name="Approved-budget"><br>

                    <label> <?php echo $lang['Owner']; ?> </label>
                    <input class="text" type="text" name="Owner"><br>

                    <label> <?php echo $lang['Required RT date']; ?> </label>
                    <input type="date" name="Required RT date" class="select-date-stategy1">

                    <label class="strategylabel"> <?php echo $lang['Approval Date']; ?> </label>
                    <input type="date" name="Approval Date" class="select-date-stategy">

                    <label class="strategylabel"> <?php echo $lang['Assignment Date']; ?> </label>
                    <input type="date" name="Assignment Date" class="select-date"><br><hr>
                </div>
                `;
                container.appendChild(div);
            }
        </script>

        <div id="Strategy" class="tabcontent">
            <div class="tab_fixed">
                <button class="tablinks" onclick="openTab(event, 'Risk')" id="defaultOpen"> <?php echo $lang['Risk']; ?> </button>
                <button class="tablinks" onclick="openTab(event, 'Score')"> <?php echo $lang['Score']; ?> </button>
                <button class="tablinks" onclick="openTab(event, 'Strategy')"> <?php echo $lang['Strategy']; ?> </button>
                <button class="tablinks" onclick="openTab(event, 'Treatment')"> <?php echo $lang['Treatment']; ?> </button>
                <button class="tablinks" onclick="openTab(event, 'Status')"> <?php echo $lang['Status']; ?> </button>
            </div>
            <div class="main-content">
                <form action="" method="POST">
                    <div id = "strategyContainer">
                        <div class="section" id="section">
                            <label>
                                <input type="checkbox" id="check-delete" name="check-delete" value="check-delete" class="check-delete"   onclick="changeContainerBackground(this)">
                                <?php echo $lang['Strategy']; ?> 
                            </label>
                            <input class="text_area" type="text" name="Strategy"><br>

                            <label> <?php echo $lang['Control']; ?> </label>
                            <input class="text" type="text" name="Control"><br>

                            <label> <?php echo $lang['Approved budget']; ?> </label>
                            <input class="text" type="text" name="Approved-budget"><br>

                            <label> <?php echo $lang['Owner']; ?> </label>
                            <input class="text" type="text" name="Owner"><br>

                            <label> <?php echo $lang['Required RT date']; ?> </label>
                            <input type="date" name="Required RT date" class="select-date-stategy1">

                            <label class="strategylabel appdate"> <?php echo $lang['Approval Date']; ?> </label>
                            <input type="date" name="Approval Date" class="select-date-stategy">

                            <label class="strategylabel"> <?php echo $lang['Assignment Date']; ?> </label>
                            <input type="date" name="Assignment Date" class="select-date"><br><hr>
                        </div>
                    </div>
                </form>
                <div class="my-icon">
                    <img src="todolist.jpg" alt="Logo">
                    <img src="report.jpg" alt="Logo">
                    <img src="document.jpg" alt="Logo">
                    <img src="calender.jpg" alt="Logo">
                    <img src="risk.jpg" alt="Logo">
                </div>
                <div class="submit-form">
                    <button type="submit" onclick="addField()" class="new"> <img src="new.svg" alt="New"> </button>
                    <button type="submit" class="save"> <img src="save.svg" alt="Save">  </button>
                    <button class="delete" type="submit" onclick="myFunction(this)"> <img src="false.svg" alt="Delete"> </button>
                </div>
            </div>
        </div>

        <script type="text/javascript">
            function addfield() {
                var container = document.getElementById("treatmentcontainer");
                var div = document.createElement("div");
                div.innerHTML = `
                <div class="section" id="section">
                    <label>
                        <input type="checkbox" id="check-delete" name="check-delete" value="check-delete" class="check-delete"  onclick="changeContainerBackground(this)">
                        <?php echo $lang['Action']; ?> 
                    </label>
                    <input class="text" type="text" name="Action"><br>

                    <label> <?php echo $lang['Owner']; ?> </label>
                    <input class="text" type="text" name="Owner"><br>

                    <label> <?php echo $lang['Due Date']; ?> </label>
                    <input class="text" type="text" name="Due Date"><br>

                    <label> <?php echo $lang['Status']; ?> </label>
                    <input type="text" name="Status" class="text"><br>

                    <label> <?php echo $lang['Date']; ?> </label>
                    <input type="date" name="Date" class="select-date-stategy1"><br><hr>
                </div>
                `;
                container.appendChild(div);
            }
        </script>

        <div id="Treatment" class="tabcontent">
            <div class="tab_fixed">
                <button class="tablinks" onclick="openTab(event, 'Risk')" id="defaultOpen"> <?php echo $lang['Risk']; ?> </button>
                <button class="tablinks" onclick="openTab(event, 'Score')"> <?php echo $lang['Score']; ?> </button>
                <button class="tablinks" onclick="openTab(event, 'Strategy')"> <?php echo $lang['Strategy']; ?> </button>
                <button class="tablinks" onclick="openTab(event, 'Treatment')"> <?php echo $lang['Treatment']; ?> </button>
                <button class="tablinks" onclick="openTab(event, 'Status')"> <?php echo $lang['Status']; ?> </button>
            </div>
            <div class="main-content">
                <form action="" method="POST">
                    <div id="treatmentcontainer">
                        <div class="section" id="section">
                            <label>
                                <input type="checkbox" id="check-delete" name="check-delete" value="check-delete" class="check-delete"  onclick="changeContainerBackground(this)">
                                <?php echo $lang['Action']; ?>
                            </label>
                            <input class="text" type="text" name="Action"><br>

                            <label> <?php echo $lang['Owner']; ?> </label>
                            <input class="text" type="text" name="Owner"><br>

                            <label> <?php echo $lang['Due Date']; ?> </label>
                            <input class="text" type="text" name="Due Date"><br>

                            <label> <?php echo $lang['Status']; ?> </label>
                            <input type="text" name="Status" class="text"><br>

                            <label> <?php echo $lang['Date']; ?> </label>
                            <input type="date" name="Date" class="select-date-stategy1"><br><hr>
                        </div>
                    </div>
                </form>
                <div class="my-icon">
                    <img src="todolist.jpg" alt="Logo">
                    <img src="report.jpg" alt="Logo">
                    <img src="document.jpg" alt="Logo">
                    <img src="calender.jpg" alt="Logo">
                    <img src="risk.jpg" alt="Logo">
                </div>
                <div class="submit-form">
                    <button type="submit" onclick="addfield()" class="new"> <img src="new.svg" alt="New"> </button>
                    <button type="submit" class="save"> <img src="save.svg" alt="Save">  </button>
                    <button class="delete" type="submit" onclick="myFunction(this)"> <img src="false.svg" alt="Delete"> </button>
                </div>
            </div>
        </div>

        <script type="text/javascript">
            function Addfield() {
                var container = document.getElementById("statuscontainer");
                var div = document.createElement("div");
                div.innerHTML = `
                <div class="section" id="section">
                    <table class="status-table">
                        <tr>
                            <td class="select-data">
                                <input type="checkbox" id="check-delete" name="check-delete" value="check-delete" class="check-delete"  onclick="changeContainerBackground(this)">
                            </td>
                            <td class="status-date"><input type="date" name="Date" class="select-date-stategy1"></td>
                            <td class="status-status">
                                    <select name="status-statu">
                                        <option value="open"> <?php echo $lang['Open']; ?> </option>
                                        <option value="closed"> <?php echo $lang['Closed']; ?> </option>
                                        <option value="in-progress"> <?php echo $lang['In Progress']; ?> </option>
                                    </select>
                            </td>
                            <td class="status-owner"><input type="text" name="Status" class="text"></td>
                            <td class="status-remark"><textarea></textarea></td>
                        </tr>
                    </table>
                </div>
                `;
                container.appendChild(div);
            }
        </script>

        <div id="Status" class="tabcontent">
            <div class="tab_fixed">
                <button class="tablinks" onclick="openTab(event, 'Risk')" id="defaultOpen"> <?php echo $lang['Risk']; ?> </button>
                <button class="tablinks" onclick="openTab(event, 'Score')"> <?php echo $lang['Score']; ?> </button>
                <button class="tablinks" onclick="openTab(event, 'Strategy')"> <?php echo $lang['Strategy']; ?> </button>
                <button class="tablinks" onclick="openTab(event, 'Treatment')"> <?php echo $lang['Treatment']; ?> </button>
                <button class="tablinks" onclick="openTab(event, 'Status')"> <?php echo $lang['Status']; ?> </button>
            </div>
            <div class="main-content">
                <form action="" method="POST" class="">
                    <div id="statuscontainer">
                        <div class="section">
                            <table class="status-table">
                                <tr>
                                    <th>
                                    <nav> <?php echo $lang['select all']; ?> </nav>
                                        <input type="checkbox" id="check-delete" name="check-delete" value="check-delete" class="check-delete">
                                    </th>
                                    <th><?php echo $lang['Date']; ?></th>
                                    <th><?php echo $lang['Status']; ?></th>
                                    <th><?php echo $lang['Owner']; ?></th>
                                    <th><?php echo $lang['Remarks']; ?></th>
                                </tr>
                                <tr id="section">
                                    <td class="select-data">
                                        <input type="checkbox" id="check-delete" name="check-delete" value="check-delete" class="check-delete"  onclick="changeContainerBackground(this)">
                                    </td>
                                    <td class="status-date"><input type="date" name="Date" class="select-date-stategy1 stat"></td>
                                    <td class="status-status">
                                        <select name="status-statu">
                                            <option value="open"> <?php echo $lang['Open']; ?> </option>
                                            <option value="closed"> <?php echo $lang['Closed']; ?> </option>
                                            <option value="in-progress"> <?php echo $lang['In Progress']; ?> </option>
                                        </select>
                                    </td>
                                    <td class="status-owner"><input type="email" name="Status" class="text"></td>
                                    <td class="status-remark"><textarea></textarea></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </form>
                <div class="my-icon">
                    <img src="todolist.jpg" alt="Logo">
                    <img src="report.jpg" alt="Logo">
                    <img src="document.jpg" alt="Logo">
                    <img src="calender.jpg" alt="Logo">
                    <img src="risk.jpg" alt="Logo">
                </div>
                <div class="submit-form">
                    <button type="submit" onclick="Addfield()" class="new"> <img src="new.svg" alt="New"> </button>
                    <button type="submit" class="save"> <img src="save.svg" alt="Save">  </button>
                    <button class="delete" type="submit" onclick="myFunction(this)"> <img src="false.svg" alt="Delete"> </button>
                </div>
            </div>           
        </div>
    </div>

    <script src="script.js"></script>

</body>
</html>

<?php
$conn->close();
?>