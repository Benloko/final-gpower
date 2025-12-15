<?php
require_once __DIR__ . '/../config/config.php';
requireLogin();

$pdo = getPDOConnection();
$success = '';
$error = '';

// Handle actions
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM newsletter_subscribers WHERE id = ?");
        $stmt->execute([$id]);
        $success = "Abonné supprimé avec succès!";
    } catch (Exception $e) {
        $error = "Erreur lors de la suppression.";
    }
}

if (isset($_GET['unsubscribe'])) {
    $id = (int)$_GET['unsubscribe'];
    try {
        $stmt = $pdo->prepare("UPDATE newsletter_subscribers SET status = 'unsubscribed' WHERE id = ?");
        $stmt->execute([$id]);
        $success = "Abonné désactivé!";
    } catch (Exception $e) {
        $error = "Erreur.";
    }
}

if (isset($_GET['reactivate'])) {
    $id = (int)$_GET['reactivate'];
    try {
        $stmt = $pdo->prepare("UPDATE newsletter_subscribers SET status = 'active' WHERE id = ?");
        $stmt->execute([$id]);
        $success = "Abonné réactivé!";
    } catch (Exception $e) {
        $error = "Erreur.";
    }
}

// Export to CSV
if (isset($_GET['export'])) {
    $stmt = $pdo->query("SELECT email, subscribed_at, status FROM newsletter_subscribers WHERE status = 'active' ORDER BY subscribed_at DESC");
    $subscribers = $stmt->fetchAll();
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=newsletter_subscribers_' . date('Y-m-d') . '.csv');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Email', 'Date d\'inscription', 'Statut']);
    
    foreach ($subscribers as $sub) {
        fputcsv($output, [
            $sub['email'],
            date('d/m/Y H:i', strtotime($sub['subscribed_at'])),
            $sub['status']
        ]);
    }
    
    fclose($output);
    exit;
}

// Get filter
$status_filter = $_GET['status'] ?? 'all';

// Get subscribers
$where_clause = '';
$params = [];

if ($status_filter !== 'all') {
    $where_clause = 'WHERE status = ?';
    $params[] = $status_filter;
}

$stmt = $pdo->prepare("SELECT * FROM newsletter_subscribers $where_clause ORDER BY subscribed_at DESC");
$stmt->execute($params);
$subscribers = $stmt->fetchAll();

// Get counts
$stmt = $pdo->query("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_count,
    SUM(CASE WHEN status = 'unsubscribed' THEN 1 ELSE 0 END) as unsubscribed_count
FROM newsletter_subscribers");
$counts = $stmt->fetch();

$page_title = 'Newsletter - Abonnés';
require_once __DIR__ . '/includes/header.php';
?>

<style>
.subscribers-header {
    background: white;
    border-bottom: 1px solid #e5e7eb;
    padding: 1.5rem 0;
    margin-bottom: 1.5rem;
}

.subscribers-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0;
}

.filters-bar {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    padding: 1rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
}

.filter-tab {
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: #6b7280;
    background: #f9fafb;
    border: 1px solid transparent;
    text-decoration: none;
    transition: all 0.2s;
}

.filter-tab:hover {
    background: #f3f4f6;
    color: #374151;
}

.filter-tab.active {
    background: #667eea;
    color: white;
}

.subscriber-item {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    padding: 1rem;
    margin-bottom: 0.75rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: all 0.2s;
}

.subscriber-item:hover {
    border-color: #667eea;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.subscriber-info {
    flex: 1;
}

.subscriber-email {
    font-weight: 600;
    color: #1f2937;
    font-size: 0.95rem;
}

.subscriber-meta {
    font-size: 0.8rem;
    color: #6b7280;
    margin-top: 0.25rem;
}

.status-badge {
    padding: 0.25rem 0.625rem;
    border-radius: 0.25rem;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
}

.badge-active {
    background: #d1fae5;
    color: #065f46;
}

.badge-unsubscribed {
    background: #fee2e2;
    color: #991b1b;
}

.action-btn {
    padding: 0.4rem 0.75rem;
    border-radius: 0.375rem;
    font-size: 0.8rem;
    border: 1px solid #e5e7eb;
    background: white;
    color: #6b7280;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
}

.action-btn:hover {
    border-color: #667eea;
    color: #667eea;
}

.action-btn.danger:hover {
    border-color: #ef4444;
    color: #ef4444;
}

@media (max-width: 768px) {
    .subscriber-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
    }
}
</style>

