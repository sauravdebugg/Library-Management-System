<?php
// Edit Student Page
$protected = true;
include '../includes/header.php';
include '../includes/db.php';

// Get student ID
if (!isset($_GET['id'])) {
    header("Location: manage_students.php");
    exit();
}

$student_id = intval($_GET['id']);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    
    // Validate input
    if (empty($name) || empty($email) || empty($phone)) {
        $_SESSION['error'] = "Please fill in all fields";
    } else {
        // Check if email exists for another student
        $check_stmt = $conn->prepare("SELECT id FROM students WHERE email = ? AND id != ?");
        $check_stmt->bind_param("si", $email, $student_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $_SESSION['error'] = "Student with this email already exists";
        } else {
            // Update student using prepared statement
            $stmt = $conn->prepare("UPDATE students SET name = ?, email = ?, phone = ? WHERE id = ?");
            $stmt->bind_param("sssi", $name, $email, $phone, $student_id);
            
            if ($stmt->execute()) {
                $_SESSION['success'] = "Student updated successfully";
                header("Location: manage_students.php");
                exit();
            } else {
                $_SESSION['error'] = "Error updating student: " . $conn->error;
            }
            $stmt->close();
        }
        $check_stmt->close();
    }
}

// Fetch student data
$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $_SESSION['error'] = "Student not found";
    header("Location: manage_students.php");
    exit();
}

$student = $result->fetch_assoc();
$stmt->close();
?>

<!-- Edit Student Form -->
<div class="page-header">
    <h3><i class="bi bi-pencil"></i> Edit Student</h3>
</div>

<div class="form-container">
    <form method="POST" action="">
        <div class="mb-3">
            <label for="name" class="form-label">Student Name</label>
            <input type="text" class="form-control" id="name" name="name" 
                   value="<?php echo htmlspecialchars($student['name']); ?>" required>
        </div>
        
        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" class="form-control" id="email" name="email" 
                   value="<?php echo htmlspecialchars($student['email']); ?>" required>
        </div>
        
        <div class="mb-3">
            <label for="phone" class="form-label">Phone Number</label>
            <input type="tel" class="form-control" id="phone" name="phone" 
                   value="<?php echo htmlspecialchars($student['phone']); ?>" required>
        </div>
        
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> Update Student
            </button>
            <a href="manage_students.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Students
            </a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
