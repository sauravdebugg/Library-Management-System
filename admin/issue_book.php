<?php
// Issue Book Page
$protected = true;
include '../includes/header.php';
include '../includes/db.php';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $book_id = intval($_POST['book_id']);
    $student_id = intval($_POST['student_id']);
    $issue_date = $_POST['issue_date'];
    $return_date = $_POST['return_date'];
    
    // Validate input
    if (empty($book_id) || empty($student_id) || empty($issue_date) || empty($return_date)) {
        $_SESSION['error'] = "Please fill in all fields";
    } else {
        // Check if book is available
        $check_stmt = $conn->prepare("SELECT available_quantity FROM books WHERE id = ?");
        $check_stmt->bind_param("i", $book_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        $book = $check_result->fetch_assoc();
        
        if ($book['available_quantity'] <= 0) {
            $_SESSION['error'] = "This book is not available for issue";
        } else {
            // Start transaction
            $conn->begin_transaction();
            
            try {
                // Insert into issued_books
                $stmt = $conn->prepare("INSERT INTO issued_books (book_id, student_id, issue_date, return_date, status) VALUES (?, ?, ?, ?, 'issued')");
                $stmt->bind_param("iiss", $book_id, $student_id, $issue_date, $return_date);
                $stmt->execute();
                
                // Decrease available quantity
                $update_stmt = $conn->prepare("UPDATE books SET available_quantity = available_quantity - 1 WHERE id = ?");
                $update_stmt->bind_param("i", $book_id);
                $update_stmt->execute();
                
                // Commit transaction
                $conn->commit();
                
                $_SESSION['success'] = "Book issued successfully";
                header("Location: return_book.php");
                exit();
            } catch (Exception $e) {
                // Rollback on error
                $conn->rollback();
                $_SESSION['error'] = "Error issuing book: " . $e->getMessage();
            }
            
            $stmt->close();
            $update_stmt->close();
        }
        $check_stmt->close();
    }
}

// Fetch available books
$books_stmt = $conn->prepare("SELECT id, title, author, available_quantity FROM books WHERE available_quantity > 0 ORDER BY title ASC");
$books_stmt->execute();
$books_result = $books_stmt->get_result();

// Fetch students
$students_stmt = $conn->prepare("SELECT id, name, email FROM students ORDER BY name ASC");
$students_stmt->execute();
$students_result = $students_stmt->get_result();
?>

<!-- Issue Book Form -->
<div class="page-header">
    <h3><i class="bi bi-box-arrow-right"></i> Issue Book to Student</h3>
</div>

<div class="form-container">
    <form method="POST" action="">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="student_id" class="form-label">Select Student</label>
                <select class="form-select" id="student_id" name="student_id" required>
                    <option value="">-- Select Student --</option>
                    <?php if ($students_result->num_rows > 0): ?>
                        <?php while ($student = $students_result->fetch_assoc()): ?>
                            <option value="<?php echo $student['id']; ?>">
                                <?php echo htmlspecialchars($student['name'] . ' (' . $student['email'] . ')'); ?>
                            </option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="book_id" class="form-label">Select Book</label>
                <select class="form-select" id="book_id" name="book_id" required>
                    <option value="">-- Select Book --</option>
                    <?php if ($books_result->num_rows > 0): ?>
                        <?php while ($book = $books_result->fetch_assoc()): ?>
                            <option value="<?php echo $book['id']; ?>">
                                <?php echo htmlspecialchars($book['title'] . ' by ' . $book['author'] . ' (Available: ' . $book['available_quantity'] . ')'); ?>
                            </option>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <option value="">No books available</option>
                    <?php endif; ?>
                </select>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="issue_date" class="form-label">Issue Date</label>
                <input type="date" class="form-control" id="issue_date" name="issue_date" 
                       value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="return_date" class="form-label">Expected Return Date</label>
                <input type="date" class="form-control" id="return_date" name="return_date" required>
            </div>
        </div>
        
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-box-arrow-right"></i> Issue Book
            </button>
            <a href="dashboard.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </form>
</div>

<script>
// Set minimum return date to issue date
document.getElementById('issue_date').addEventListener('change', function() {
    document.getElementById('return_date').min = this.value;
});
</script>

<?php 
$books_stmt->close();
$students_stmt->close();
include '../includes/footer.php'; 
?>
