<?php
// Helper function untuk load settings dari database
// Include file ini di halaman yang butuh settings

if (!function_exists('getSettings')) {
    function getSettings() {
        global $conn;
        static $settings = null;
        
        if ($settings === null) {
            $settings = [];
            $query = "SELECT setting_key, setting_value FROM settings";
            $result = mysqli_query($conn, $query);
            
            if ($result) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $settings[$row['setting_key']] = $row['setting_value'];
                }
            }
        }
        
        return $settings;
    }
}

if (!function_exists('getSetting')) {
    function getSetting($key, $default = '') {
        $settings = getSettings();
        return $settings[$key] ?? $default;
    }
}
