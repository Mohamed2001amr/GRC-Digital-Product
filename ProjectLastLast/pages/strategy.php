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
    $existing_controls = $_POST['existing_controls'];
    $type = $_POST['type'];
    $function = $_POST['function'];
    $site = $_POST['site'];
    $initiative = $_POST['initiative'];
    $initiative_level = $_POST['initiative_level'];
    $status = $_POST['status'];
    $status_date = $_POST['status_date'];
    $reported_by = $_POST['reported_by'];
    $reporting_date = $_POST['reporting_date'];

    $sql = "INSERT INTO risks (scenario, source, threats, vulnerabilities, existing_controls, type, function, site, initiative, initiative_level, status, status_date, reported_by, reporting_date)
            VALUES ('$scenario', '$source', '$threats', '$vulnerabilities', '$existing_controls', '$type', '$function', '$site', '$initiative', '$initiative_level', '$status', '$status_date', '$reported_by', '$reporting_date')";

    if ($conn->query($sql) === TRUE) {
        echo "Risk successfully added!";
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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Risk Management</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>
<div class="container">
    <header>
        <img src="logo.jpeg" alt="Logo" class="logo">
        <nav class="header-tabs">
            <ul>
                <li><a href="#">Risk</a></li>
                <li><a href="#">Compliance</a></li>
                <li><a href="#">Performance</a></li>
                <li><a href="#">Assets</a></li>
                <li><a href="#">BCM</a></li>
                <li><a href="#">Crisis</a></li>
            </ul>
        </nav>
    </header>

    <!-- Sidebar -->
    <div class="sidebar">
        <ul>
            <li><a href="#">Register</a></li>
            <li><a href="#">Evaluation</a></li>
            <li><a href="#">Reports</a></li>
            <li><a href="#">myRisks</a></li>
            <li><a href="#">myActions</a></li>
        </ul>
    </div>

    <div class="sidebar-r">
        <ul>
            <li><a href="risk.php">Risk</a></li>
            <li><a href="score.php">Score</a></li>
            <li><a href="strategy.php">Strategy</a></li>
            <li><a href="treatment.php">Treatment</a></li>
            <li><a href="status.php">Status</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h2>Risk Strategy</h2>
        <form action="" method="POST">
            <input type="hidden" name="add_risk" value="1">
            <label>Scenario:</label>
            <textarea name="scenario" required></textarea><br>
            
            <label>Source:</label>
            <input type="text" name="source"><br>

            <label>Threats:</label>
            <textarea name="threats"></textarea><br>

            <label>Vulnerabilities:</label>
            <textarea name="vulnerabilities"></textarea><br>

            <label>Existing Controls:</label>
            <textarea name="existing_controls"></textarea><br>

            <label>Type:</label>
            <input type="text" name="type"><br>

            <label>Function:</label>
            <input type="text" name="function"><br>

            <label>Site:</label>
            <input type="text" name="site"><br>

            <label>Initiative:</label>
            <textarea name="initiative"></textarea><br>

            <label>Initiative Level:</label>
            <select name="initiative_level">
                <option value="Project">Project</option>
                <option value="Program">Program</option>
                <option value="Portfolio">Portfolio</option>
            </select><br>

            <label>Status:</label>
            <select name="status">
                <option value="Open">Open</option>
                <option value="Closed">Closed</option>
                <option value="In Progress">In Progress</option>
            </select><br>

            <label>Status Date:</label>
            <input type="date" name="status_date"><br>

            <label>Reported By:</label>
            <input type="text" name="reported_by"><br>

            <label>Reporting Date:</label>
            <input type="date" name="reporting_date"><br>

            <button type="submit">Add Risk</button>
        </form>

        <h2>Risk List</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Scenario</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . $row['scenario'] . "</td>";
                    echo "<td>" . $row['status'] . "</td>";
                    echo "<td><a href='?delete=" . $row['id'] . "'>Delete</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No risks found</td></tr>";
            }
            ?>
        </table>
        <h2>Search Risks</h2>
        <div class="search-bar">
            <input type="text" placeholder="Search for risks..." />
            <button type="submit">Search</button>
        </div>    
    </div>

    <div class="clearfix"></div>
</div>

    <form action="main.php" method="GET">
        <button type="submit">Back</button>
    </form>
</body>
</html>

<?php
$conn->close();
?>