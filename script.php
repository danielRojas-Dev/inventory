<?php

$electrodr = new PDO("mysql:host=127.0.0.1;dbname=electrodr", "root", "");
$inventory = new PDO("mysql:host=127.0.0.1;dbname=new_electrodr", "root", "");

// Obtener todas las marcas de inventory (para match rápido en memoria)
$brandQuery = $inventory->query("SELECT id, name FROM brands");
$inventoryBrands = $brandQuery->fetchAll(PDO::FETCH_ASSOC);

// Obtener productos de electrodr
$productos = $electrodr->query("SELECT * FROM productos")->fetchAll(PDO::FETCH_ASSOC);

foreach ($productos as $producto) {
    $productName = $producto['nombre'];
    $productImage = basename($producto['ruta_img']);
    $productStore = $producto['id_negocio'];
    $sellingPrice = $producto['precio'];
    $createdAt = $producto['fecha_modificacion'];
    $updatedAt = $producto['fecha_modificacion'];
    $productCode = substr(md5($productName . rand()), 0, 6);
    $categoryId = 6;
    $buyingDate = date('Y-m-d H:i:s');
    $buyingPrice = $sellingPrice;

    // Obtener descripción de marca desde electrodr
    $marcaId = $producto['id_marca'];
    $marcaStmt = $electrodr->prepare("SELECT descrip_marca FROM marca WHERE id_marca = ?");
    $marcaStmt->execute([$marcaId]);
    $marca = $marcaStmt->fetchColumn();

    // Buscar brand_id en inventory usando comparación insensible a mayúsculas
    $brandId = null;
    if ($marca) {
        foreach ($inventoryBrands as $invBrand) {
            if (strcasecmp(trim($invBrand['name']), trim($marca)) === 0) {
                $brandId = $invBrand['id'];
                break;
            }
        }
    }

    // Si no se encuentra marca, puedes decidir ignorar o continuar con null
    if ($brandId === null) {
        echo "Marca '{$marca}' no encontrada para producto '{$productName}' - se omite\n";
        continue;
    }

    // Verificar si el producto ya existe en inventory
    $existsStmt = $inventory->prepare("
        SELECT COUNT(*) FROM products 
        WHERE product_name = :product_name AND brand_id = :brand_id AND product_store = :product_store
    ");
    $existsStmt->execute([
        ':product_name' => $productName,
        ':brand_id' => $brandId,
        ':product_store' => $productStore
    ]);

    if ($existsStmt->fetchColumn() > 0) {
        // echo "Producto '{$productName}' ya existe (Marca: $marca, Brand ID: $brandId) - se omite\n";
        continue;
    }

    // Insertar en inventory.products
    $stmt = $inventory->prepare("
        INSERT INTO products (
            product_name, category_id, brand_id, product_code,
            product_image, product_store, buying_date, buying_price,
            selling_price, created_at, updated_at
        ) VALUES (
            :product_name, :category_id, :brand_id, :product_code,
            :product_image, :product_store, :buying_date, :buying_price,
            :selling_price, :created_at, :updated_at
        )
    ");

    $stmt->execute([
        ':product_name' => $productName,
        ':category_id' => $categoryId,
        ':brand_id' => $brandId,
        ':product_code' => $productCode,
        ':product_image' => $productImage,
        ':product_store' => $productStore,
        ':buying_date' => $buyingDate,
        ':buying_price' => $buyingPrice,
        ':selling_price' => $sellingPrice,
        ':created_at' => $createdAt,
        ':updated_at' => $updatedAt,
    ]);

    echo "Migrado: $productName (Marca: $marca, Brand ID: $brandId)\n";
}

echo "Migración finalizada.\n";
