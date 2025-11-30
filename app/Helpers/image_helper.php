<?php

if (!function_exists('product_image_url')) {
    /**
     * Get the full URL for a product image
     * Handles both uploaded files and external URLs
     *
     * @param string|null $imageUrl The image URL from database
     * @return string Full URL to the image
     */
    function product_image_url(?string $imageUrl): string
    {
        if (empty($imageUrl)) {
            return base_url('assets/images/no-image.png'); // Default placeholder
        }
        
        // If it's already a full URL (http/https), return as is
        if (strpos($imageUrl, 'http://') === 0 || strpos($imageUrl, 'https://') === 0) {
            return $imageUrl;
        }
        
        // If it's a relative path (uploads/products/...), serve via image route
        if (strpos($imageUrl, 'uploads/products/') === 0) {
            $filename = basename($imageUrl);
            return base_url('image/' . $filename);
        }
        
        // If it's just a filename, assume it's in products folder
        if (strpos($imageUrl, '/') === false) {
            return base_url('image/' . $imageUrl);
        }
        
        // Default: try to serve via image route
        $filename = basename($imageUrl);
        return base_url('image/' . $filename);
    }
}

