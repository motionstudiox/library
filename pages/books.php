<?php
require_once __DIR__ . '/../includes/header.php';

$pageTitle = 'Books Catalog';
$userId = SessionManager::getCurrentUserId();

$search = sanitizeInput($_GET['search'] ?? '');
$genre = sanitizeInput($_GET['genre'] ?? '');
$page = (int)($_GET['page'] ?? 1);
$page = max(1, $page);

// Build query
$where = "1=1";
if (!empty($search)) {
    $search_sql = sanitizeSQL($search);
    $where .= " AND (title LIKE '%$search_sql%' OR author LIKE '%$search_sql%' OR isbn LIKE '%$search_sql%')";
}
if (!empty($genre)) {
    $genre_sql = sanitizeSQL($genre);
    $where .= " AND genre = '$genre_sql'";
}

// Get total count
$countResult = $db->query("SELECT COUNT(*) as count FROM books WHERE $where");
$totalRecords = $countResult->fetch_assoc()['count'];

// Pagination
$recordsPerPage = 10;
$paginationInfo = getPaginationInfo($page, $totalRecords, $recordsPerPage);
$offset = $paginationInfo['offset'];

// Get books
$booksResult = $db->query("
    SELECT * FROM books 
    WHERE $where 
    ORDER BY title ASC 
    LIMIT $offset, $recordsPerPage
");

// Get genres for filter
$genresResult = $db->query("SELECT DISTINCT genre FROM books WHERE genre IS NOT NULL ORDER BY genre");
?>

<div class="row mb-4">
    <div class="col-md-8">
        <h2><i class="fas fa-book-open"></i> Book Catalog</h2>
    </div>
</div>

<!-- Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="Search by title, author, or ISBN..." 
                       value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-4">
                <select name="genre" class="form-select">
                    <option value="">All Genres</option>
                    <?php while ($g = $genresResult->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($g['genre']); ?>" 
                                <?php echo $genre === $g['genre'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($g['genre']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i> Search
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Books List -->
<div class="row">
    <?php if ($booksResult->num_rows > 0): ?>
        <?php while ($book = $booksResult->fetch_assoc()): ?>
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h5>
                        <p class="card-text text-muted">by <?php echo htmlspecialchars($book['author']); ?></p>
                        
                        <?php if ($book['genre']): ?>
                            <span class="badge bg-info"><?php echo htmlspecialchars($book['genre']); ?></span>
                        <?php endif; ?>
                        
                        <p class="card-text mt-2"><?php echo htmlspecialchars(substr($book['description'], 0, 100)) . '...'; ?></p>
                        
                        <div class="row text-center mb-3">
                            <div class="col">
                                <small class="text-muted">Available</small><br>
                                <strong><?php echo $book['available_copies']; ?>/<?php echo $book['total_copies']; ?></strong>
                            </div>
                            <div class="col">
                                <small class="text-muted">Year</small><br>
                                <strong><?php echo $book['publication_year'] ?? 'N/A'; ?></strong>
                            </div>
                        </div>
                        
                        <div class="btn-group w-100" role="group">
                            <a href="<?php echo SITE_URL; ?>/pages/book-detail.php?id=<?php echo $book['id']; ?>" 
                               class="btn btn-sm btn-outline-primary flex-grow-1">
                               <i class="fas fa-eye"></i> Details
                            </a>
                            <?php if ($book['available_copies'] > 0): ?>
                                <a href="<?php echo SITE_URL; ?>/api/borrow.php?book_id=<?php echo $book['id']; ?>" 
                                   class="btn btn-sm btn-primary flex-grow-1">
                                   <i class="fas fa-download"></i> Borrow
                                </a>
                            <?php else: ?>
                                <a href="<?php echo SITE_URL; ?>/api/reserve.php?book_id=<?php echo $book['id']; ?>" 
                                   class="btn btn-sm btn-warning flex-grow-1">
                                   <i class="fas fa-bookmark"></i> Reserve
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="col-md-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No books found matching your search criteria.
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Pagination -->
<?php if ($paginationInfo['total_pages'] > 1): ?>
    <nav aria-label="Page navigation" class="mt-4">
        <ul class="pagination justify-content-center">
            <li class="page-item <?php echo $page === 1 ? 'disabled' : ''; ?>">
                <a class="page-link" href="?search=<?php echo urlencode($search); ?>&genre=<?php echo urlencode($genre); ?>&page=1">First</a>
            </li>
            <li class="page-item <?php echo $page === 1 ? 'disabled' : ''; ?>">
                <a class="page-link" href="?search=<?php echo urlencode($search); ?>&genre=<?php echo urlencode($genre); ?>&page=<?php echo $page - 1; ?>">Previous</a>
            </li>
            
            <?php for ($i = max(1, $page - 2); $i <= min($paginationInfo['total_pages'], $page + 2); $i++): ?>
                <li class="page-item <?php echo $page === $i ? 'active' : ''; ?>">
                    <a class="page-link" href="?search=<?php echo urlencode($search); ?>&genre=<?php echo urlencode($genre); ?>&page=<?php echo $i; ?>">
                        <?php echo $i; ?>
                    </a>
                </li>
            <?php endfor; ?>
            
            <li class="page-item <?php echo $page === $paginationInfo['total_pages'] ? 'disabled' : ''; ?>">
                <a class="page-link" href="?search=<?php echo urlencode($search); ?>&genre=<?php echo urlencode($genre); ?>&page=<?php echo $page + 1; ?>">Next</a>
            </li>
            <li class="page-item <?php echo $page === $paginationInfo['total_pages'] ? 'disabled' : ''; ?>">
                <a class="page-link" href="?search=<?php echo urlencode($search); ?>&genre=<?php echo urlencode($genre); ?>&page=<?php echo $paginationInfo['total_pages']; ?>">Last</a>
            </li>
        </ul>
    </nav>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
