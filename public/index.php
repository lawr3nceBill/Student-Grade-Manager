<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Grade Manager</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📚 Student Grade Manager</h1>
            <p>Enter student names and grades to calculate statistics</p>
        </div>
        
        <div class="content">
            <div class="grade-info">
                <p><strong>Grade Scale:</strong> 0-100 (A: 90-100, B: 80-89, C: 70-79, D: 60-69, F: 0-59)</p>
            </div>

            <form action="../src/controllers/process.php" method="POST" id="gradeForm">
                <div id="studentsContainer">
                    <div class="student-entry">
                        <div class="student-row">
                            <div class="form-group">
                                <label for="name1">Student Name:</label>
                                <input type="text" id="name1" name="names[]" required placeholder="Enter student name">
                            </div>
                            <div class="form-group">
                                <label for="grade1">Grade:</label>
                                <input type="number" id="grade1" name="grades[]" min="0" max="100" required placeholder="0-100">
                            </div>
                            <button type="button" class="btn btn-danger" onclick="removeStudent(this)">Remove</button>
                        </div>
                    </div>
                </div>

                <div class="add-student">
                    <button type="button" class="btn" onclick="addStudent()">➕ Add Another Student</button>
                </div>

                <div class="actions">
                    <button type="submit" class="btn btn-success">📊 Calculate Grades</button>
                    <button type="button" class="btn" onclick="clearForm()">🗑️ Clear All</button>
                </div>
            </form>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>
