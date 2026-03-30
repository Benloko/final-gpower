<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/email.php';
requireLogin();

$pdo = getPDOConnection();

$success = '';
$error = '';

// Create table if it doesn't exist
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS contact_messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        subject VARCHAR(500) NOT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status ENUM('new', 'read', 'replied') DEFAULT 'new',
        admin_reply TEXT NULL,
        replied_at TIMESTAMP NULL,
        INDEX idx_status (status),
        INDEX idx_created (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
} catch (Exception $e) {
    $error = "Database error: " . $e->getMessage();
}

// Handle reply submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_message'])) {
    $message_id = (int)$_POST['message_id'];
    $reply = trim($_POST['reply']);
    
    if (!empty($reply)) {
        try {
            // Check and add admin_reply column if not exists
            $columns = $pdo->query("SHOW COLUMNS FROM contact_messages LIKE 'admin_reply'")->fetchAll();
            if (empty($columns)) {
                $pdo->exec("ALTER TABLE contact_messages ADD COLUMN admin_reply TEXT NULL");
            }
            
            // Check and add replied_at column if not exists
            $columns = $pdo->query("SHOW COLUMNS FROM contact_messages LIKE 'replied_at'")->fetchAll();
            if (empty($columns)) {
                $pdo->exec("ALTER TABLE contact_messages ADD COLUMN replied_at TIMESTAMP NULL");
            }
            
            // Get message details for email
            $msg = $pdo->prepare("SELECT * FROM contact_messages WHERE id = ?");
            $msg->execute([$message_id]);
            $message = $msg->fetch(PDO::FETCH_ASSOC);
            
            // Update database
            $stmt = $pdo->prepare("UPDATE contact_messages SET admin_reply = ?, replied_at = NOW(), status = 'replied' WHERE id = ?");
            $stmt->execute([$reply, $message_id]);
            
            // Send email to user
            $to = $message['email'];
            $toName = $message['name'];
            $subject = "Re: " . $message['subject'];
            
            $email_body = "Hello " . $message['name'] . ",\n\n";
            $email_body .= "Thank you for your message. Here is our reply:\n\n";
            $email_body .= $reply . "\n\n";
            $email_body .= "---\n";
            $email_body .= "Votre message original :\n";
            $email_body .= $message['message'] . "\n\n";
            $email_body .= "Best regards,\n";
            $email_body .= "The Gpower Team";
            
            if (sendEmail($to, $toName, $subject, $email_body, false)) {
                $success = "Reply saved and emailed successfully!";
            } else {
                $success = "Reply saved (email could not be sent - check SMTP configuration)";
            }
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}

// Handle mark as read
if (isset($_GET['mark_read'])) {
    $message_id = (int)$_GET['mark_read'];
    try {
        $stmt = $pdo->prepare("UPDATE contact_messages SET status = 'read' WHERE id = ?");
        $stmt->execute([$message_id]);
        redirect('messages.php');
    } catch (Exception $e) {
        $error = "Error during update.";
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $message_id = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
        $stmt->execute([$message_id]);
        $success = "Message deleted successfully!";
    } catch (Exception $e) {
        $error = "Erreur lors de la suppression.";
    }
}

// Get filter
$status_filter = $_GET['status'] ?? 'all';

// Get messages
$where_clause = '';
$params = [];

if ($status_filter !== 'all') {
    $where_clause = 'WHERE status = ?';
    $params[] = $status_filter;
}

$stmt = $pdo->prepare("SELECT * FROM contact_messages $where_clause ORDER BY created_at DESC");
$stmt->execute($params);
$messages = $stmt->fetchAll();

// Get counts
$stmt = $pdo->query("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'new' THEN 1 ELSE 0 END) as new_count,
    SUM(CASE WHEN status = 'read' THEN 1 ELSE 0 END) as read_count,
    SUM(CASE WHEN status = 'replied' THEN 1 ELSE 0 END) as replied_count
FROM contact_messages");
$counts = $stmt->fetch();

$page_title = 'Contact Messages';
require_once __DIR__ . '/includes/header.php';
?>

<style>
/* Modern Clean Layout */
.messages-header {
    background: white;
    border-bottom: 1px solid #e5e7eb;
    padding: 1.5rem 0;
    margin-bottom: 1.5rem;
}

.messages-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0;
}

.messages-subtitle {
    color: #6b7280;
    font-size: 0.875rem;
    margin: 0.25rem 0 0 0;
}

/* Filters Bar */
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

.filter-tabs {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
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
    border-color: #667eea;
}

.stats-inline {
    display: flex;
    gap: 1.5rem;
    font-size: 0.875rem;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.stat-label {
    color: #6b7280;
}

.stat-value {
    font-weight: 600;
    color: #1f2937;
}

/* Message List */
.message-item {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    margin-bottom: 1rem;
    overflow: hidden;
    transition: all 0.2s;
}

.message-item:hover {
    border-color: #667eea;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.message-header-bar {
    padding: 1rem;
    background: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.75rem;
}

.message-info {
    flex: 1;
    min-width: 200px;
}

.message-sender {
    font-weight: 600;
    color: #1f2937;
    font-size: 0.95rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.message-meta {
    font-size: 0.8rem;
    color: #6b7280;
    margin-top: 0.25rem;
}

.message-actions {
    display: flex;
    gap: 0.5rem;
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
    background: #f9fafb;
}

.action-btn.danger:hover {
    border-color: #ef4444;
    color: #ef4444;
}

.action-btn.success:hover {
    border-color: #10b981;
    color: #10b981;
}

.message-content {
    padding: 1rem;
}

.message-subject {
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.message-text {
    color: #4b5563;
    font-size: 0.875rem;
    line-height: 1.6;
    white-space: pre-line;
}

.status-badge {
    padding: 0.25rem 0.625rem;
    border-radius: 0.25rem;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
}

.badge-new {
    background: #dbeafe;
    color: #1e40af;
}

.badge-read {
    background: #fef3c7;
    color: #92400e;
}

.badge-replied {
    background: #d1fae5;
    color: #065f46;
}

.reply-section {
    background: #f0fdf4;
    border-top: 1px solid #bbf7d0;
    padding: 1rem;
}

.reply-header {
    font-size: 0.8rem;
    font-weight: 600;
    color: #065f46;
    margin-bottom: 0.5rem;
}

.reply-text {
    font-size: 0.85rem;
    color: #166534;
    line-height: 1.5;
}

.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: #9ca3af;
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

/* Responsive */
@media (max-width: 768px) {
    .messages-header {
        padding: 1rem 0;
    }
    
    .messages-title {
        font-size: 1.25rem;
    }
    
    .filters-bar {
        padding: 0.75rem;
    }
    
    .stats-inline {
        width: 100%;
        justify-content: space-between;
    }
    
    .message-header-bar {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .message-actions {
        width: 100%;
        justify-content: flex-end;
    }
}
</style>

<div class="container-fluid px-4 py-3">
    <!-- Header -->
    <div class="messages-header">
        <h1 class="messages-title">
            <i class="fas fa-envelope me-2" style="color: #667eea;"></i>Messages
        </h1>
        <p class="messages-subtitle">Manage contact messages from your customers</p>
    </div>

    <!-- Alerts -->
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

    <!-- Filters and Stats -->
    <div class="filters-bar">
        <div class="filter-tabs">
            <a href="?status=all" class="filter-tab <?php echo $status_filter === 'all' ? 'active' : ''; ?>">
                <i class="fas fa-inbox me-1"></i>Tous
            </a>
            <a href="?status=new" class="filter-tab <?php echo $status_filter === 'new' ? 'active' : ''; ?>">
                <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i>New
            </a>
            <a href="?status=read" class="filter-tab <?php echo $status_filter === 'read' ? 'active' : ''; ?>">
                <i class="fas fa-eye me-1"></i>Read
            </a>
            <a href="?status=replied" class="filter-tab <?php echo $status_filter === 'replied' ? 'active' : ''; ?>">
                <i class="fas fa-check me-1"></i>Replied
            </a>
        </div>
        
        <div class="stats-inline">
            <div class="stat-item">
                <span class="stat-label">Total:</span>
                <span class="stat-value"><?php echo $counts['total'] ?? 0; ?></span>
            </div>
            <div class="stat-item">
                <span class="stat-label">New:</span>
                <span class="stat-value" style="color: #667eea;"><?php echo $counts['new_count'] ?? 0; ?></span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Replied:</span>
                <span class="stat-value" style="color: #10b981;"><?php echo $counts['replied_count'] ?? 0; ?></span>
            </div>
        </div>
    </div>

    <!-- Messages List -->
    <?php if (empty($messages)): ?>
    <div class="empty-state">
        <i class="fas fa-inbox"></i>
        <h5>No messages found</h5>
        <p>Contact messages will appear here</p>
    </div>
    <?php else: ?>
    <?php foreach ($messages as $msg): ?>
    <div class="message-item">
        <div class="message-header-bar">
            <div class="message-info">
                <div class="message-sender">
                    <?php echo htmlspecialchars($msg['name']); ?>
                    <span class="status-badge badge-<?php echo $msg['status']; ?>">
                        <?php echo $msg['status'] === 'new' ? 'New' : ($msg['status'] === 'read' ? 'Read' : 'Replied'); ?>
                    </span>
                </div>
                <div class="message-meta">
                    <i class="fas fa-envelope me-1"></i><?php echo htmlspecialchars($msg['email']); ?>
                    <span class="mx-2">•</span>
                    <i class="fas fa-clock me-1"></i><?php echo date('d/m/Y H:i', strtotime($msg['created_at'])); ?>
                </div>
            </div>
            <div class="message-actions">
                <?php if ($msg['status'] === 'new'): ?>
                <a href="?mark_read=<?php echo $msg['id']; ?>" class="action-btn" title="Mark as read">
                    <i class="fas fa-eye"></i>
                </a>
                <?php endif; ?>
                     <a href="mailto:<?php echo htmlspecialchars($msg['email']); ?>?subject=Re: <?php echo urlencode($msg['subject']); ?>" 
                         class="action-btn success" title="Reply by email">
                    <i class="fas fa-reply"></i>
                </a>
                <a href="?delete=<?php echo $msg['id']; ?>" 
                   class="action-btn danger"
                         onclick="return confirm('Delete this message?')"
                         title="Delete">
                    <i class="fas fa-trash"></i>
                </a>
            </div>
        </div>
        
        <div class="message-content">
            <div class="message-subject">
                <i class="fas fa-tag me-1" style="color: #667eea;"></i><?php echo htmlspecialchars($msg['subject']); ?>
            </div>
            <div class="message-text">
                <?php echo htmlspecialchars($msg['message']); ?>
            </div>
            
            <?php if ($msg['status'] === 'replied' && !empty($msg['admin_reply'])): ?>
            <div class="reply-section">
                <div class="reply-header">
                    <i class="fas fa-reply me-1"></i>Your reply
                    <span style="font-weight: normal; color: #6b7280;">
                        • <?php echo date('d/m/Y H:i', strtotime($msg['replied_at'])); ?>
                    </span>
                </div>
                <div class="reply-text">
                    <?php echo nl2br(htmlspecialchars($msg['admin_reply'])); ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($msg['status'] !== 'replied'): ?>
            <div class="mt-3">
                <button class="action-btn success" 
                        type="button" 
                        data-bs-toggle="collapse" 
                        data-bs-target="#reply-<?php echo $msg['id']; ?>">
                    <i class="fas fa-reply me-1"></i>Reply
                </button>
                
                <div class="collapse mt-3" id="reply-<?php echo $msg['id']; ?>">
                    <form method="POST" action="" style="background: #f9fafb; padding: 1rem; border-radius: 0.5rem; border: 1px solid #e5e7eb;">
                        <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.875rem; font-weight: 600;">Your reply</label>
                            <textarea name="reply" 
                                      class="form-control" 
                                      rows="4" 
                                      placeholder="Type your reply here..."
                                      style="font-size: 0.875rem;"
                                      required></textarea>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" name="reply_message" class="action-btn success">
                                <i class="fas fa-paper-plane me-1"></i>Send
                            </button>
                            <button type="button" 
                                    class="action-btn" 
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#reply-<?php echo $msg['id']; ?>">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
