<?php
// Reports Page
$protected = true;
include '../includes/header.php';
include '../includes/db.php';

// Get filter type
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'issued';

// Fetch data based on filter
if ($filter == 'issued') {
    $stmt = $conn->prepare("SELECT ib.id, ib.issue_date, ib.return_date as expected_return,
                            b.title, b.author, s.name as student_name, s.email as student_email
                            FROM issued_books ib
                            JOIN books b ON ib.book_id = b.id
                            JOIN students s ON ib.student_id = s.id
                            WHERE ib.status = 'issued'
                            ORDER BY ib.issue_date DESC");
    $title = "Currently Issued Books";
} elseif ($filter == 'returned') {
    $stmt = $conn->prepare("SELECT ib.id, ib.issue_date, ib.return_date as expected_return,
                            b.title, b.author, s.name as student_name, s.email as student_email
                            FROM issued_books ib
                            JOIN books b ON ib.book_id = b.id
                            JOIN students s ON ib.student_id = s.id
                            WHERE ib.status = 'returned'
                            ORDER BY ib.issue_date DESC");
    $title = "Returned Books";
} elseif ($filter == 'overdue') {
    $stmt = $conn->prepare("SELECT ib.id, ib.issue_date, ib.return_date as expected_return,
                            b.title, b.author, s.name as student_name, s.email as student_email
                            FROM issued_books ib
                            JOIN books b ON ib.book_id = b.id
                            JOIN students s ON ib.student_id = s.id
                            WHERE ib.status = 'issued' AND ib.return_date < CURDATE()
                            ORDER BY ib.return_date ASC");
    $title = "Overdue Books";
} else {
    $stmt = $conn->prepare("SELECT ib.id, ib.issue_date, ib.return_date as expected_return,
                            b.title, b.author, s.name as student_name, s.email as student_email, ib.status
                            FROM issued_books ib
                            JOIN books b ON ib.book_id = b.id
                            JOIN students s ON ib.student_id = s.id
                            ORDER BY ib.issue_date DESC");
    $title = "All Transactions";
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!-- Reports Page -->
<div class="page-header">
    <h3><i class="bi bi-file-text"></i> Reports</h3>
</div>

<!-- Filter Buttons -->
<div class="mb-4">
    <div class="btn-group" role="group">
        <a href="reports.php?filter=issued" class="btn <?php echo $filter == 'issued' ? 'btn-primary' : 'btn-outline-primary'; ?>">
            <i class="bi bi-box-arrow-right"></i> Issued Books
        </a>
        <a href="reports.php?filter=returned" class="btn <?php echo $filter == 'returned' ? 'btn-primary' : 'btn-outline-primary'; ?>">
            <i class="bi bi-box-arrow-in-left"></i> Returned Books
        </a>
        <a href="reports.php?filter=overdue" class="btn <?php echo $filter == 'overdue' ? 'btn-primary' : 'btn-outline-primary'; ?>">
            <i class="bi bi-exclamation-triangle"></i> Overdue Books
        </a>
        <a href="reports.php?filter=all" class="btn <?php echo $filter == 'all' ? 'btn-primary' : 'btn-outline-primary'; ?>">
            <i class="bi bi-list"></i> All Transactions
        </a>
    </div>
</div>

<div class="table-container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5><?php echo $title; ?></h5>
        <span class="badge bg-info">Total: <?php echo $result->num_rows; ?></span>
    </div>
    
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
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): 
                        $expected_return = strtotime($row['expected_return']);
                        $today = strtotime(date('Y-m-d'));
                        $is_overdue = $today > $expected_return && ($filter == 'issued' || $filter == 'overdue' || $filter == 'all');
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
                                <?php if ($filter == 'all'): ?>
                                    <span class="badge <?php echo $row['status'] == 'issued' ? 'bg-warning text-dark' : 'bg-success'; ?>">
                                        <?php echo ucfirst($row['status']); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge <?php echo $is_overdue ? 'bg-danger' : 'bg-warning text-dark'; ?>">
                                        <?php echo $is_overdue ? 'Overdue' : ucfirst($filter); ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">
                            No records found.
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
