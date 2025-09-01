// Student Grade Manager JavaScript
let studentCount = 1;
let validationErrors = {};

/**
 * Validates a student name on the client side
 * @param {string} name - The name to validate
 * @returns {object} Validation result
 */
function validateName(name) {
    const trimmedName = name.trim();
    
    if (trimmedName.length === 0) {
        return { valid: false, message: 'Name is required' };
    }
    
    if (trimmedName.length < 2) {
        return { valid: false, message: 'Name must be at least 2 characters' };
    }
    
    if (trimmedName.length > 50) {
        return { valid: false, message: 'Name cannot exceed 50 characters' };
    }
    
    if (!/^[a-zA-Z\s\'-]+$/.test(trimmedName)) {
        return { valid: false, message: 'Name can only contain letters, spaces, hyphens, and apostrophes' };
    }
    
    if (/\s{2,}/.test(trimmedName)) {
        return { valid: false, message: 'Name cannot contain consecutive spaces' };
    }
    
    return { valid: true, message: '' };
}

/**
 * Validates a grade on the client side
 * @param {string} grade - The grade to validate
 * @returns {object} Validation result
 */
function validateGrade(grade) {
    if (grade === '') {
        return { valid: false, message: 'Grade is required' };
    }
    
    if (!/^\d*\.?\d{0,2}$/.test(grade)) {
        return { valid: false, message: 'Grade must be a number with max 2 decimal places' };
    }
    
    const gradeValue = parseFloat(grade);
    
    if (isNaN(gradeValue)) {
        return { valid: false, message: 'Grade must be a valid number' };
    }
    
    if (gradeValue < 0) {
        return { valid: false, message: 'Grade cannot be negative' };
    }
    
    if (gradeValue > 100) {
        return { valid: false, message: 'Grade cannot exceed 100' };
    }
    
    return { valid: true, message: '' };
}

/**
 * Shows validation error message
 * @param {HTMLElement} input - The input element
 * @param {string} message - The error message
 */
function showError(input, message) {
    const fieldId = input.id;
    const errorDiv = document.getElementById(`${fieldId}-error`);
    
    if (errorDiv) {
        errorDiv.textContent = message;
        errorDiv.style.display = 'block';
    } else {
        // Create error div if it doesn't exist
        const newErrorDiv = document.createElement('div');
        newErrorDiv.id = `${fieldId}-error`;
        newErrorDiv.className = 'error-message';
        newErrorDiv.textContent = message;
        newErrorDiv.style.display = 'block';
        input.parentNode.appendChild(newErrorDiv);
    }
    
    input.classList.add('error-input');
    validationErrors[fieldId] = message;
}

/**
 * Hides validation error message
 * @param {HTMLElement} input - The input element
 */
function hideError(input) {
    const fieldId = input.id;
    const errorDiv = document.getElementById(`${fieldId}-error`);
    
    if (errorDiv) {
        errorDiv.style.display = 'none';
    }
    
    input.classList.remove('error-input');
    delete validationErrors[fieldId];
}

/**
 * Validates a single student entry
 * @param {HTMLElement} studentEntry - The student entry container
 * @returns {boolean} Whether the entry is valid
 */
function validateStudentEntry(studentEntry) {
    const nameInput = studentEntry.querySelector('input[name="names[]"]');
    const gradeInput = studentEntry.querySelector('input[name="grades[]"]');
    
    let isValid = true;
    
    // Validate name
    const nameValidation = validateName(nameInput.value);
    if (!nameValidation.valid) {
        showError(nameInput, nameValidation.message);
        isValid = false;
    } else {
        hideError(nameInput);
    }
    
    // Validate grade
    const gradeValidation = validateGrade(gradeInput.value);
    if (!gradeValidation.valid) {
        showError(gradeInput, gradeValidation.message);
        isValid = false;
    } else {
        hideError(gradeInput);
    }
    
    return isValid;
}

// /**
//  * Checks for duplicate names
//  * @returns {object} Duplicate check result
//  */
// function checkDuplicateNames() {
//     const nameInputs = document.querySelectorAll('input[name="names[]"]');
//     const names = [];
//     const duplicates = [];
    
//     nameInputs.forEach((input, index) => {
//         const name = input.value.trim().toLowerCase();
//         if (name && names.includes(name)) {
//             duplicates.push({ index, name: input.value.trim() });
//         } else if (name) {
//             names.push(name);
//         }
//     });
    
//     return { hasDuplicates: duplicates.length > 0, duplicates };
// }

/**
 * Validates the entire form
 * @returns {boolean} Whether the form is valid
 */
function validateForm() {
    const studentEntries = document.querySelectorAll('.student-entry');
    let isValid = true;
    
    // Clear previous validation errors
    validationErrors = {};
    document.querySelectorAll('.error-message').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.error-input').forEach(el => el.classList.remove('error-input'));
    
    // Validate each student entry
    studentEntries.forEach((entry, index) => {
        const nameInput = entry.querySelector('input[name="names[]"]');
        const gradeInput = entry.querySelector('input[name="grades[]"]');
        
        // Skip completely empty entries
        if (!nameInput.value.trim() && !gradeInput.value.trim()) {
            return;
        }
        
        if (!validateStudentEntry(entry)) {
            isValid = false;
        }
    });
    
    // // Check for duplicates
    // const duplicateCheck = checkDuplicateNames();
    // if (duplicateCheck.hasDuplicates) {
    //     duplicateCheck.duplicates.forEach(dup => {
    //         const nameInput = studentEntries[dup.index].querySelector('input[name="names[]"]');
    //         showError(nameInput, `Duplicate name: "${dup.name}"`);
    //         isValid = false;
    //     });
    // }
    
    // Check if at least one student is entered
    const hasValidStudents = Array.from(studentEntries).some(entry => {
        const nameInput = entry.querySelector('input[name="names[]"]');
        const gradeInput = entry.querySelector('input[name="grades[]"]');
        return nameInput.value.trim() && gradeInput.value.trim();
    });
    
    if (!hasValidStudents) {
        showFormError('Please enter at least one student name and grade.');
        isValid = false;
    } else {
        hideFormError();
    }
    
    return isValid;
}

/**
 * Shows form-level error message
 * @param {string} message - The error message
 */
function showFormError(message) {
    let errorDiv = document.getElementById('form-error');
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.id = 'form-error';
        errorDiv.className = 'form-error';
        const form = document.getElementById('gradeForm');
        form.insertBefore(errorDiv, form.firstChild);
    }
    errorDiv.textContent = message;
    errorDiv.style.display = 'block';
}

