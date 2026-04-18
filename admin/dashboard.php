<?php
// Admin Dashboard
$protected = true;
include '../includes/header.php';
include '../includes/db.php';

// Get statistics
// Total books
$total_books_query = "SELECT SUM(quantity) as total FROM books";
$total_books_result = $conn->query($total_books_query);
$total_books = $total_books_result->fetch_assoc()['total'] ?? 0;

// Available books
$available_books_query = "SELECT SUM(available_quantity) as available FROM books";
$available_books_result = $conn->query($available_books_query);
$available_books = $available_books_result->fetch_assoc()['available'] ?? 0;

// Total students
$total_students_query = "SELECT COUNT(*) as total FROM students";
$total_students_result = $conn->query($total_students_query);
$total_students = $total_students_result->fetch_assoc()['total'];

// Issued books count
$issued_books_query = "SELECT COUNT(*) as total FROM issued_books WHERE status = 'issued'";
$issued_books_result = $conn->query($issued_books_query);
$issued_books = $issued_books_result->fetch_assoc()['total'];

// Returned books count
$returned_books_query = "SELECT COUNT(*) as total FROM issued_books WHERE status = 'returned'";
$returned_books_result = $conn->query($returned_books_query);
$returned_books = $returned_books_result->fetch_assoc()['total'];
?>

<!-- Dashboard Statistics -->
<div class="page-header">
    <h3><i class="bi bi-speedometer2"></i> Dashboard</h3>
</div>

<div class="row">
    <!-- Total Books -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card bg-primary-custom text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="stat-label mb-0">Total Books</p>
                        <h2 class="stat-number"><?php echo $total_books; ?></h2>
                    </div>
                    <div class="card-icon">
                        <i class="bi bi-book"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Available Books -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card bg-success-custom text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="stat-label mb-0">Available Books</p>
                        <h2 class="stat-number"><?php echo $available_books; ?></h2>
                    </div>
                    <div class="card-icon">
                        <i class="bi bi-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Students -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card bg-info-custom text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="stat-label mb-0">Total Students</p>
                        <h2 class="stat-number"><?php echo $total_students; ?></h2>
                    </div>
                    <div class="card-icon">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Issued Books -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card bg-warning-custom text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="stat-label mb-0">Issued Books</p>
                        <h2 class="stat-number"><?php echo $issued_books; ?></h2>
                    </div>
                    <div class="card-icon">
                        <i class="bi bi-box-arrow-right"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Returned Books -->
    <div class="col-xl-6 col-md-6 mb-4">
        <div class="card stat-card bg-danger-custom text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="stat-label mb-0">Returned Books</p>
                        <h2 class="stat-number"><?php echo $returned_books; ?></h2>
                    </div>
                    <div class="card-icon">
                        <i class="bi bi-box-arrow-in-left"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-xl-6 col-md-6 mb-4">
        <div class="card stat-card bg-dark text-white">
            <div class="card-body">
                <h5 class="card-title mb-3"><i class="bi bi-lightning"></i> Quick Actions</h5>
                <div class="d-flex flex-wrap gap-2">
                    <a href="add_book.php" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-circle"></i> Add Book
                    </a>
                    <a href="add_student.php" class="btn btn-success btn-sm">
                        <i class="bi bi-person-plus"></i> Add Student
                    </a>
                    <a href="issue_book.php" class="btn btn-warning btn-sm">
                        <i class="bi bi-box-arrow-right"></i> Issue Book
                    </a>
                    <a href="return_book.php" class="btn btn-info btn-sm">
                        <i class="bi bi-box-arrow-in-left"></i> Return Book
                    </a>
                    <a href="reports.php" class="btn btn-secondary btn-sm">
                        <i class="bi bi-file-text"></i> Reports
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-clock-history"></i> Welcome to Library Management System</h5>
            </div>
            <div class="card-body">
                <p class="mb-0">Use the sidebar navigation to manage books, students, and track book issues/returns. 
                The dashboard shows real-time statistics of your library.</p>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
