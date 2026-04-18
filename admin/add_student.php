<?php
// Add Student Page
$protected = true;
include '../includes/header.php';
include '../includes/db.php';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    
    // Validate input
    if (empty($name) || empty($email) || empty($phone)) {
        $_SESSION['error'] = "Please fill in all fields";
    } else {
        // Check if email already exists
        $check_stmt = $conn->prepare("SELECT id FROM students WHERE email = ?");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $_SESSION['error'] = "Student with this email already exists";
        } else {
            // Insert student using prepared statement
            $stmt = $conn->prepare("INSERT INTO students (name, email, phone) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $phone);
            
            if ($stmt->execute()) {
                $_SESSION['success'] = "Student added successfully";
                header("Location: manage_students.php");
                exit();
            } else {
                $_SESSION['error'] = "Error adding student: " . $conn->error;
            }
            $stmt->close();
        }
        $check_stmt->close();
    }
}
?>

<!-- Add Student Form -->
<div class="page-header">
    <h3><i class="bi bi-person-plus"></i> Add New Student</h3>
</div>

<div class="form-container">
    <form method="POST" action="">
        <div class="mb-3">
            <label for="name" class="form-label">Student Name</label>
            <input type="text" class="form-control" id="name" name="name" 
                   placeholder="Enter student name" required>
        </div>
        
        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" class="form-control" id="email" name="email" 
                   placeholder="Enter email address" required>
        </div>
        
        <div class="mb-3">
            <label for="phone" class="form-label">Phone Number</label>
            <input type="tel" class="form-control" id="phone" name="phone" 
                   placeholder="Enter phone number" required>
        </div>
        
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> Add Student
            </button>
            <a href="manage_students.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Students
            </a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
