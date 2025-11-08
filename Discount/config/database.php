<?php
/**
 * Database Configuration
 * Discount Management System
 */

return [
    'database' => [
        'type' => 'sqlite',
        'path' => __DIR__ . '/../storage/database/discount_system.db',
        'charset' => 'utf8',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    ],
    
    'tables' => [
        'stores' => 'stores',
        'tickets' => 'tickets', 
        'discounts' => 'discounts'
    ]
];