<?php
// Include configuration
require_once '../includes/config.php';

// Include grade calculation functions
require_once '../includes/grade_functions.php';

// Include validation functions
require_once '../includes/validation.php';

// Initialize variables
$students = [];
$statistics = [];
$errors = [];

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $names = sanitizeInput($_POST['names'] ?? []);
    $grades = sanitizeInput($_POST['grades'] ?? []);
    
    // Validate and process student data
    $validationResult = validateStudentData($names, $grades);
    $students = $validationResult['students'];
    $errors = $validationResult['errors'];
    
    // Calculate statistics if we have valid students
    if (!empty($students)) {
        $statistics = calculateGradeStatistics($students);
    }
}

// Include the template for presentation
include '../../templates/results.php';
?>
