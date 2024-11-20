<?php
include('../connection.php');

try {
    if (!$con) {
        throw new Exception("Database connection failed.");
    }
    //LEFT JOIN
    $statement = $con->prepare("
        SELECT 
            a.ID,
            a.Alumni_ID_Number,
            a.Student_Number, 
            a.Last_Name, 
            a.First_Name, 
            a.Middle_Name, 
            a.College, 
            a.Department, 
            a.Section, 
            a.Year_Graduated, 
            a.Contact_Number, 
            a.Personal_Email, 
            ed.Employment,
            ed.Employment_Status, 
            ed.Present_Occupation, 
            ed.Name_of_Employer, 
            ed.Address_of_Employer, 
            ed.Number_of_Years_in_Present_Employer, 
            ed.Type_of_Employer, 
            ed.Major_Line_of_Business,
            CONCAT('AL', LPAD(a.Alumni_ID_Number, 5, '0')) AS Alumni_ID_Number_Format
        FROM `2024-2025` a 
        LEFT JOIN `2024-2025_ed` ed ON a.Alumni_ID_Number = ed.Alumni_ID_Number 
        WHERE ed.Alumni_ID_Number IS NULL OR ed.ID = (
            SELECT MAX(ID) 
            FROM `2024-2025_ed` 
            WHERE Alumni_ID_Number = a.Alumni_ID_Number
        )
    ");

    $statement->execute();

    $colleges = $con->query("SELECT DISTINCT College FROM `2024-2025`")->fetchAll(PDO::FETCH_COLUMN);
    $departments = $con->query("SELECT DISTINCT Department FROM `2024-2025`")->fetchAll(PDO::FETCH_COLUMN);
    $sections = $con->query("SELECT DISTINCT Section FROM `2024-2025`")->fetchAll(PDO::FETCH_COLUMN);

    if (isset($_GET['success']) && $_GET['success'] == 1) {
        echo '<div class="alert alert-success">Alumni information added successfully!</div>';
    }
} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
}

if (isset($_GET['deleted']) && $_GET['deleted'] == 1) {
    echo '<div class="alert alert-success">Alumni record deleted successfully!</div>';
}

$searchQuery = "";
if (isset($_GET['searchInput'])) {
    $searchInput = $_GET['searchInput'];
    $searchQuery = "WHERE a.Last_Name LIKE :searchInput OR a.First_Name LIKE :searchInput OR a.Middle_Name LIKE :searchInput OR a.College LIKE :searchInput OR a.Department LIKE :searchInput OR a.Section LIKE :searchInput OR a.Year_Graduated LIKE :searchInput OR a.Contact_Number LIKE :searchInput OR a.Personal_Email LIKE :searchInput";
}

