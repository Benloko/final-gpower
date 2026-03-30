<?php
require_once 'config/config.php';
require_once 'includes/header.php';

$section = $_GET['section'] ?? 'mentions';
?>

<div class="container py-4 mt-2">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="mb-3">
                <a href="<?php echo BASE_URL; ?>/" class="btn btn-white rounded-pill px-4 py-2 shadow-sm hover-lift text-decoration-none fw-bold">
                    <i class="fas fa-arrow-left me-2 text-primary"></i> Retour
                </a>
            </div>

            <div class="text-center mb-4">
                <h1 class="fw-bold display-5 mb-2">Informations légales</h1>
                <p class="text-muted lead">Transparence et confiance au cœur de nos engagements.</p>
            </div>

            <div>
                <!-- Horizontal Navigation -->
                <div class="row g-2 mb-4 justify-content-center">
                    <div class="col-4 col-md-auto">
                        <a href="?section=mentions" class="btn w-100 h-100 px-1 py-2 px-md-4 py-md-3 rounded-pill shadow-sm d-flex flex-column flex-md-row align-items-center justify-content-center gap-1 gap-md-2 hover-lift <?php echo $section === 'mentions' ? 'btn-primary' : 'bg-white text-muted border'; ?>">
                            <i class="fas fa-building"></i> 
                            <span class="fw-bold d-none d-md-inline">Mentions légales</span>
                        </a>
                    </div>
                    <div class="col-4 col-md-auto">
                        <a href="?section=privacy" class="btn w-100 h-100 px-1 py-2 px-md-4 py-md-3 rounded-pill shadow-sm d-flex flex-column flex-md-row align-items-center justify-content-center gap-1 gap-md-2 hover-lift <?php echo $section === 'privacy' ? 'btn-primary' : 'bg-white text-muted border'; ?>">
                            <i class="fas fa-user-shield"></i>
                            <span class="fw-bold d-none d-md-inline">Confidentialité</span>
                        </a>
                    </div>
                    <div class="col-4 col-md-auto">
                        <a href="?section=terms" class="btn w-100 h-100 px-1 py-2 px-md-4 py-md-3 rounded-pill shadow-sm d-flex flex-column flex-md-row align-items-center justify-content-center gap-1 gap-md-2 hover-lift <?php echo $section === 'terms' ? 'btn-primary' : 'bg-white text-muted border'; ?>">
                            <i class="fas fa-file-contract"></i>
                            <span class="fw-bold d-none d-md-inline">Conditions</span>
                        </a>
                    </div>
                </div>

                <div>
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-5">
                            <?php if ($section === 'mentions'): ?>
                                <h2 class="fw-bold mb-4 text-primary">Mentions légales</h2>
                                <div class="mb-4">
                                    <h5 class="fw-bold">1. Propriétaire du site</h5>
                                    <p class="text-muted">
                                        Ce site est publié par GPOWER SARL.<br>
                                        Siège social : Abidjan, Côte d'Ivoire<br>
                                        Email : contact@gpower.ci<br>
                                        Téléphone : +225 07 07 07 07 07
                                    </p>
                                </div>
                                <div class="mb-4">
                                    <h5 class="fw-bold">2. Hébergement</h5>
                                    <p class="text-muted">
                                        Ce site est hébergé par [Nom de l'hébergeur], [Adresse].
                                    </p>
                                </div>
                                <div class="mb-4">
                                    <h5 class="fw-bold">3. Propriété intellectuelle</h5>
                                    <p class="text-muted">
                                        Tous les contenus de ce site sont protégés par les lois sur le droit d'auteur et la propriété intellectuelle.
                                    </p>
                                </div>

                            <?php elseif ($section === 'privacy'): ?>
                                <h2 class="fw-bold mb-4 text-primary">Politique de confidentialité</h2>
                                <p class="lead text-muted mb-4">
                                    Chez GPOWER, nous prenons la protection de vos données personnelles au sérieux.
                                </p>
                                <div class="mb-4">
                                    <h5 class="fw-bold">Collecte des données</h5>
                                    <p class="text-muted">
                                        Les informations collectées sur ce site sont conservées par GPOWER pour la gestion client et l'envoi d'offres.
                                    </p>
                                </div>
                                <div class="mb-4">
                                    <h5 class="fw-bold">Utilisation des données</h5>
                                    <p class="text-muted">
                                        Les données sont conservées jusqu'à 3 ans et sont utilisées par nos équipes marketing et commerciales basées en Côte d'Ivoire.
                                    </p>
                                </div>
                                <div class="alert alert-info border-0 rounded-3">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Conformément à la loi, vous pouvez exercer votre droit d'accès et de rectification en nous contactant.
                                </div>

                            <?php elseif ($section === 'terms'): ?>
                                <h2 class="fw-bold mb-4 text-primary">Conditions générales</h2>
                                <div class="mb-4">
                                    <h5 class="fw-bold">1. Objet</h5>
                                    <p class="text-muted">
                                        Ces conditions régissent la vente d'équipements électroniques et accessoires par GPOWER.
                                    </p>
                                </div>
                                <div class="mb-4">
                                    <h5 class="fw-bold">2. Tarification</h5>
                                    <p class="text-muted">
                                        Les prix des produits sont indiqués en dollars américains (USD), toutes taxes comprises, sauf indication contraire.
                                    </p>
                                </div>
                                <div class="mb-4">
                                    <h5 class="fw-bold">3. Commandes</h5>
                                    <p class="text-muted">
                                        Les commandes peuvent être passées via notre site ou WhatsApp. Les informations contractuelles sont fournies en anglais.
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