/**
 * Hides form-level error message
 */
function hideFormError() {
    const errorDiv = document.getElementById('form-error');
    if (errorDiv) {
        errorDiv.style.display = 'none';
    }
}

/**
 * Adds a new student entry to the form
 */
function addStudent() {
    if (studentCount >= 50) {
        alert('Maximum 50 students allowed per submission.');
        return;
    }
    
    studentCount++;
    const container = document.getElementById('studentsContainer');
    const newStudent = document.createElement('div');
    newStudent.className = 'student-entry';
    newStudent.innerHTML = `
        <div class="student-row">
            <div class="form-group">
                <label for="name${studentCount}">Student Name:</label>
                <input type="text" id="name${studentCount}" name="names[]" required placeholder="Enter student name" 
                       onblur="validateField(this, 'name')" oninput="clearFieldError(this)">
            </div>
            <div class="form-group">
                <label for="grade${studentCount}">Grade:</label>
                <input type="number" id="grade${studentCount}" name="grades[]" min="0" max="100" step="0.01" required 
                       placeholder="0-100" onblur="validateField(this, 'grade')" oninput="clearFieldError(this)">
            </div>
            <button type="button" class="btn btn-danger" onclick="removeStudent(this)">Remove</button>
        </div>
    `;
    container.appendChild(newStudent);
    
    // Add smooth animation
    newStudent.style.opacity = '0';
    newStudent.style.transform = 'translateY(-20px)';
    setTimeout(() => {
        newStudent.style.transition = 'all 0.3s ease';
        newStudent.style.opacity = '1';
        newStudent.style.transform = 'translateY(0)';
    }, 10);
}

/**
 * Removes a student entry from the form
 * @param {HTMLElement} button - The remove button element
 */
function removeStudent(button) {
    const studentEntry = button.closest('.student-entry');
    if (studentEntry) {
        // Add removal animation
        studentEntry.style.transition = 'all 0.3s ease';
        studentEntry.style.opacity = '0';
        studentEntry.style.transform = 'translateX(-100%)';
        
        setTimeout(() => {
            studentEntry.remove();
            // Revalidate form after removal
            validateForm();
        }, 300);
    }
}

