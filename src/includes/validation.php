<?php
/**
 * Student Grade Manager - Validation Functions
 * 
 * This file contains all validation logic for the Student Grade Manager application.
 * Separating validation from business logic improves maintainability and testability.
 */

/**
 * Validates a single student name
 * 
 * @param string $name The student name to validate
 * @return array Array with validation result and error message
 */
function validateStudentName($name) {
    $name = trim($name);
    
    // Check if name is empty
    if (empty($name)) {
        return ['valid' => false, 'error' => 'Student name cannot be empty.'];
    }
    
    // Check minimum length (at least 2 characters)
    if (strlen($name) < MIN_NAME_LENGTH) {
        return ['valid' => false, 'error' => 'Student name must be at least ' . MIN_NAME_LENGTH . ' characters long.'];
    }
    
    // Check maximum length (50 characters)
    if (strlen($name) > MAX_NAME_LENGTH) {
        return ['valid' => false, 'error' => 'Student name cannot exceed ' . MAX_NAME_LENGTH . ' characters.'];
    }
    
    // Check for valid characters (letters, spaces, hyphens, apostrophes)
    if (!preg_match('/^[a-zA-Z\s\'-]+$/', $name)) {
        return ['valid' => false, 'error' => 'Student name can only contain letters, spaces, hyphens, and apostrophes.'];
    }
    
    // Check for consecutive spaces
    if (preg_match('/\s{2,}/', $name)) {
        return ['valid' => false, 'error' => 'Student name cannot contain consecutive spaces.'];
    }
    
    // Check for names that are just spaces or special characters
    if (preg_match('/^[\s\'-]+$/', $name)) {
        return ['valid' => false, 'error' => 'Student name must contain at least one letter.'];
    }
    
    return ['valid' => true, 'name' => ucwords(strtolower($name))];
}

/**
 * Validates a single grade
 * 
 * @param mixed $grade The grade to validate
 * @return array Array with validation result and error message
 */
function validateGrade($grade) {
    // Check if grade is empty
    if ($grade === '' || $grade === null) {
        return ['valid' => false, 'error' => 'Grade cannot be empty.'];
    }
    
    // Check if grade is numeric
    if (!is_numeric($grade)) {
        return ['valid' => false, 'error' => 'Grade must be a number.'];
    }
    
    $gradeValue = floatval($grade);
    
    // Check grade range
    if ($gradeValue < MIN_GRADE) {
        return ['valid' => false, 'error' => 'Grade cannot be less than ' . MIN_GRADE . '.'];
    }
    
    if ($gradeValue > MAX_GRADE) {
        return ['valid' => false, 'error' => 'Grade cannot exceed ' . MAX_GRADE . '.'];
    }
    
    // Check for reasonable decimal places (max 2)
    if (strpos($grade, '.') !== false) {
        $decimalPlaces = strlen(substr(strrchr($grade, "."), 1));
        if ($decimalPlaces > MAX_DECIMAL_PLACES) {
            return ['valid' => false, 'error' => 'Grade can have maximum ' . MAX_DECIMAL_PLACES . ' decimal places.'];
        }
    }
    
    return ['valid' => true, 'grade' => round($gradeValue, 2)];
}

/**
 * Validates student data with comprehensive checks
 * 
 * @param array $names Array of student names
 * @param array $grades Array of student grades
 * @return array Array with validation result and processed data
 */
function validateStudentData($names, $grades) {
    $students = [];
    $errors = [];
    $processedNames = [];
    $maxStudents = MAX_STUDENTS; // Maximum number of students allowed
    
    // Check if arrays are empty
    if (empty($names) || empty($grades)) {
        $errors[] = 'Please enter at least one student name and grade.';
        return ['students' => [], 'errors' => $errors];
    }
    
    // Check maximum number of students
    if (count($names) > $maxStudents) {
        $errors[] = "Maximum {$maxStudents} students allowed per submission.";
        return ['students' => [], 'errors' => $errors];
    }
    
    // Process each student entry
    for ($i = 0; $i < count($names); $i++) {
        $name = $names[$i] ?? '';
        $grade = $grades[$i] ?? '';
        
        // Skip completely empty entries
        if (empty($name) && $grade === '') {
            continue;
        }
        
        // Validate name
        $nameValidation = validateStudentName($name);
        if (!$nameValidation['valid']) {
            $errors[] = "Student " . ($i + 1) . ": " . $nameValidation['error'];
            continue;
        }
        
        // Validate grade
        $gradeValidation = validateGrade($grade);
        if (!$gradeValidation['valid']) {
            $errors[] = "Student " . ($i + 1) . " ({$nameValidation['name']}): " . $gradeValidation['error'];
            continue;
        }
        
        // Check for duplicate names (case-insensitive)
        // commenting this there might be students witht the same firstname and lastname
        // $normalizedName = strtolower($nameValidation['name']);
        // if (in_array($normalizedName, $processedNames)) {
        //     $errors[] = "Duplicate student name: {$nameValidation['name']}";
        //     continue;
        // }
        
        // $processedNames[] = $normalizedName;
        
        // Add valid student
        $students[] = [
            'name' => $nameValidation['name'],
            'grade' => $gradeValidation['grade']
        ];
    }
    
    // Check if we have any valid students
    if (empty($students)) {
        if (empty($errors)) {
            $errors[] = 'Please enter valid student names and grades (0-100).';
        }
    } else {
        // Additional validation for valid students
        if (count($students) < 1) {
            $errors[] = 'At least one student is required.';
        }
        
        // Check for grade distribution warnings
        $gradeWarnings = checkGradeDistribution($students);
        if (!empty($gradeWarnings)) {
            $errors = array_merge($errors, $gradeWarnings);
        }
    }
    
    return ['students' => $students, 'errors' => $errors];
}

