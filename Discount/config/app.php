<?php
/**
 * Application Configuration
 * Discount Management System
 */

return [
    'app' => [
        'name' => 'Discount Management System',
        'version' => '1.0.0',
        'environment' => 'development',
        'timezone' => 'UTC'
    ],
    
    'discount' => [
        'allowed_percentages' => [10, 20, 30, 40, 50],
        'max_stack_per_ticket' => 10, // Maximum stacked discounts per ticket
        'max_total_percentage' => 100 // Maximum total percentage allowed
    ],
    
    'display' => [
        'recent_discounts_limit' => 20,
        'pagination_limit' => 10
    ]
];