/**
 * Validates a single field
 * @param {HTMLElement} input - The input element
 * @param {string} type - The type of validation ('name' or 'grade')
 */
function validateField(input, type) {
    const value = input.value.trim();
    
    if (type === 'name') {
        const validation = validateName(value);
        if (!validation.valid) {
            showError(input, validation.message);
        } else {
            hideError(input);
        }
    } else if (type === 'grade') {
        const validation = validateGrade(value);
        if (!validation.valid) {
            showError(input, validation.message);
        } else {
            hideError(input);
        }
    }
}

/**
 * Clears field error when user starts typing
 * @param {HTMLElement} input - The input element
 */
function clearFieldError(input) {
    hideError(input);
}

/**
 * Clears all form data and resets to initial state
 */
function clearForm() {
    const form = document.getElementById('gradeForm');
    if (form) {
        form.reset();
    }
    
    const container = document.getElementById('studentsContainer');
    if (container) {
        container.innerHTML = `
            <div class="student-entry">
                <div class="student-row">
                    <div class="form-group">
                        <label for="name1">Student Name:</label>
                        <input type="text" id="name1" name="names[]" required placeholder="Enter student name" 
                               onblur="validateField(this, 'name')" oninput="clearFieldError(this)">
                    </div>
                    <div class="form-group">
                        <label for="grade1">Grade:</label>
                        <input type="number" id="grade1" name="grades[]" min="0" max="100" step="0.01" required 
                               placeholder="0-100" onblur="validateField(this, 'grade')" oninput="clearFieldError(this)">
                    </div>
                    <button type="button" class="btn btn-danger" onclick="removeStudent(this)">Remove</button>
                </div>
            </div>
        `;
    }
    
    // Clear all error messages
    document.querySelectorAll('.error-message').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.error-input').forEach(el => el.classList.remove('error-input'));
    hideFormError();
    validationErrors = {};
    studentCount = 1;
}

/**
 * Handles form submission with validation
 * @param {Event} event - The form submission event
 */
function handleFormSubmit(event) {
    event.preventDefault();
    
    if (validateForm()) {
        // Show loading state
        const submitBtn = document.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Processing...';
        submitBtn.disabled = true;
        
        // Submit the form
        setTimeout(() => {
            document.getElementById('gradeForm').submit();
        }, 500);
    } else {
        // Scroll to first error
        const firstError = document.querySelector('.error-input');
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
}

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    // Add form submission handler
    const form = document.getElementById('gradeForm');
    if (form) {
        form.addEventListener('submit', handleFormSubmit);
    }
    
    // Add validation to initial student entry
    const initialNameInput = document.getElementById('name1');
    const initialGradeInput = document.getElementById('grade1');
    
    if (initialNameInput) {
        initialNameInput.addEventListener('blur', () => validateField(initialNameInput, 'name'));
        initialNameInput.addEventListener('input', () => clearFieldError(initialNameInput));
    }
    
    if (initialGradeInput) {
        initialGradeInput.addEventListener('blur', () => validateField(initialGradeInput, 'grade'));
        initialGradeInput.addEventListener('input', () => clearFieldError(initialGradeInput));
    }
    
    // Add keyboard shortcuts
    document.addEventListener('keydown', function(event) {
        // Ctrl/Cmd + Enter to submit form
        if ((event.ctrlKey || event.metaKey) && event.key === 'Enter') {
            event.preventDefault();
            handleFormSubmit(event);
        }
        
        // Ctrl/Cmd + N to add new student
        if ((event.ctrlKey || event.metaKey) && event.key === 'n') {
            event.preventDefault();
            addStudent();
        }
        
        // Ctrl/Cmd + R to clear form
        if ((event.ctrlKey || event.metaKey) && event.key === 'r') {
            event.preventDefault();
            clearForm();
        }
    });
    
    // Add real-time validation for existing fields
    document.querySelectorAll('input[name="names[]"]').forEach(input => {
        input.addEventListener('blur', () => validateField(input, 'name'));
        input.addEventListener('input', () => clearFieldError(input));
    });
    
    document.querySelectorAll('input[name="grades[]"]').forEach(input => {
        input.addEventListener('blur', () => validateField(input, 'grade'));
        input.addEventListener('input', () => clearFieldError(input));
    });
});
