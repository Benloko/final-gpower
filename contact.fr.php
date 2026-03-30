<?php
$page_title = 'Contact';
require_once __DIR__ . '/includes/header.php';
?>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h1 class="fw-bold">Contactez-nous</h1>
                <p class="text-muted">Nous sommes là pour vous aider. Envoyez-nous un message ou contactez-nous sur WhatsApp.</p>
            </div>
        </div>

        <div class="row justify-content-center mt-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <form method="POST" action="<?php echo BASE_URL; ?>/contact.php">
                            <div class="mb-3">
                                <label class="form-label">Nom</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Sujet</label>
                                <input type="text" name="subject" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Message</label>
                                <textarea name="message" class="form-control" rows="6" required></textarea>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" name="send_message" class="btn btn-primary">Envoyer</button>
                                <a href="<?php echo BASE_URL; ?>" class="btn btn-outline-secondary">Annuler</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