if (isset($_GET['searchInput'])) {
    $requete = "
        SELECT 
            a.ID,
            a.Alumni_ID_Number,
            a.Student_Number, 
            a.Last_Name, 
            a.First_Name, 
            a.Middle_Name, 
            a.College, 
            a.Department, 
            a.Section, 
            a.Year_Graduated, 
            a.Contact_Number, 
            a.Personal_Email, 
            ed.Employment,
            ed.Employment_Status, 
            ed.Present_Occupation, 
            ed.Name_of_Employer, 
            ed.Address_of_Employer, 
            ed.Number_of_Years_in_Present_Employer, 
            ed.Type_of_Employer, 
            ed.Major_Line_of_Business,
            CONCAT('AL', LPAD(a.Alumni_ID_Number, 5, '0')) AS Alumni_ID_Number_Format
        FROM `2024-2025` a 
        LEFT JOIN `2024-2025_ed` ed ON a.Alumni_ID_Number = ed.Alumni_ID_Number 
        $searchQuery
    ";
    $stmt = $con->prepare($requete);
    $stmt->bindValue(':searchInput', "%$searchInput%");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($result as $row) {
        echo "<tr>
                <td>" . htmlspecialchars($row['ID'] ?? '') . "</td>
                <td>" . htmlspecialchars($row['Alumni_ID_Number'] ?? '') . "</td>
                <td>" . htmlspecialchars($row['Student_Number'] ?? '') . "</td>
                <td>" . htmlspecialchars($row['Last_Name'] ?? '') . "</td>
                <td>" . htmlspecialchars($row['First_Name'] ?? '') . "</td>
                <td>" . htmlspecialchars($row['Middle_Name'] ?? '') . "</td>
                <td>" . htmlspecialchars($row['College'] ?? '') . "</td>
                <td>" . htmlspecialchars($row['Department'] ?? '') . "</td>
                <td>" . htmlspecialchars($row['Section'] ?? '') . "</td>
                <td>" . htmlspecialchars($row['Year_Graduated'] ?? '') . "</td>
                <td>" . htmlspecialchars($row['Contact_Number'] ?? '') . "</td>
                <td>" . htmlspecialchars($row['Personal_Email'] ?? '') . "</td>
                <td>" . htmlspecialchars($row['Employment'] ?? '') . "</td>
                <td>" . htmlspecialchars($row['Employment_Status'] ?? '') . "</td>
                <td>" . htmlspecialchars($row['Present_Occupation'] ?? '') . "</td>
                <td>" . htmlspecialchars($row['Name_of_Employer'] ?? '') . "</td>
                <td>" . htmlspecialchars($row['Address_of_Employer'] ?? '') . "</td>
                <td>" . htmlspecialchars($row['Number_of_Years_in_Present_Employer'] ?? '') . "</td>
                <td>" . htmlspecialchars($row['Type_of_Employer'] ?? '') . "</td>
                <td>" . htmlspecialchars($row['Major_Line_of_Business'] ?? '') . "</td>
                <td>
                    <a href='alumni_edit.php?Alumni_ID_Number=" . $row['Alumni_ID_Number'] . "'><i class='far fa-pen'></i></a>
                    <a href='alumni_process.php?action=delete&alumni_id=" . $row['Alumni_ID_Number'] . "'><i class='far fa-trash'></i></a>
                </td>
              </tr>";
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni List</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" crossorigin="anonymous" />
    <style>
        .btn-add {
            margin-left: auto;
        }

        .btn {
            white-space: nowrap;
        }
    </style>
</head>

<body class="bg-content">
    <main class="dashboard d-flex">
        <?php include "component/sidebar.php"; ?>
        <div class="container-fluid px">
            <?php include "component/alumni_list_header.php"; ?>
            <div class="alumni-list-header d-flex justify-content-between align-items-center py-2">
                <div class="title h6 fw-bold">Alumni List</div>
                <div class="btn-add d-flex gap-3 align-items-center">
                    <?php include 'alumni_add.php'; ?>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#importModal">Import Alumni Data</button>
                </div>
            </div>

            <div class="filter-container">
                <select id="collegeFilter" class="form-select" onchange="filterTable()">
                    <option value="">College</option>
                    <?php foreach ($colleges as $college): ?>
                        <option value="<?php echo htmlspecialchars($college); ?>"><?php echo htmlspecialchars($college); ?></option>
                    <?php endforeach; ?>
                </select>

                <select id="departmentFilter" class="form-select" onchange="filterTable()">
                    <option value="">Department</option>
                    <?php foreach ($departments as $department): ?>
                        <option value="<?php echo htmlspecialchars($department); ?>"><?php echo htmlspecialchars($department); ?></option>
                    <?php endforeach; ?>
                </select>

                <select id="sectionFilter" class="form-select" onchange="filterTable()">
                    <option value="">Section</option>
                    <?php foreach ($sections as $section): ?>
                        <option value="<?php echo htmlspecialchars($section); ?>"><?php echo htmlspecialchars($section); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <?php include 'importmodal.php'; ?>

            <div class="table-responsive table-container">
                <table class="table alumni_list table-borderless">
                    <thead>
                        <tr class="align-middle">
                            <th>ID</th>
                            <th>Alumni ID Number</th>
                            <th>Student Number</th>
                            <th>Last Name</th>
                            <th>First Name</th>
                            <th>Middle Name</th>
                            <th>College</th>
                            <th>Department</th>
                            <th>Section</th>
                            <th>Year Graduated</th>
                            <th>Contact Number</th>
                            <th>Personal Email</th>
                            <th>Employment</th>
                            <th>Employment Status</th>
                            <th>Present Occupation</th>
                            <th>Name of Employer</th>
                            <th>Address of Employer</th>
                            <th>Number of Years in Present Employer</th>
                            <th>Type of Employer</th>
                            <th>Major Line of Business</th>
                            <th class="opacity">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="alumniTable">
                        <?php if ($statement->rowCount() > 0): ?>
                            <?php while ($row = $statement->fetch(PDO::FETCH_ASSOC)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['ID'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($row['Alumni_ID_Number_Format'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($row['Student_Number'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($row['Last_Name'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($row['First_Name'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($row['Middle_Name'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($row['College'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($row['Department'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($row['Section'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($row['Year_Graduated'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($row['Contact_Number'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($row['Personal_Email'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($row['Employment'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($row['Employment_Status'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($row['Present_Occupation'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($row['Name_of_Employer'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($row['Address_of_Employer'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($row['Number_of_Years_in_Present_Employer'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($row['Type_of_Employer'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($row['Major_Line_of_Business'] ?? ''); ?></td>
                                    <td>
                                        <a href="alumni_edit.php?Alumni_ID_Number=<?php echo $row['Alumni_ID_Number'] ?>"><i class="far fa-pen"></i></a>
                                        <a href="alumni_process.php?action=delete&alumni_id=<?php echo $row['Alumni_ID_Number']; ?>"><i class="far fa-trash"></i></a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="20" class="text-center">No alumni found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script>
        function filterTable() {
            const collegeFilter = document.getElementById('collegeFilter').value.toLowerCase();
            const departmentFilter = document.getElementById('departmentFilter').value.toLowerCase();
            const sectionFilter = document.getElementById('sectionFilter').value.toLowerCase();
            const table = document.querySelector('.alumni_list tbody');
            const rows = table.querySelectorAll('tr');

            rows.forEach(row => {
                const college = row.cells[6].textContent.toLowerCase();
                const department = row.cells[7].textContent.toLowerCase();
                const section = row.cells[8].textContent.toLowerCase();

                const collegeMatch = college.includes(collegeFilter);
                const departmentMatch = department.includes(departmentFilter);
                const sectionMatch = section.includes(sectionFilter);

                row.style.display = (collegeMatch && departmentMatch && sectionMatch) ? '' : 'none';
            });
        }

        document.getElementById('searchInput').addEventListener('input', function() {
            const searchInput = this.value;
            fetch(`alumni_list.php?searchInput=${searchInput}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('alumniTable').innerHTML = data;
                });
        });
    </script>
</body>

</html