<?php
// Manage Students Page
$protected = true;
include '../includes/header.php';
include '../includes/db.php';

// Handle delete action
if (isset($_GET['delete'])) {
    $student_id = intval($_GET['delete']);
    
    // Check if student has issued books
    $check_stmt = $conn->prepare("SELECT id FROM issued_books WHERE student_id = ? AND status = 'issued'");
    $check_stmt->bind_param("i", $student_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $_SESSION['error'] = "Cannot delete student. They have books currently issued.";
    } else {
        $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
        $stmt->bind_param("i", $student_id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Student deleted successfully";
        } else {
            $_SESSION['error'] = "Error deleting student: " . $conn->error;
        }
        $stmt->close();
    }
    $check_stmt->close();
    header("Location: manage_students.php");
    exit();
}

// Search functionality
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Fetch students
if (!empty($search)) {
    $stmt = $conn->prepare("SELECT * FROM students WHERE name LIKE ? OR email LIKE ? OR phone LIKE ? ORDER BY created_at DESC");
    $search_param = "%$search%";
    $stmt->bind_param("sss", $search_param, $search_param, $search_param);
} else {
    $stmt = $conn->prepare("SELECT * FROM students ORDER BY created_at DESC");
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!-- Manage Students Page -->
<div class="page-header">
    <h3><i class="bi bi-people"></i> Manage Students</h3>
</div>

<div class="action-bar">
    <form class="search-box" method="GET" action="">
        <div class="input-group">
            <input type="text" class="form-control" name="search" 
                   placeholder="Search students..." value="<?php echo htmlspecialchars($search); ?>">
            <button class="btn btn-primary" type="submit">
                <i class="bi bi-search"></i>
            </button>
            <?php if (!empty($search)): ?>
                <a href="manage_students.php" class="btn btn-secondary">
                    <i class="bi bi-x"></i>
                </a>
            <?php endif; ?>
        </div>
    </form>
    
    <a href="add_student.php" class="btn btn-primary">
        <i class="bi bi-person-plus"></i> Add New Student
    </a>
</div>

<div class="table-container">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Registered Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['phone']); ?></td>
                            <td><?php echo date('Y-m-d', strtotime($row['created_at'])); ?></td>
                            <td>
                                <a href="edit_student.php?id=<?php echo $row['id']; ?>" 
                                   class="btn btn-sm btn-warning btn-action">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="manage_students.php?delete=<?php echo $row['id']; ?>" 
                                   class="btn btn-sm btn-danger btn-action"
                                   onclick="return confirm('Are you sure you want to delete this student?');">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">
                            <?php if (!empty($search)): ?>
                                No students found matching your search.
                            <?php else: ?>
                                No students added yet. <a href="add_student.php">Add your first student</a>.
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php 
$stmt->close();
include '../includes/footer.php'; 
?>
