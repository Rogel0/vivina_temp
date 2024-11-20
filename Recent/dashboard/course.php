<?php 

include('../connection.php');

if (isset($_POST['submit'])) {
    $name = $_POST['Name'];
    $description = $_POST['Description'];
    $prix = $_POST['Prix'];

    $sql = "INSERT INTO courses (Name, Description, Prix) VALUES (:name, :description, :prix)";
    $stmt = $con->prepare($sql);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':prix', $prix);

    if ($stmt->execute()) {
        echo "New course added successfully";
    } else {
        echo "Error: " . implode(", ", $stmt->errorInfo());
    }
}

$searchQuery = "";
if (isset($_GET['searchInput'])) {
    $searchInput = $_GET['searchInput'];
    $searchQuery = "WHERE Name LIKE :searchInput OR Description LIKE :searchInput OR Prix LIKE :searchInput";
}

if (isset($_GET['searchInput'])) {
    $requete = "SELECT * FROM courses $searchQuery";
    $stmt = $con->prepare($requete);
    $stmt->bindValue(':searchInput', "%$searchInput%");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($result as $value) {
        echo "<tr>
                <td>{$value['Name']}</td>
                <td>{$value['Description']}</td>
                <td>{$value['Prix']}</td>
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
    <title>Courses</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" crossorigin="anonymous" />
</head>
<body class="bg-content">
    <main class="dashboard d-flex">
        <?php include "component/sidebar.php"; ?>
        <div class="container-fluid px-4">
            <?php include "component/header.php"; ?>
            <div class="button-add-student">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">Add Courses</button>
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Add Courses</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="" enctype="multipart/form-data">
                                    <div class="">
                                        <label for="recipient-name" class="col-form-label">Name:</label>
                                        <input type="text" class="form-control" id="recipient-name" name="Name">
                                    </div>
                                    <div class="">
                                        <label for="recipient-description" class="col-form-label">Description:</label>
                                        <input type="text" class="form-control" id="recipient-description" name="Description">
                                    </div>
                                    <div class="">
                                        <label for="recipient-prix" class="col-form-label">Prix:</label>
                                        <input type="text" class="form-control" id="recipient-prix" name="Prix">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" name="submit" class="btn btn-primary">Add Courses</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="courses">
                <table class="table table-responsive">
                    <thead>
                        <th>Courses</th>
                        <th>Description</th>
                        <th>Prix</th>
                    </thead>
                    <tbody id="coursesTable"> 
                    <?php 
                    include '../connection.php'; 
                    $requete = "SELECT * FROM courses";
                    $stmt = $con->prepare($requete);
                    $stmt->execute();
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach($result as $value):
                    ?>
                        <tr> 
                            <td><?php echo $value['Name'] ?></td>
                            <td><?php echo $value['Description'] ?></td>
                            <td><?php echo $value['Prix'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <script src="../assets/js/script.js"></script>
    <script src="../assets/js/bootstrap.bundle.js"></script>
</body>
</html>