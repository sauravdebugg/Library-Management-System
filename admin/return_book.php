<?php
// Return Book Page
$protected = true;
include '../includes/header.php';
include '../includes/db.php';

// Handle return action
if (isset($_GET['return'])) {
    $issue_id = intval($_GET['return']);
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Get book_id from issued_books
        $get_stmt = $conn->prepare("SELECT book_id FROM issued_books WHERE id = ? AND status = 'issued'");
        $get_stmt->bind_param("i", $issue_id);
        $get_stmt->execute();
        $get_result = $get_stmt->get_result();
        
        if ($get_result->num_rows == 0) {
            $_SESSION['error'] = "Invalid issue record or book already returned";
            $conn->rollback();
        } else {
            $issue = $get_result->fetch_assoc();
            $book_id = $issue['book_id'];
            
            // Update issued_books status
            $update_stmt = $conn->prepare("UPDATE issued_books SET status = 'returned' WHERE id = ?");
            $update_stmt->bind_param("i", $issue_id);
            $update_stmt->execute();
            
            // Increase available quantity
            $book_stmt = $conn->prepare("UPDATE books SET available_quantity = available_quantity + 1 WHERE id = ?");
            $book_stmt->bind_param("i", $book_id);
            $book_stmt->execute();
            
            // Commit transaction
            $conn->commit();
            
            $_SESSION['success'] = "Book returned successfully";
        }
        
        $get_stmt->close();
        $update_stmt->close();
        $book_stmt->close();
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        $_SESSION['error'] = "Error returning book: " . $e->getMessage();
    }
    
    header("Location: return_book.php");
    exit();
}

// Fetch issued books (not returned)
$stmt = $conn->prepare("SELECT ib.id, ib.issue_date, ib.return_date as expected_return, 
                        b.title, b.author, s.name as student_name, s.email as student_email
                        FROM issued_books ib
                        JOIN books b ON ib.book_id = b.id
                        JOIN students s ON ib.student_id = s.id
                        WHERE ib.status = 'issued'
                        ORDER BY ib.issue_date DESC");
$stmt->execute();
$result = $stmt->get_result();
?>

<!-- Return Book Page -->
<div class="page-header">
    <h3><i class="bi bi-box-arrow-in-left"></i> Return Book</h3>
</div>

<div class="table-container">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Book Title</th>
                    <th>Author</th>
                    <th>Student Name</th>
                    <th>Student Email</th>
                    <th>Issue Date</th>
                    <th>Expected Return</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): 
                        $expected_return = strtotime($row['expected_return']);
                        $today = strtotime(date('Y-m-d'));
                        $is_overdue = $today > $expected_return;
                    ?>
                        <tr class="<?php echo $is_overdue ? 'table-warning' : ''; ?>">
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><?php echo htmlspecialchars($row['author']); ?></td>
                            <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['student_email']); ?></td>
                            <td><?php echo date('Y-m-d', strtotime($row['issue_date'])); ?></td>
                            <td><?php echo date('Y-m-d', strtotime($row['expected_return'])); ?></td>
                            <td>
                                <span class="badge <?php echo $is_overdue ? 'bg-danger' : 'bg-warning text-dark'; ?>">
                                    <?php echo $is_overdue ? 'Overdue' : 'Issued'; ?>
                                </span>
                            </td>
                            <td>
                                <a href="return_book.php?return=<?php echo $row['id']; ?>" 
                                   class="btn btn-sm btn-success btn-action"
                                   onclick="return confirm('Are you sure you want to mark this book as returned?');">
                                    <i class="bi bi-check-circle"></i> Return
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center">
                            No books currently issued. <a href="issue_book.php">Issue a book</a>.
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
