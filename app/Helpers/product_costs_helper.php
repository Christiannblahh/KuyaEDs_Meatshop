<?php

/**
 * Product Cost Prices Helper
 * Maps product names to their cost prices for expense tracking
 */

if (!function_exists('get_product_cost_price')) {
    /**
     * Get the cost price for a product by name
     * 
     * @param string $productName The name of the product
     * @return float|null The cost price per unit, or null if not found
     */
    function get_product_cost_price(string $productName): ?float
    {
        $costMapping = [
            'Chicken Wings' => 45.00,   // ₱45 per kl
            'Chicken Feet' => 30.00,    // ₱30 per kl
            'Baticolon' => 25.00,       // ₱25 per kl
            'Whole Chicken' => 100.00,  // ₱100 per kl
            'Chorizo' => 80.00,         // ₱80 per pack
            'Kikiam' => 40.00,          // ₱40 per pack
            'Hotdog' => 70.00,          // ₱70 per pack
        ];

        // Case-insensitive search
        $productName = trim($productName);
        foreach ($costMapping as $name => $price) {
            if (strcasecmp($name, $productName) === 0) {
                return $price;
            }
        }

        return null;
    }
}

if (!function_exists('get_all_product_cost_prices')) {
    /**
     * Get all product cost price mappings
     * 
     * @return array Array of product name => cost price mappings
     */
    function get_all_product_cost_prices(): array
    {
        return [
            'Chicken Wings' => 45.00,   // ₱45 per kl
            'Chicken Feet' => 30.00,    // ₱30 per kl
            'Baticolon' => 25.00,       // ₱25 per kl
            'Whole Chicken' => 100.00,  // ₱100 per kl
            'Chorizo' => 80.00,         // ₱80 per pack
            'Kikiam' => 40.00,          // ₱40 per pack
            'Hotdog' => 70.00,          // ₱70 per pack
        ];
    }
}

if (!function_exists('calculate_stock_expense')) {
    /**
     * Calculate the total expense for adding stock
     * 
     * @param string $productName The name of the product
     * @param float $quantity The quantity being added
     * @return float The total expense (quantity × cost price per unit)
     */
    function calculate_stock_expense(string $productName, float $quantity): float
    {
        $costPrice = get_product_cost_price($productName);
        
        if ($costPrice === null) {
            return 0.00;
        }
        
        return $quantity * $costPrice;
    }
}