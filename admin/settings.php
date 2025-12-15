<?php
require_once __DIR__ . '/../config/config.php';
requireLogin();

$pdo = getPDOConnection();

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle General Settings
    if (isset($_POST['action']) && $_POST['action'] == 'general') {
        $site_name = sanitize($_POST['site_name']);
        $site_email = sanitize($_POST['site_email']);
        $site_phone = sanitize($_POST['site_phone']);
        
        if (empty($site_name) || empty($site_email)) {
            $error = 'Le nom du site et l\'email sont requis';
        } else {
            $settings_to_update = [
                'site_name' => $site_name,
                'site_email' => $site_email,
                'site_phone' => $site_phone
            ];
            
            foreach ($settings_to_update as $key => $value) {
                $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
                $stmt->execute([$value, $key]);
            }
            
            $success = 'Paramètres généraux mis à jour avec succès!';
        }
    }
    
    // Handle Contact Settings
    if (isset($_POST['action']) && $_POST['action'] == 'contact') {
        $whatsapp_number = sanitize($_POST['whatsapp_number']);
        
        $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
        $stmt->execute([$whatsapp_number, 'whatsapp_number']);
        
        $success = 'Numéro WhatsApp mis à jour avec succès!';
    }
    
    // Handle Password Change
    if (isset($_POST['action']) && $_POST['action'] == 'password') {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        $new_username = isset($_POST['new_username']) ? sanitize($_POST['new_username']) : '';
        
        if (empty($current_password)) {
            $error = 'Le mot de passe actuel est requis';
        } else {
            // Verify current password
            $stmt = $pdo->prepare("SELECT password, username FROM admins WHERE id = ?");
            $stmt->execute([$_SESSION['admin_id']]);
            $admin = $stmt->fetch();
            
            if (!password_verify($current_password, $admin['password'])) {
                $error = 'Mot de passe actuel incorrect';
            } else {
                $updateFields = [];
                $updateValues = [];
                
                // Update username if provided
                if (!empty($new_username) && $new_username !== $admin['username']) {
                    // Check if username already exists
                    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM admins WHERE username = ? AND id != ?");
                    $checkStmt->execute([$new_username, $_SESSION['admin_id']]);
                    if ($checkStmt->fetchColumn() > 0) {
                        $error = 'Ce nom d\'utilisateur est déjà utilisé';
                    } else {
                        $updateFields[] = "username = ?";
                        $updateValues[] = $new_username;
                    }
                }
                
                // Update password if provided
                if (!empty($new_password)) {
                    if ($new_password !== $confirm_password) {
                        $error = 'Les mots de passe ne correspondent pas';
                    } elseif (strlen($new_password) < 6) {
                        $error = 'Le mot de passe doit contenir au moins 6 caractères';
                    } else {
                        $updateFields[] = "password = ?";
                        $updateValues[] = password_hash($new_password, PASSWORD_DEFAULT);
                    }
                }
                
                // Execute update if there are fields to update and no errors
                if (empty($error) && !empty($updateFields)) {
                    $updateValues[] = $_SESSION['admin_id'];
                    $sql = "UPDATE admins SET " . implode(', ', $updateFields) . " WHERE id = ?";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($updateValues);
                    
                    $success = 'Informations mises à jour avec succès!';
                } elseif (empty($error) && empty($updateFields)) {
                    $error = 'Aucune modification à enregistrer';
                }
            }
        }
    }
}

