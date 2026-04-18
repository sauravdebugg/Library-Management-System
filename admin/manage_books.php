<?php
// Manage Books Page
$protected = true;
include '../includes/header.php';
include '../includes/db.php';

// Handle delete action
if (isset($_GET['delete'])) {
    $book_id = intval($_GET['delete']);
    
    // Check if book is issued to any student
    $check_stmt = $conn->prepare("SELECT id FROM issued_books WHERE book_id = ? AND status = 'issued'");
    $check_stmt->bind_param("i", $book_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $_SESSION['error'] = "Cannot delete book. It is currently issued to a student.";
    } else {
        $stmt = $conn->prepare("DELETE FROM books WHERE id = ?");
        $stmt->bind_param("i", $book_id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Book deleted successfully";
        } else {
            $_SESSION['error'] = "Error deleting book: " . $conn->error;
        }
        $stmt->close();
    }
    $check_stmt->close();
    header("Location: manage_books.php");
    exit();
}

// Search functionality
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Fetch books
if (!empty($search)) {
    $stmt = $conn->prepare("SELECT * FROM books WHERE title LIKE ? OR author LIKE ? OR isbn LIKE ? ORDER BY added_date DESC");
    $search_param = "%$search%";
    $stmt->bind_param("sss", $search_param, $search_param, $search_param);
} else {
    $stmt = $conn->prepare("SELECT * FROM books ORDER BY added_date DESC");
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!-- Manage Books Page -->
<div class="page-header">
    <h3><i class="bi bi-book"></i> Manage Books</h3>
</div>

<div class="action-bar">
    <form class="search-box" method="GET" action="">
        <div class="input-group">
            <input type="text" class="form-control" name="search" 
                   placeholder="Search books..." value="<?php echo htmlspecialchars($search); ?>">
            <button class="btn btn-primary" type="submit">
                <i class="bi bi-search"></i>
            </button>
            <?php if (!empty($search)): ?>
                <a href="manage_books.php" class="btn btn-secondary">
                    <i class="bi bi-x"></i>
                </a>
            <?php endif; ?>
        </div>
    </form>
    
    <a href="add_book.php" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add New Book
    </a>
</div>

<div class="table-container">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Category</th>
                    <th>ISBN</th>
                    <th>Quantity</th>
                    <th>Available</th>
                    <th>Added Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><?php echo htmlspecialchars($row['author']); ?></td>
                            <td><?php echo htmlspecialchars($row['category']); ?></td>
                            <td><?php echo htmlspecialchars($row['isbn']); ?></td>
                            <td><?php echo $row['quantity']; ?></td>
                            <td>
                                <span class="badge <?php echo $row['available_quantity'] > 0 ? 'bg-success' : 'bg-danger'; ?>">
                                    <?php echo $row['available_quantity']; ?>
                                </span>
                            </td>
                            <td><?php echo date('Y-m-d', strtotime($row['added_date'])); ?></td>
                            <td>
                                <a href="edit_book.php?id=<?php echo $row['id']; ?>" 
                                   class="btn btn-sm btn-warning btn-action">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="manage_books.php?delete=<?php echo $row['id']; ?>" 
                                   class="btn btn-sm btn-danger btn-action"
                                   onclick="return confirm('Are you sure you want to delete this book?');">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center">
                            <?php if (!empty($search)): ?>
                                No books found matching your search.
                            <?php else: ?>
                                No books added yet. <a href="add_book.php">Add your first book</a>.
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
