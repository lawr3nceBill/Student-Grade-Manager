<?php
/**
 * Student Grade Manager - Grade Calculation Functions
 * 
 * This file contains all the functions related to grade calculations
 * and grade processing for the Student Grade Manager application.
 */

/**
 * Converts numerical grade to letter grade
 * 
 * @param float $grade The numerical grade (0-100)
 * @return string The letter grade (A, B, C, D, F)
 */
function getLetterGrade($grade) {
    if ($grade >= GRADE_A_MIN) return 'A';
    elseif ($grade >= GRADE_B_MIN) return 'B';
    elseif ($grade >= GRADE_C_MIN) return 'C';
    elseif ($grade >= GRADE_D_MIN) return 'D';
    else return 'F';
}

/**
 * Returns appropriate color for grade visualization
 * 
 * @param float $grade The numerical grade (0-100)
 * @return string The hex color code
 */
function getGradeColor($grade) {
    if ($grade >= GRADE_A_MIN) return '#4caf50';      // Green for A
    elseif ($grade >= GRADE_B_MIN) return '#8bc34a';  // Light green for B
    elseif ($grade >= GRADE_C_MIN) return '#ff9800';  // Orange for C
    elseif ($grade >= GRADE_D_MIN) return '#ff5722';  // Red-orange for D
    else return '#f44336';                   // Red for F
}

/**
 * Calculates grade statistics
 * 
 * @param array $students Array of student data
 * @return array Array containing calculated statistics
 */
function calculateGradeStatistics($students) {
    if (empty($students)) {
        return [
            'total_students' => 0,
            'average_grade' => 0,
            'highest_grade' => 0,
            'lowest_grade' => 0,
            'top_students' => [],
            'grade_distribution' => []
        ];
    }
    
    $grades = array_column($students, 'grade');
    $totalGrade = array_sum($grades);
    $average = round($totalGrade / count($students), 2);
    $highestGrade = max($grades);
    $lowestGrade = min($grades);
    
    // Find top students
    $topStudents = array_filter($students, function($student) use ($highestGrade) {
        return $student['grade'] == $highestGrade;
    });
    
    // Calculate grade distribution
    $gradeDistribution = [
        'A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'F' => 0
    ];
    
    foreach ($students as $student) {
        $letterGrade = getLetterGrade($student['grade']);
        $gradeDistribution[$letterGrade]++;
    }
    
    return [
        'total_students' => count($students),
        'average_grade' => $average,
        'highest_grade' => $highestGrade,
        'lowest_grade' => $lowestGrade,
        'top_students' => $topStudents,
        'grade_distribution' => $gradeDistribution
    ];
}

/**
 * Gets performance status for a student
 * 
 * @param float $grade The student's grade
 * @param float $highestGrade The highest grade in the class
 * @return array Array with status text and color
 */
function getPerformanceStatus($grade, $highestGrade) {
    if ($grade == $highestGrade) {
        return [
            'text' => '🥇 Top Performer',
            'color' => '#ffd700'
        ];
    } elseif ($grade >= GRADE_A_MIN) {
        return [
            'text' => '⭐ Excellent',
            'color' => '#4caf50'
        ];
    } elseif ($grade >= GRADE_B_MIN) {
        return [
            'text' => '👍 Good',
            'color' => '#8bc34a'
        ];
    } elseif ($grade >= GRADE_C_MIN) {
        return [
            'text' => '⚠️ Needs Improvement',
            'color' => '#ff9800'
        ];
    } else {
        return [
            'text' => '❌ Failing',
            'color' => '#f44336'
        ];
    }
}
?>