// Get current settings
$stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
$settings = [];
while ($row = $stmt->fetch()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

// Get current admin info
$stmt = $pdo->prepare("SELECT username, email FROM admins WHERE id = ?");
$stmt->execute([$_SESSION['admin_id']]);
$admin_info = $stmt->fetch();

$page_title = 'Settings';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Page Header -->
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1 fw-bold text-dark">Paramètres</h4>
            <p class="text-muted small mb-0">Gérer les paramètres de votre site</p>
        </div>
    </div>
</div>

<?php if ($error): ?>
<div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 mb-3" role="alert">
    <div class="d-flex align-items-center">
        <i class="fas fa-exclamation-circle me-2"></i>
        <div><?php echo htmlspecialchars($error); ?></div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if ($success): ?>
<div class="alert alert-success alert-dismissible fade show border-0 rounded-3 mb-3" role="alert">
    <div class="d-flex align-items-center">
        <i class="fas fa-check-circle me-2"></i>
        <div><?php echo htmlspecialchars($success); ?></div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row g-3">
    <!-- General Settings Card -->
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm rounded-3 h-100 setting-card" style="cursor: pointer; transition: transform 0.2s;" onclick="openSettingModal('general')">
            <div class="card-body p-3">
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div class="bg-primary bg-opacity-10 rounded-3 p-2">
                        <i class="fas fa-cog text-primary" style="font-size: 1.5rem;"></i>
                    </div>
                    <i class="fas fa-chevron-right text-muted" style="font-size: 0.8rem;"></i>
                </div>
                <h6 class="fw-bold mb-2">Paramètres Généraux</h6>
                <p class="text-muted small mb-0">Nom du site, email, téléphone</p>
            </div>
        </div>
    </div>

    <!-- Contact Settings Card -->
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm rounded-3 h-100 setting-card" style="cursor: pointer; transition: transform 0.2s;" onclick="openSettingModal('contact')">
            <div class="card-body p-3">
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div class="bg-success bg-opacity-10 rounded-3 p-2">
                        <i class="fab fa-whatsapp text-success" style="font-size: 1.5rem;"></i>
                    </div>
                    <i class="fas fa-chevron-right text-muted" style="font-size: 0.8rem;"></i>
                </div>
                <h6 class="fw-bold mb-2">Contact WhatsApp</h6>
                <p class="text-muted small mb-0">Numéro WhatsApp pour contact</p>
            </div>
        </div>
    </div>

    <!-- Password Settings Card -->
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm rounded-3 h-100 setting-card" style="cursor: pointer; transition: transform 0.2s;" onclick="openSettingModal('password')">
            <div class="card-body p-3">
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div class="bg-danger bg-opacity-10 rounded-3 p-2">
                        <i class="fas fa-key text-danger" style="font-size: 1.5rem;"></i>
                    </div>
                    <i class="fas fa-chevron-right text-muted" style="font-size: 0.8rem;"></i>
                </div>
                <h6 class="fw-bold mb-2">Mot de passe</h6>
                <p class="text-muted small mb-0">Changer votre mot de passe</p>
            </div>
        </div>
    </div>

    <!-- Username Settings Card -->
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm rounded-3 h-100 setting-card" style="cursor: pointer; transition: transform 0.2s;" onclick="openSettingModal('username')">
            <div class="card-body p-3">
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div class="bg-info bg-opacity-10 rounded-3 p-2">
                        <i class="fas fa-user-edit text-info" style="font-size: 1.5rem;"></i>
                    </div>
                    <i class="fas fa-chevron-right text-muted" style="font-size: 0.8rem;"></i>
                </div>
                <h6 class="fw-bold mb-2">Nom d'utilisateur</h6>
                <p class="text-muted small mb-0">Modifier votre identifiant</p>
            </div>
        </div>
    </div>

    <!-- System Info Card -->
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm rounded-3 h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body p-3">
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div class="bg-white bg-opacity-20 rounded-3 p-2">
                        <i class="fas fa-info-circle text-white" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
                <h6 class="fw-bold mb-2 text-white">Informations Système</h6>
                <div class="d-flex flex-column gap-1" style="font-size: 0.75rem;">
                    <div class="d-flex justify-content-between text-white opacity-90">
                        <span>PHP:</span>
                        <span class="fw-semibold"><?php echo PHP_VERSION; ?></span>
                    </div>
                    <div class="d-flex justify-content-between text-white opacity-90">
                        <span>Database:</span>
                        <span class="fw-semibold">MySQL</span>
                    </div>
                    <div class="d-flex justify-content-between text-white opacity-90">
                        <span>Version:</span>
                        <span class="fw-semibold">1.0.0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- General Settings Modal -->
<div class="modal fade" id="generalSettingsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-2" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="text-white">
                    <h5 class="modal-title fw-bold mb-1">
                        <i class="fas fa-cog me-2"></i>Paramètres Généraux
                    </h5>
                    <p class="small mb-0 opacity-90">Modifier les informations de base du site</p>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="general">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small mb-2">
                            <i class="fas fa-building text-primary me-1"></i>Nom du site *
                        </label>
                        <input type="text" 
                               name="site_name" 
                               class="form-control form-control-lg border-1 rounded-3" 
                               value="<?php echo htmlspecialchars($settings['site_name'] ?? 'Gpower'); ?>"
                               placeholder="Ex: Gpower"
                               required>
                        <div class="form-text small">Le nom affiché sur votre site web</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold small mb-2">
                            <i class="fas fa-envelope text-primary me-1"></i>Email du site *
                        </label>
                        <input type="email" 
                               name="site_email" 
                               class="form-control form-control-lg border-1 rounded-3" 
                               value="<?php echo htmlspecialchars($settings['site_email'] ?? ''); ?>"
                               placeholder="contact@gpower.ci"
                               required>
                        <div class="form-text small">Email principal pour les contacts</div>
                    </div>
                    
                    <div class="mb-0">
                        <label class="form-label fw-semibold small mb-2">
                            <i class="fas fa-phone text-primary me-1"></i>Téléphone du site
                        </label>
                        <input type="text" 
                               name="site_phone" 
                               class="form-control form-control-lg border-1 rounded-3" 
                               value="<?php echo htmlspecialchars($settings['site_phone'] ?? ''); ?>"
                               placeholder="+225 XX XX XX XX XX">
                        <div class="form-text small">Numéro de téléphone principal (optionnel)</div>
                    </div>
                    <input type="hidden" name="whatsapp_number" value="<?php echo htmlspecialchars($settings['whatsapp_number'] ?? ''); ?>">
                </div>
                <div class="modal-footer border-0 bg-light pt-3 pb-3">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Annuler
                    </button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                        <i class="fas fa-save me-2"></i>Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Contact Settings Modal -->
<div class="modal fade" id="contactSettingsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-2" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                <div class="text-white">
                    <h5 class="modal-title fw-bold mb-1">
                        <i class="fab fa-whatsapp me-2"></i>Contact WhatsApp
                    </h5>
                    <p class="small mb-0 opacity-90">Configurer le numéro de contact WhatsApp</p>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="contact">
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 rounded-3 d-flex align-items-start" style="background-color: #e7f9f5;">
                        <i class="fas fa-info-circle text-info me-2 mt-1"></i>
                        <div class="small">
                            <strong>Important:</strong> Entrez votre numéro WhatsApp au format international sans le signe +. 
                            Par exemple: <code>2250700000000</code> pour la Côte d'Ivoire.
                        </div>
                    </div>
                    
                    <div class="mb-0">
                        <label class="form-label fw-semibold small mb-2">
                            <i class="fab fa-whatsapp text-success me-1"></i>Numéro WhatsApp
                        </label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-success bg-opacity-10 border-1">
                                <i class="fab fa-whatsapp text-success"></i>
                            </span>
                            <input type="text" 
                                   name="whatsapp_number" 
                                   class="form-control border-1 rounded-end-3" 
                                   value="<?php echo htmlspecialchars($settings['whatsapp_number'] ?? ''); ?>"
                                   placeholder="2250700000000"
                                   pattern="[0-9]+"
                                   title="Entrez uniquement des chiffres">
                        </div>
                        <div class="form-text small mt-2">
                            <i class="fas fa-lightbulb text-warning me-1"></i>
                            Format: Code pays + numéro sans espaces ni symboles
                        </div>
                    </div>
                    <input type="hidden" name="site_name" value="<?php echo htmlspecialchars($settings['site_name'] ?? 'Gpower'); ?>">
                    <input type="hidden" name="site_email" value="<?php echo htmlspecialchars($settings['site_email'] ?? ''); ?>">
                    <input type="hidden" name="site_phone" value="<?php echo htmlspecialchars($settings['site_phone'] ?? ''); ?>">
                </div>
                <div class="modal-footer border-0 bg-light pt-3 pb-3">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Annuler
                    </button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm">
                        <i class="fas fa-save me-2"></i>Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Password Settings Modal -->
<div class="modal fade" id="passwordSettingsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0" style="background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);">
                <div class="text-white">
                    <h5 class="modal-title fw-bold mb-0">
                        <i class="fas fa-key me-2"></i>Mot de passe
                    </h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="password">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small mb-2">
                            <i class="fas fa-lock text-danger me-1"></i>Mot de passe actuel *
                        </label>
                        <input type="password" 
                               name="current_password" 
                               class="form-control rounded-3"
                               placeholder="Mot de passe actuel"
                               required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold small mb-2">
                            <i class="fas fa-key text-success me-1"></i>Nouveau mot de passe
                        </label>
                        <input type="password" 
                               name="new_password" 
                               class="form-control rounded-3"
                               placeholder="Nouveau mot de passe"
                               minlength="6"
                               required>
                        <div class="form-text small">Minimum 6 caractères</div>
                    </div>
                    
                    <div class="mb-0">
                        <label class="form-label fw-semibold small mb-2">
                            <i class="fas fa-check-circle text-success me-1"></i>Confirmer
                        </label>
                        <input type="password" 
                               name="confirm_password" 
                               class="form-control rounded-3"
                               placeholder="Retapez le mot de passe"
                               minlength="6"
                               required>
                    </div>
                </div>
                
                <div class="modal-footer border-0 pt-0 pb-3 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Annuler
                    </button>
                    <button type="submit" class="btn text-white rounded-pill px-4 shadow-sm" style="background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);">
                        <i class="fas fa-save me-2"></i>Modifier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Username Settings Modal -->
<div class="modal fade" id="usernameSettingsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="text-white">
                    <h5 class="modal-title fw-bold mb-0">
                        <i class="fas fa-user-edit me-2"></i>Nom d'utilisateur
                    </h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="password">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small mb-2">
                            <i class="fas fa-user text-muted me-1"></i>Actuel
                        </label>
                        <input type="text" 
                               class="form-control rounded-3"
                               value="<?php echo htmlspecialchars($admin_info['username'] ?? ''); ?>"
                               disabled
                               style="background-color: #f8f9fa; font-weight: 500;">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold small mb-2">
                            <i class="fas fa-user-edit text-info me-1"></i>Nouveau nom *
                        </label>
                        <input type="text" 
                               name="new_username" 
                               class="form-control rounded-3"
                               placeholder="Nouveau nom d'utilisateur"
                               required>
                    </div>
                    
                    <div class="mb-0">
                        <label class="form-label fw-semibold small mb-2">
                            <i class="fas fa-lock text-danger me-1"></i>Mot de passe actuel *
                        </label>
                        <input type="password" 
                               name="current_password" 
                               class="form-control rounded-3"
                               placeholder="Confirmez avec votre mot de passe"
                               required>
                        <div class="form-text small">Requis pour confirmer</div>
                    </div>
                </div>
                
                <div class="modal-footer border-0 pt-0 pb-3 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Annuler
                    </button>
                    <button type="submit" class="btn text-white rounded-pill px-4 shadow-sm" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                        <i class="fas fa-save me-2"></i>Modifier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.setting-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.1) !important;
}

