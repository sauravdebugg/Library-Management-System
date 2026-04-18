<?php
// Edit Book Page
$protected = true;
include '../includes/header.php';
include '../includes/db.php';

// Get book ID
if (!isset($_GET['id'])) {
    header("Location: manage_books.php");
    exit();
}

$book_id = intval($_GET['id']);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $category = trim($_POST['category']);
    $isbn = trim($_POST['isbn']);
    $new_quantity = intval($_POST['quantity']);
    
    // Validate input
    if (empty($title) || empty($author) || empty($category) || empty($isbn) || empty($new_quantity)) {
        $_SESSION['error'] = "Please fill in all fields";
    } else {
        // Check if ISBN exists for another book
        $check_stmt = $conn->prepare("SELECT id FROM books WHERE isbn = ? AND id != ?");
        $check_stmt->bind_param("si", $isbn, $book_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $_SESSION['error'] = "Book with this ISBN already exists";
        } else {
            // Get current book data
            $current_stmt = $conn->prepare("SELECT quantity, available_quantity FROM books WHERE id = ?");
            $current_stmt->bind_param("i", $book_id);
            $current_stmt->execute();
            $current_result = $current_stmt->get_result();
            $current_book = $current_result->fetch_assoc();
            
            // Calculate new available quantity
            $issued_count = $current_book['quantity'] - $current_book['available_quantity'];
            $new_available_quantity = max(0, $new_quantity - $issued_count);
            
            // Update book using prepared statement
            $stmt = $conn->prepare("UPDATE books SET title = ?, author = ?, category = ?, isbn = ?, quantity = ?, available_quantity = ? WHERE id = ?");
            $stmt->bind_param("ssssiii", $title, $author, $category, $isbn, $new_quantity, $new_available_quantity, $book_id);
            
            if ($stmt->execute()) {
                $_SESSION['success'] = "Book updated successfully";
                header("Location: manage_books.php");
                exit();
            } else {
                $_SESSION['error'] = "Error updating book: " . $conn->error;
            }
            $stmt->close();
        }
        $check_stmt->close();
        $current_stmt->close();
    }
}

// Fetch book data
$stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $_SESSION['error'] = "Book not found";
    header("Location: manage_books.php");
    exit();
}

$book = $result->fetch_assoc();
$stmt->close();
?>

<!-- Edit Book Form -->
<div class="page-header">
    <h3><i class="bi bi-pencil"></i> Edit Book</h3>
</div>

<div class="form-container">
    <form method="POST" action="">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="title" class="form-label">Book Title</label>
                <input type="text" class="form-control" id="title" name="title" 
                       value="<?php echo htmlspecialchars($book['title']); ?>" required>
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="author" class="form-label">Author</label>
                <input type="text" class="form-control" id="author" name="author" 
                       value="<?php echo htmlspecialchars($book['author']); ?>" required>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="category" class="form-label">Category</label>
                <select class="form-select" id="category" name="category" required>
                    <option value="">Select Category</option>
                    <option value="Fiction" <?php echo $book['category'] == 'Fiction' ? 'selected' : ''; ?>>Fiction</option>
                    <option value="Non-Fiction" <?php echo $book['category'] == 'Non-Fiction' ? 'selected' : ''; ?>>Non-Fiction</option>
                    <option value="Science" <?php echo $book['category'] == 'Science' ? 'selected' : ''; ?>>Science</option>
                    <option value="Technology" <?php echo $book['category'] == 'Technology' ? 'selected' : ''; ?>>Technology</option>
                    <option value="History" <?php echo $book['category'] == 'History' ? 'selected' : ''; ?>>History</option>
                    <option value="Biography" <?php echo $book['category'] == 'Biography' ? 'selected' : ''; ?>>Biography</option>
                    <option value="Romance" <?php echo $book['category'] == 'Romance' ? 'selected' : ''; ?>>Romance</option>
                    <option value="Science Fiction" <?php echo $book['category'] == 'Science Fiction' ? 'selected' : ''; ?>>Science Fiction</option>
                    <option value="Mystery" <?php echo $book['category'] == 'Mystery' ? 'selected' : ''; ?>>Mystery</option>
                    <option value="Other" <?php echo $book['category'] == 'Other' ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="isbn" class="form-label">ISBN</label>
                <input type="text" class="form-control" id="isbn" name="isbn" 
                       value="<?php echo htmlspecialchars($book['isbn']); ?>" required>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="quantity" class="form-label">Quantity</label>
                <input type="number" class="form-control" id="quantity" name="quantity" 
                       value="<?php echo $book['quantity']; ?>" min="1" required>
                <small class="text-muted">Currently issued: <?php echo $book['quantity'] - $book['available_quantity']; ?></small>
            </div>
        </div>
        
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> Update Book
            </button>
            <a href="manage_books.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Books
            </a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