<div class="container-fluid px-4 py-3">
    <div class="subscribers-header">
        <h1 class="subscribers-title">
            <i class="fas fa-users me-2" style="color: #667eea;"></i>Abonnés Newsletter
        </h1>
        <p class="text-muted mb-0" style="font-size: 0.875rem;">Gérez vos abonnés à la newsletter</p>
    </div>

    <?php if ($success): ?>
    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
        <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="filters-bar">
        <div class="d-flex gap-2 flex-wrap">
            <a href="?status=all" class="filter-tab <?php echo $status_filter === 'all' ? 'active' : ''; ?>">
                <i class="fas fa-users me-1"></i>Tous (<?php echo $counts['total'] ?? 0; ?>)
            </a>
            <a href="?status=active" class="filter-tab <?php echo $status_filter === 'active' ? 'active' : ''; ?>">
                <i class="fas fa-check me-1"></i>Actifs (<?php echo $counts['active_count'] ?? 0; ?>)
            </a>
            <a href="?status=unsubscribed" class="filter-tab <?php echo $status_filter === 'unsubscribed' ? 'active' : ''; ?>">
                <i class="fas fa-times me-1"></i>Désabonnés (<?php echo $counts['unsubscribed_count'] ?? 0; ?>)
            </a>
        </div>
        
        <a href="?export=csv" class="action-btn" style="background: #10b981; color: white; border-color: #10b981;">
            <i class="fas fa-download"></i>Exporter CSV
        </a>
    </div>

    <?php if (empty($subscribers)): ?>
    <div class="text-center py-5">
        <i class="fas fa-users" style="font-size: 3rem; color: #9ca3af; opacity: 0.5; margin-bottom: 1rem;"></i>
        <h5 class="text-muted">Aucun abonné trouvé</h5>
        <p class="text-muted">Les abonnés apparaîtront ici</p>
    </div>
    <?php else: ?>
    <?php foreach ($subscribers as $sub): ?>
    <div class="subscriber-item">
        <div class="subscriber-info">
            <div class="subscriber-email">
                <i class="fas fa-envelope me-2" style="color: #667eea;"></i>
                <?php echo htmlspecialchars($sub['email']); ?>
                <span class="status-badge badge-<?php echo $sub['status']; ?> ms-2">
                    <?php echo $sub['status'] === 'active' ? 'Actif' : 'Désabonné'; ?>
                </span>
            </div>
            <div class="subscriber-meta">
                <i class="fas fa-calendar me-1"></i>
                Inscrit le <?php echo date('d/m/Y à H:i', strtotime($sub['subscribed_at'])); ?>
                <?php if ($sub['ip_address']): ?>
                    <span class="mx-2">•</span>
                    <i class="fas fa-globe me-1"></i><?php echo htmlspecialchars($sub['ip_address']); ?>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="d-flex gap-2">
            <?php if ($sub['status'] === 'active'): ?>
            <a href="?unsubscribe=<?php echo $sub['id']; ?>" 
               class="action-btn btn-unsubscribe"
               data-id="<?php echo $sub['id']; ?>"
               data-email="<?php echo htmlspecialchars($sub['email']); ?>"
               title="Désabonner">
                <i class="fas fa-user-slash"></i>
            </a>
            <?php else: ?>
            <a href="?reactivate=<?php echo $sub['id']; ?>" 
               class="action-btn"
               title="Réactiver">
                <i class="fas fa-user-check"></i>
            </a>
            <?php endif; ?>
            
            <a href="?delete=<?php echo $sub['id']; ?>" 
               class="action-btn danger btn-delete-subscriber"
               data-id="<?php echo $sub['id']; ?>"
               data-email="<?php echo htmlspecialchars($sub['email']); ?>"
               title="Supprimer">
                <i class="fas fa-trash"></i>
            </a>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Delete subscriber confirmation
document.querySelectorAll('.btn-delete-subscriber').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const email = this.dataset.email;
        const url = this.href;
        
        Swal.fire({
            title: 'Supprimer ?',
            html: `<p class="small">${email}</p>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Oui',
            cancelButtonText: 'Non',
            reverseButtons: true,
            width: '350px',
            padding: '1.5rem'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    });
});

// Unsubscribe confirmation
document.querySelectorAll('.btn-unsubscribe').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const email = this.dataset.email;
        const url = this.href;
        
        Swal.fire({
            title: 'Désabonner ?',
            html: `<p class="small">${email}</p>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ffc107',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Oui',
            cancelButtonText: 'Non',
            reverseButtons: true,
            width: '350px',
            padding: '1.5rem'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
