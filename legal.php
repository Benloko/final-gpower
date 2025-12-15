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
                <h1 class="fw-bold display-5 mb-2">Informations Légales</h1>
                <p class="text-muted lead">Transparence et confiance sont au cœur de nos engagements.</p>
            </div>

            <div>
                <!-- Horizontal Navigation -->
                <div class="row g-2 mb-4 justify-content-center">
                    <div class="col-4 col-md-auto">
                        <a href="?section=mentions" class="btn w-100 h-100 px-1 py-2 px-md-4 py-md-3 rounded-pill shadow-sm d-flex flex-column flex-md-row align-items-center justify-content-center gap-1 gap-md-2 hover-lift <?php echo $section === 'mentions' ? 'btn-primary' : 'bg-white text-muted border'; ?>">
                            <i class="fas fa-building"></i> 
                            <span class="fw-bold d-none d-md-inline">Mentions Légales</span>
                            <span class="fw-bold d-inline d-md-none" style="font-size: 0.7rem;">Mentions</span>
                        </a>
                    </div>
                    <div class="col-4 col-md-auto">
                        <a href="?section=privacy" class="btn w-100 h-100 px-1 py-2 px-md-4 py-md-3 rounded-pill shadow-sm d-flex flex-column flex-md-row align-items-center justify-content-center gap-1 gap-md-2 hover-lift <?php echo $section === 'privacy' ? 'btn-primary' : 'bg-white text-muted border'; ?>">
                            <i class="fas fa-user-shield"></i>
                            <span class="fw-bold d-none d-md-inline">Confidentialité</span>
                            <span class="fw-bold d-inline d-md-none" style="font-size: 0.7rem;">Confidentialité</span>
                        </a>
                    </div>
                    <div class="col-4 col-md-auto">
                        <a href="?section=terms" class="btn w-100 h-100 px-1 py-2 px-md-4 py-md-3 rounded-pill shadow-sm d-flex flex-column flex-md-row align-items-center justify-content-center gap-1 gap-md-2 hover-lift <?php echo $section === 'terms' ? 'btn-primary' : 'bg-white text-muted border'; ?>">
                            <i class="fas fa-file-contract"></i>
                            <span class="fw-bold d-none d-md-inline">Conditions (CGV)</span>
                            <span class="fw-bold d-inline d-md-none" style="font-size: 0.7rem;">CGV</span>
                        </a>
                    </div>
                </div>

                <!-- Content Area -->
                <div>
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-5">
                            <?php if ($section === 'mentions'): ?>
                                <h2 class="fw-bold mb-4 text-primary">Mentions Légales</h2>
                                <div class="mb-4">
                                    <h5 class="fw-bold">1. Éditeur du site</h5>
                                    <p class="text-muted">
                                        Le site GPOWER est édité par la société GPOWER SARL.<br>
                                        Siège social : Abidjan, Côte d'Ivoire<br>
                                        Email : contact@gpower.ci<br>
                                        Téléphone : +225 07 07 07 07 07
                                    </p>
                                </div>
                                <div class="mb-4">
                                    <h5 class="fw-bold">2. Hébergement</h5>
                                    <p class="text-muted">
                                        Ce site est hébergé par [Nom de l'hébergeur], [Adresse de l'hébergeur].
                                    </p>
                                </div>
                                <div class="mb-4">
                                    <h5 class="fw-bold">3. Propriété intellectuelle</h5>
                                    <p class="text-muted">
                                        L'ensemble de ce site relève de la législation ivoirienne et internationale sur le droit d'auteur et la propriété intellectuelle. Tous les droits de reproduction sont réservés.
                                    </p>
                                </div>

                            <?php elseif ($section === 'privacy'): ?>
                                <h2 class="fw-bold mb-4 text-primary">Politique de Confidentialité</h2>
                                <p class="lead text-muted mb-4">
                                    Chez GPOWER, nous prenons la protection de vos données très au sérieux.
                                </p>
                                <div class="mb-4">
                                    <h5 class="fw-bold">Collecte des données</h5>
                                    <p class="text-muted">
                                        Les informations recueillies sur ce site sont enregistrées dans un fichier informatisé par GPOWER pour la gestion de notre clientèle et l'envoi de nos offres.
                                    </p>
                                </div>
                                <div class="mb-4">
                                    <h5 class="fw-bold">Utilisation des données</h5>
                                    <p class="text-muted">
                                        Elles sont conservées pendant 3 ans et sont destinées au service marketing et au service commercial établis en Côte d'Ivoire.
                                    </p>
                                </div>
                                <div class="alert alert-info border-0 rounded-3">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Conformément à la loi, vous pouvez exercer votre droit d'accès aux données vous concernant et les faire rectifier en nous contactant.
                                </div>

                            <?php elseif ($section === 'terms'): ?>
                                <h2 class="fw-bold mb-4 text-primary">Conditions Générales de Vente</h2>
                                <div class="mb-4">
                                    <h5 class="fw-bold">1. Objet</h5>
                                    <p class="text-muted">
                                        Les présentes conditions régissent les ventes par la société GPOWER d'équipements électroniques et accessoires.
                                    </p>
                                </div>
                                <div class="mb-4">
                                    <h5 class="fw-bold">2. Prix</h5>
                                    <p class="text-muted">
                                        Les prix de nos produits sont indiqués en Francs CFA (FCFA) toutes taxes comprises (TTC), sauf indication contraire.
                                    </p>
                                </div>
                                <div class="mb-4">
                                    <h5 class="fw-bold">3. Commandes</h5>
                                    <p class="text-muted">
                                        Vous pouvez passer commande sur notre site internet ou via WhatsApp. Les informations contractuelles sont présentées en langue française.
                                    </p>
                                </div>
                                <div class="mb-4">
                                    <h5 class="fw-bold">4. Livraison</h5>
                                    <p class="text-muted">
                                        Les produits sont livrés à l'adresse de livraison indiquée au cours du processus de commande, dans le délai indiqué sur la page de validation de la commande.
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
