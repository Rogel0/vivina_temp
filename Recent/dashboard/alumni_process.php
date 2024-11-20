<?php
session_start();
include('../connection.php');
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['action']) && $_GET['action'] == 'delete') {
    try {
        $alumniID = $_GET['alumni_id'];
        $con->beginTransaction();

        $stmtDelete = $con->prepare("DELETE FROM `2024-2025_ed` WHERE Alumni_ID_Number = :alumniID");
        $stmtDelete->execute([':alumniID' => $alumniID]);

        $stmtDeleteAlumni = $con->prepare("DELETE FROM `2024-2025` WHERE Alumni_ID_Number = :alumniID");
        $stmtDeleteAlumni->execute([':alumniID' => $alumniID]);

        $con->commit();
        $_SESSION['success_message'] = 'Alumni deleted successfully!';
        header("Location: alumni_list.php");
        exit;
    } catch (PDOException $e) {
        $con->rollBack();
        die("Error: " . $e->getMessage());
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        if (isset($_POST['alumni_id'])) {
            // Update Logic
            $alumniID = $_POST['alumni_id'];
            $studentNumber = $_POST['student_number'] ?? '';
            $lastName = $_POST['last_name'] ?? '';
            $firstName = $_POST['first_name'] ?? '';
            $middleName = $_POST['middle_name'] ?? '';
            $college = $_POST['college'] ?? '';
            $department = $_POST['department'] ?? '';
            $section = $_POST['section'] ?? '';
            $yearGraduated = $_POST['year_graduated'] ?? '';
            $contactNumber = $_POST['contact_number'] ?? '';
            $personalEmail = $_POST['personal_email'] ?? '';
            $employment = $_POST['employment'] ?? '';
            $employmentStatus = $_POST['employment_status'] ?? '';
            $presentOccupation = $_POST['present_occupation'] ?? '';
            $employerName = $_POST['name_of_employer'] ?? '';
            $employerAddress = $_POST['address_of_employer'] ?? '';
            $yearsInPresentEmployer = ($_POST['number_of_years_in_present_employer'] ?? '') ?: null;
            $typeOfEmployer = $_POST['type_of_employer'] ?? '';
            $lineOfBusiness = $_POST['major_line_of_business'] ?? '';

            $con->beginTransaction();

            // Update alumni information
            $stmtUpdateAlumni = $con->prepare("
                UPDATE `2024-2025` SET 
                    Student_Number = :studentNumber, 
                    Last_Name = :lastName, 
                    First_Name = :firstName, 
                    Middle_Name = :middleName, 
                    College = :college, 
                    Department = :department, 
                    Section = :section, 
                    Year_Graduated = :yearGraduated, 
                    Contact_Number = :contactNumber, 
                    Personal_Email = :personalEmail 
                WHERE Alumni_ID_Number = :alumniID
            ");
            
            $stmtUpdateAlumni->execute([
                ':studentNumber' => $studentNumber,
                ':lastName' => $lastName,
                ':firstName' => $firstName,
                ':middleName' => $middleName,
                ':college' => $college,
                ':department' => $department,
                ':section' => $section,
                ':yearGraduated' => $yearGraduated,
                ':contactNumber' => $contactNumber,
                ':personalEmail' => $personalEmail,
                ':alumniID' => $alumniID
            ]);

            // Update employment information
            $stmtUpdateEmployment = $con->prepare("
            UPDATE `2024-2025_ed` SET 
            Employment = :employment, 
            Employment_Status = :employmentStatus, 
            Present_Occupation = :presentOccupation, 
            Name_of_Employer = :employerName, 
            Address_of_Employer = :employerAddress, 
            Number_of_Years_in_Present_Employer = :yearsInPresentEmployer, 
            Type_of_Employer = :typeOfEmployer, 
            Major_Line_of_Business = :lineOfBusiness 
            WHERE Alumni_ID_Number = :alumniID
            ");

            $isSelfEmployedOrLooking = ($employmentStatus === 'Self-employed' || $employmentStatus === 'Actively Looking for a Job' || $employmentStatus === 'Never Been Employed');
            $isEmployed = ($employment === 'Employed');

            $employmentStatusValue = $isSelfEmployedOrLooking ? $employmentStatus : ($isEmployed ? $employmentStatus : null);
            $presentOccupationValue = $isSelfEmployedOrLooking || $isEmployed ? $presentOccupation : null;
            $employerNameValue = $isEmployed ? $employerName : ($isSelfEmployedOrLooking ? $employerName : null);
            $employerAddressValue = $isEmployed ? $employerAddress : ($isSelfEmployedOrLooking ? $employerAddress : null);
            $yearsInPresentEmployerValue = $isEmployed ? $yearsInPresentEmployer : ($isSelfEmployedOrLooking ? $yearsInPresentEmployer : null);
            $typeOfEmployerValue = $isEmployed ? $typeOfEmployer : ($isSelfEmployedOrLooking ? $typeOfEmployer : null);
            $lineOfBusinessValue = $isEmployed ? $lineOfBusiness : ($isSelfEmployedOrLooking ? $lineOfBusiness : null);

            $stmtUpdateEmployment->execute([
                ':employment' => $employment,
                ':employmentStatus' => $employmentStatusValue,
                ':presentOccupation' => $presentOccupationValue,
                ':employerName' => $employerNameValue,
                ':employerAddress' => $employerAddressValue,
                ':yearsInPresentEmployer' => $yearsInPresentEmployerValue,
                ':typeOfEmployer' => $typeOfEmployerValue,
                ':lineOfBusiness' => $lineOfBusinessValue, 
                ':alumniID' => $alumniID
            ]);

            $con->commit();
            $_SESSION['success_message'] = 'Alumni updated successfully!';
            header("Location: alumni_list.php");
            exit;
        } else {
            // Add Logic
            $studentNumber = $_POST['Student_Number'] ?? '';
            $lastName = $_POST['Last_Name'] ?? '';
            $firstName = $_POST['First_Name'] ?? '';
            $middleName = $_POST['Middle_Name'] ?? '';
            $college = $_POST['College'] ?? '';
            $department = $_POST['Department'] ?? '';
            $section = $_POST['Section'] ?? '';
            $yearGraduated = $_POST['Year_Graduated'] ?? '';
            $contactNumber = $_POST['Contact_Number'] ?? '';
            $personalEmail = $_POST['Personal_Email'] ?? '';
            $employment = $_POST['Employment'] ?? '';
            $employmentStatus = $_POST['Employment_Status'] ?? '';
            $presentOccupation = $_POST['Present_Occupation'] ?? '';
            $employerName = $_POST['Name_of_Employer'] ?? '';
            $employerAddress = $_POST['Address_of_Employer'] ?? '';
            $yearsInPresentEmployer = ($_POST['Number_of_Years_in_Present_Employer'] ?? '') ?: null;
            $typeOfEmployer = $_POST['Type_of_Employer'] ?? '';
            $lineOfBusiness = $_POST['Major_Line_of_Business'] ?? '';

            $con->beginTransaction();
            $con->exec("SET FOREIGN_KEY_CHECKS = 0");

            $stmtInsertAlumni = $con->prepare("
                INSERT INTO `2024-2025` (
                    Student_Number, Last_Name, First_Name, Middle_Name, College, Department, Section, 
                    Year_Graduated, Contact_Number, Personal_Email
                ) VALUES (
                    :studentNumber, :lastName, :firstName, :middleName, :college, :department, :section, 
                    :yearGraduated, :contactNumber, :personalEmail
                )
            ");
            $stmtInsertAlumni->execute([
                ':studentNumber' => $studentNumber,
                ':lastName' => $lastName,
                ':firstName' => $firstName,
                ':middleName' => $middleName,
                ':college' => $college,
                ':department' => $department,
                ':section' => $section,
                ':yearGraduated' => $yearGraduated,
                ':contactNumber' => $contactNumber,
                ':personalEmail' => $personalEmail
            ]);

            $alumniID = $con->lastInsertId();

            $stmtInsertEmployment = $con->prepare("
                INSERT INTO `2024-2025_ed` (
                    Alumni_ID_Number, Employment, Employment_Status, Present_Occupation, Name_of_Employer, 
                    Address_of_Employer, Number_of_Years_in_Present_Employer, Type_of_Employer, Major_Line_of_Business
                ) VALUES (
                    :alumniID, :employment, :employmentStatus, :presentOccupation, :employerName, :employerAddress, 
                    :yearsInPresentEmployer, :typeOfEmployer, :lineOfBusiness
                )
            ");
            $stmtInsertEmployment->execute([
                ':alumniID' => $alumniID,
                ':employment' => $employment,
                ':employmentStatus' => $employmentStatus,
                ':presentOccupation' => $presentOccupation,
                ':employerName' => $employerName,
                ':employerAddress' => $employerAddress,
                ':yearsInPresentEmployer' => $yearsInPresentEmployer,
                ':typeOfEmployer' => $typeOfEmployer,
                ':lineOfBusiness' => $lineOfBusiness
            ]);

            $con->commit();
            $_SESSION['success_message'] = 'Alumni added successfully!';
            header("Location: alumni_list.php");
            exit;
        }
    } catch (PDOException $e) {
        $con->rollBack();
        error_log("Error: " . $e->getMessage());
        $_SESSION['error_message'] = 'An error occurred: ' . $e->getMessage();
    }
}