/* Ensure modal scrolls properly */
.modal-dialog-scrollable .modal-body {
    overflow-y: auto !important;
}

/* Modal responsiveness improvements */
@media (max-width: 576px) {
    .modal-dialog {
        margin: 0.5rem;
    }
    
    .modal-dialog-scrollable {
        max-height: calc(100vh - 1rem);
    }
    
    .modal-content {
        border-radius: 1rem !important;
        max-height: 100%;
    }
    
    .modal-header {
        padding: 1rem !important;
    }
    
    .modal-body {
        padding: 1rem !important;
    }
    
    .modal-footer {
        padding: 0.75rem 1rem !important;
    }
    
    .modal-footer .btn {
        width: 100%;
    }
    
    .input-group-lg .form-control,
    .input-group-lg .input-group-text {
        font-size: 1rem;
        padding: 0.75rem;
    }
    }
}

/* Better form controls on mobile */
@media (max-width: 768px) {
    .form-control-lg {
        font-size: 1rem;
    }
}

/* Smooth modal animations */
.modal.fade .modal-dialog {
    transition: transform 0.3s ease-out;
}

.modal-content {
    overflow: hidden;
}

/* Alert box styling */
.modal-body .alert {
    font-size: 0.85rem;
}

@media (max-width: 576px) {
    .modal-body .alert {
        font-size: 0.8rem;
        padding: 0.75rem;
    }
}
</style>

<script>
function openSettingModal(type) {
    let modalId = '';
    switch(type) {
        case 'general':
            modalId = 'generalSettingsModal';
            break;
        case 'contact':
            modalId = 'contactSettingsModal';
            break;
        case 'password':
            modalId = 'passwordSettingsModal';
            break;
        case 'username':
            modalId = 'usernameSettingsModal';
            break;
    }
    
    if (modalId) {
        const modal = new bootstrap.Modal(document.getElementById(modalId));
        modal.show();
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
