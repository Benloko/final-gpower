<?php
require_once __DIR__ . '/config/config.php';

echo "Seeding database...\n";

try {
    $pdo = getPDOConnection();
    
    // Clear existing data
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $pdo->exec("TRUNCATE TABLE products");
    $pdo->exec("TRUNCATE TABLE categories");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    
    echo "Cleared existing data.\n";
    
    // Insert Categories
    $categories = [
        [
            'name' => 'Generators',
            'slug' => 'generators',
            'description' => 'Reliable power generators for home and industrial use.',
            'image' => null
        ],
        [
            'name' => 'Solar Energy',
            'slug' => 'solar-energy',
            'description' => 'Sustainable solar panels, inverters, and batteries.',
            'image' => null
        ],
        [
            'name' => 'Power Tools',
            'slug' => 'power-tools',
            'description' => 'Professional grade tools for construction and DIY.',
            'image' => null
        ],
        [
            'name' => 'Water Pumps',
            'slug' => 'water-pumps',
            'description' => 'Efficient water pumping solutions for agriculture and drainage.',
            'image' => null
        ],
        [
            'name' => 'Electrical',
            'slug' => 'electrical',
            'description' => 'Cables, switches, and electrical accessories.',
            'image' => null
        ]
    ];
    
    $cat_ids = [];
    $stmt = $pdo->prepare("INSERT INTO categories (name, slug, description, status) VALUES (?, ?, ?, 'active')");
    
    foreach ($categories as $cat) {
        $stmt->execute([$cat['name'], $cat['slug'], $cat['description']]);
        $cat_ids[$cat['slug']] = $pdo->lastInsertId();
        echo "Created category: {$cat['name']}\n";
    }
    
    // Insert Products
    $products = [
        [
            'category' => 'generators',
            'name' => 'Silent Diesel Generator 5kVA',
            'price' => 1250.00,
            'description' => 'High performance silent diesel generator suitable for home backup and small businesses. Features automatic start and low fuel consumption.',
            'featured' => 1
        ],
        [
            'category' => 'generators',
            'name' => 'Portable Gasoline Generator 2kW',
            'price' => 350.00,
            'description' => 'Lightweight and portable generator perfect for camping and outdoor activities. Easy pull start mechanism.',
            'featured' => 0
        ],
        [
            'category' => 'generators',
            'name' => 'Industrial Generator 50kVA',
            'price' => 8500.00,
            'description' => 'Heavy duty industrial generator designed for continuous operation. Water cooled engine and soundproof canopy.',
            'featured' => 1
        ],
        [
            'category' => 'solar-energy',
            'name' => 'Monocrystalline Solar Panel 450W',
            'price' => 180.00,
            'description' => 'High efficiency solar panel with advanced cell technology. Durable aluminum frame and tempered glass.',
            'featured' => 1
        ],
        [
            'category' => 'solar-energy',
            'name' => 'Hybrid Solar Inverter 5kW',
            'price' => 650.00,
            'description' => 'Smart hybrid inverter compatible with both grid and battery power. Built-in MPPT charge controller.',
            'featured' => 1
        ],
        [
            'category' => 'solar-energy',
            'name' => 'Lithium Battery 48V 100Ah',
            'price' => 1200.00,
            'description' => 'Long-life lithium iron phosphate (LiFePO4) battery for solar energy storage. 6000+ cycle life.',
            'featured' => 0
        ],
        [
            'category' => 'power-tools',
            'name' => 'Cordless Hammer Drill 18V',
            'price' => 145.00,
            'description' => 'Professional cordless drill with hammer function. Includes 2 batteries and a fast charger.',
            'featured' => 1
        ],
        [
            'category' => 'power-tools',
            'name' => 'Angle Grinder 900W',
            'price' => 65.00,
            'description' => 'Powerful angle grinder for cutting and grinding metal and stone. Ergonomic design for comfort.',
            'featured' => 0
        ],
        [
            'category' => 'water-pumps',
            'name' => 'Submersible Water Pump 1HP',
            'price' => 120.00,
            'description' => 'Stainless steel submersible pump for clean and dirty water. Automatic float switch included.',
            'featured' => 0
        ],
        [
            'category' => 'water-pumps',
            'name' => 'Solar Water Pump Kit',
            'price' => 450.00,
            'description' => 'Complete solar pumping solution for irrigation. Includes pump, controller, and panels.',
            'featured' => 1
        ],
        [
            'category' => 'electrical',
            'name' => 'Industrial Circuit Breaker 3P',
            'price' => 45.00,
            'description' => 'Three-phase circuit breaker for industrial distribution boards. High breaking capacity.',
            'featured' => 0
        ],
        [
            'category' => 'electrical',
            'name' => 'Heavy Duty Extension Reel 50m',
            'price' => 85.00,
            'description' => 'Robust cable reel with 4 outlets and thermal cutout protection. Suitable for outdoor use.',
            'featured' => 0
        ]
    ];
    
    $stmt = $pdo->prepare("INSERT INTO products (category_id, name, slug, description, price, status, featured, created_at) VALUES (?, ?, ?, ?, ?, 'active', ?, NOW())");
    
    foreach ($products as $prod) {
        $cat_id = $cat_ids[$prod['category']];
        $slug = strtolower(str_replace(' ', '-', $prod['name'])) . '-' . rand(1000, 9999);
        
        $stmt->execute([
            $cat_id,
            $prod['name'],
            $slug,
            $prod['description'],
            $prod['price'],
            $prod['featured']
        ]);
        
        echo "Created product: {$prod['name']}\n";
    }
    
    echo "Database seeding completed successfully!\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