/**
 * Checks grade distribution for potential issues
 * 
 * @param array $students Array of student data
 * @return array Array of warning messages
 */
function checkGradeDistribution($students) {
    $warnings = [];
    $grades = array_column($students, 'grade');
    
    // Check if all grades are the same
    if (count(array_unique($grades)) === 1) {
        $warnings[] = '⚠️ All students have the same grade. Please verify the data.';
    }
    
    // Check for too many failing grades (more than 50%)
    $failingCount = count(array_filter($grades, function($grade) {
        return $grade < GRADE_D_MIN;
    }));
    
    if ($failingCount > count($grades) * 0.5) {
        $warnings[] = '⚠️ More than 50% of students are failing. Please review the grading criteria.';
    }
    
    // Check for grade clustering (too many grades in a small range)
    $gradeRanges = [
        GRADE_A_MIN . '-100' => 0,
        GRADE_B_MIN . '-89' => 0,
        GRADE_C_MIN . '-79' => 0,
        GRADE_D_MIN . '-69' => 0,
        '0-' . (GRADE_D_MIN - 1) => 0
    ];
    
    foreach ($grades as $grade) {
        if ($grade >= GRADE_A_MIN) $gradeRanges[GRADE_A_MIN . '-100']++;
        elseif ($grade >= GRADE_B_MIN) $gradeRanges[GRADE_B_MIN . '-89']++;
        elseif ($grade >= GRADE_C_MIN) $gradeRanges[GRADE_C_MIN . '-79']++;
        elseif ($grade >= GRADE_D_MIN) $gradeRanges[GRADE_D_MIN . '-69']++;
        else $gradeRanges['0-' . (GRADE_D_MIN - 1)]++;
    }
    
    $totalStudents = count($grades);
    foreach ($gradeRanges as $range => $count) {
        if ($count > $totalStudents * 0.8) {
            $warnings[] = "⚠️ {$count} out of {$totalStudents} students are in the {$range} range. Consider reviewing the assessment.";
        }
    }
    
    return $warnings;
}

/**
 * Sanitizes input data
 * 
 * @param mixed $data The data to sanitize
 * @return mixed The sanitized data
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Validates form submission method
 * 
 * @param string $method The HTTP method
 * @return bool Whether the method is valid
 */
function validateRequestMethod($method) {
    return strtoupper($method) === 'POST';
}

/**
 * Validates that required form fields are present
 * 
 * @param array $postData The POST data
 * @param array $requiredFields Array of required field names
 * @return array Array with validation result and missing fields
 */
function validateRequiredFields($postData, $requiredFields) {
    $missingFields = [];
    
    foreach ($requiredFields as $field) {
        if (!isset($postData[$field]) || empty($postData[$field])) {
            $missingFields[] = $field;
        }
    }
    
    return [
        'valid' => empty($missingFields),
        'missing_fields' => $missingFields
    ];
}

/**
 * Validates file upload (if needed in future)
 * 
 * @param array $file The uploaded file array
 * @param array $allowedTypes Array of allowed MIME types
 * @param int $maxSize Maximum file size in bytes
 * @return array Array with validation result and error message
 */
function validateFileUpload($file, $allowedTypes = [], $maxSize = 5242880) {
    // Check if file was uploaded
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return ['valid' => false, 'error' => 'No file was uploaded.'];
    }
    
    // Check file size
    if ($file['size'] > $maxSize) {
        return ['valid' => false, 'error' => 'File size exceeds maximum allowed size.'];
    }
    
    // Check file type if specified
    if (!empty($allowedTypes) && !in_array($file['type'], $allowedTypes)) {
        return ['valid' => false, 'error' => 'File type not allowed.'];
    }
    
    return ['valid' => true, 'file' => $file];
}

/**
 * Validates email format (if needed in future)
 * 
 * @param string $email The email to validate
 * @return bool Whether the email is valid
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validates date format (if needed in future)
 * 
 * @param string $date The date string to validate
 * @param string $format The expected date format
 * @return bool Whether the date is valid
 */
function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

/**
 * Validates numeric range
 * 
 * @param mixed $value The value to validate
 * @param float $min Minimum value
 * @param float $max Maximum value
 * @return bool Whether the value is within range
 */
function validateNumericRange($value, $min, $max) {
    if (!is_numeric($value)) {
        return false;
    }
    
    $numValue = floatval($value);
    return $numValue >= $min && $numValue <= $max;
}

/**
 * Validates string length
 * 
 * @param string $string The string to validate
 * @param int $minLength Minimum length
 * @param int $maxLength Maximum length
 * @return bool Whether the string length is valid
 */
function validateStringLength($string, $minLength, $maxLength) {
    $length = strlen(trim($string));
    return $length >= $minLength && $length <= $maxLength;
}

/**
 * Validates alphanumeric characters
 * 
 * @param string $string The string to validate
 * @param bool $allowSpaces Whether to allow spaces
 * @return bool Whether the string contains only alphanumeric characters
 */
function validateAlphanumeric($string, $allowSpaces = false) {
    $pattern = $allowSpaces ? '/^[a-zA-Z0-9\s]+$/' : '/^[a-zA-Z0-9]+$/';
    return preg_match($pattern, $string);
}
?>
