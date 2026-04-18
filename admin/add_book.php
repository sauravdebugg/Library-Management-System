<?php
// Add Book Page
$protected = true;
include '../includes/header.php';
include '../includes/db.php';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $category = trim($_POST['category']);
    $isbn = trim($_POST['isbn']);
    $quantity = intval($_POST['quantity']);
    
    // Validate input
    if (empty($title) || empty($author) || empty($category) || empty($isbn) || empty($quantity)) {
        $_SESSION['error'] = "Please fill in all fields";
    } else {
        // Check if ISBN already exists
        $check_stmt = $conn->prepare("SELECT id FROM books WHERE isbn = ?");
        $check_stmt->bind_param("s", $isbn);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $_SESSION['error'] = "Book with this ISBN already exists";
        } else {
            // Insert book using prepared statement
            $stmt = $conn->prepare("INSERT INTO books (title, author, category, isbn, quantity, available_quantity) VALUES (?, ?, ?, ?, ?, ?)");
            $available_quantity = $quantity;
            $stmt->bind_param("ssssii", $title, $author, $category, $isbn, $quantity, $available_quantity);
            
            if ($stmt->execute()) {
                $_SESSION['success'] = "Book added successfully";
                header("Location: manage_books.php");
                exit();
            } else {
                $_SESSION['error'] = "Error adding book: " . $conn->error;
            }
            $stmt->close();
        }
        $check_stmt->close();
    }
}
?>

<!-- Add Book Form -->
<div class="page-header">
    <h3><i class="bi bi-plus-circle"></i> Add New Book</h3>
</div>

<div class="form-container">
    <form method="POST" action="">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="title" class="form-label">Book Title</label>
                <input type="text" class="form-control" id="title" name="title" 
                       placeholder="Enter book title" required>
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="author" class="form-label">Author</label>
                <input type="text" class="form-control" id="author" name="author" 
                       placeholder="Enter author name" required>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="category" class="form-label">Category</label>
                <select class="form-select" id="category" name="category" required>
                    <option value="">Select Category</option>
                    <option value="Fiction">Fiction</option>
                    <option value="Non-Fiction">Non-Fiction</option>
                    <option value="Science">Science</option>
                    <option value="Technology">Technology</option>
                    <option value="History">History</option>
                    <option value="Biography">Biography</option>
                    <option value="Romance">Romance</option>
                    <option value="Science Fiction">Science Fiction</option>
                    <option value="Mystery">Mystery</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="isbn" class="form-label">ISBN</label>
                <input type="text" class="form-control" id="isbn" name="isbn" 
                       placeholder="Enter ISBN number" required>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="quantity" class="form-label">Quantity</label>
                <input type="number" class="form-control" id="quantity" name="quantity" 
                       placeholder="Enter quantity" min="1" required>
            </div>
        </div>
        
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> Add Book
            </button>
            <a href="manage_books.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Books
            </a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
