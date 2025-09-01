<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grade Results - Student Grade Manager</title>
    <link rel="stylesheet" href="../../public/assets/css/styles.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 Grade Results</h1>
            <p>Analysis of student performance</p>
        </div>
        
        <div class="content">
            <?php if (!empty($errors)): ?>
                <div class="error">
                    <strong>Error:</strong> <?php echo implode('<br>', $errors); ?>
                </div>
                <div class="actions">
                    <a href="../../public/index.php" class="btn">← Back to Input Form</a>
                </div>
            <?php elseif (!empty($students)): ?>
                <!-- Statistics Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>📚 Total Students</h3>
                        <div class="stat-value"><?php echo $statistics['total_students']; ?></div>
                        <div class="stat-subtitle">enrolled</div>
                    </div>
                    
                    <div class="stat-card">
                        <h3>📈 Average Grade</h3>
                        <div class="stat-value"><?php echo $statistics['average_grade']; ?>%</div>
                        <div class="stat-subtitle">class average</div>
                    </div>
                    
                    <div class="stat-card">
                        <h3>🏆 Highest Grade</h3>
                        <div class="stat-value"><?php echo $statistics['highest_grade']; ?>%</div>
                        <div class="stat-subtitle">achieved by <?php echo count($statistics['top_students']); ?> student(s)</div>
                    </div>
                </div>

                <!-- Top Students Highlight -->
                <?php if (!empty($statistics['top_students'])): ?>
                    <div class="highlight">
                        <h3>🏆 Top Performing Student(s)</h3>
                        <p>
                            <?php 
                            $topNames = array_map(function($student) {
                                return htmlspecialchars($student['name']);
                            }, $statistics['top_students']);
                            echo implode(', ', $topNames);
                            ?>
                            with a grade of <?php echo $statistics['highest_grade']; ?>%
                        </p>
                    </div>
                <?php endif; ?>

                <!-- Students Table -->
                <h2 class="section-title">📋 Student Grade Report</h2>
                <table class="students-table">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Grade (%)</th>
                            <th>Letter Grade</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                            <?php 
                            $isTopStudent = ($student['grade'] == $statistics['highest_grade']);
                            $performanceStatus = getPerformanceStatus($student['grade'], $statistics['highest_grade']);
                            ?>
                            <tr <?php echo $isTopStudent ? 'class="top-student"' : ''; ?>>
                                <td><?php echo htmlspecialchars($student['name']); ?></td>
                                <td><?php echo $student['grade']; ?>%</td>
                                <td>
                                    <span class="grade-badge" style="background-color: <?php echo getGradeColor($student['grade']); ?>">
                                        <?php echo getLetterGrade($student['grade']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span style="color: <?php echo $performanceStatus['color']; ?>; font-weight: bold;">
                                        <?php echo $performanceStatus['text']; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Actions -->
                <div class="actions">
                    <a href="../../public/index.php" class="btn">➕ Add More Students</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Load JavaScript from correct path -->
    <script src="../../public/assets/js/script.js"></script>
</body>
</html>
