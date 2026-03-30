<?php
$page_title = 'À propos';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Section Héro -->
<div class="position-relative overflow-hidden" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 5rem 0 4rem;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center text-white">
                <h1 class="display-3 fw-bold mb-3" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.2);">À propos de Gpower</h1>
                <p class="lead mb-0" style="font-size: 1.25rem; opacity: 0.95;">Votre partenaire de confiance pour des équipements professionnels</p>
            </div>
        </div>
    </div>
    <div class="position-absolute bottom-0 start-0 end-0" style="height: 40px; background: white; border-radius: 50% 50% 0 0 / 100% 100% 0 0;"></div>
</div>

<div class="container" style="margin-top: -20px;">
    <div class="row justify-content-center mb-5">
        <div class="col-lg-10">
            <div class="card border-0 shadow-lg rounded-4">
                <div class="card-body p-5">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center mb-4 mb-md-0">
                            <div class="d-inline-block p-4 rounded-circle" style="background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);">
                                <i class="fas fa-building" style="font-size: 3rem; color: #667eea;"></i>
                            </div>
                        </div>
                        <div class="col-md-10">
                            <h2 class="fw-bold mb-3" style="color: #2d3748;">Qui sommes-nous</h2>
                            <p class="text-muted mb-3" style="font-size: 1.05rem; line-height: 1.8;">
                                Gpower est une entreprise internationale spécialisée dans la distribution d'équipements professionnels de haute qualité.
                                Depuis notre création, nous nous engageons à fournir des produits fiables et performants répondant aux besoins
                                des professionnels et des entreprises dans tous les secteurs.
                            </p>
                            <p class="text-muted mb-0" style="font-size: 1.05rem; line-height: 1.8;">
                                Notre expertise nous permet de sélectionner soigneusement chaque produit de notre catalogue pour garantir la satisfaction
                                de nos clients. Nous travaillons avec des marques reconnues au niveau mondial.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mission & Vision (fr) -->
    <div class="row g-4 mb-5">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <h3 class="fw-bold mb-3">Notre mission</h3>
                    <p class="text-muted">Fournir des équipements professionnels de qualité pour permettre à nos clients de travailler efficacement et en toute confiance.</p>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <h3 class="fw-bold mb-3">Notre vision</h3>
                    <p class="text-muted">Devenir le distributeur de référence pour les équipements professionnels à l'échelle internationale.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center mb-5">
        <div class="col-lg-10 text-center">
            <h2 class="fw-bold">Nos valeurs</h2>
            <p class="text-muted">Fiabilité, confiance et innovation guident notre travail quotidien.</p>
        </div>
    </div>

    <div class="row justify-content-center mb-5">
        <div class="col-lg-10">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-body p-5 text-center">
                    <h3 class="fw-bold">Prêt à commencer ?</h3>
                    <p class="text-muted">Parcourez notre catalogue ou contactez notre équipe pour des conseils personnalisés.</p>
                    <a href="<?php echo BASE_URL; ?>/index.php" class="btn btn-primary rounded-pill px-4">Voir nos produits</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
