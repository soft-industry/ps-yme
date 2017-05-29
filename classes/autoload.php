<?php
/**
 * 2017 Soft Industry
 *
 *   @author    Skorobogatko Alexei <a.skorobogatko@soft-industry.com>
 *   @copyright 2017 Soft-Industry
 *   @license   http://opensource.org/licenses/afl-3.0.php
 *   @since     0.1.0
 */

// Register module class autoloader.
spl_autoload_register(function ($class) {

    $prefix = 'SI\\YandexMarket\\';
    $len = strlen($prefix);
    
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $split = explode('\\', substr($class, $len));
    
    // Get class base name.
    $class_name = array_pop($split);
    
    $file = __DIR__ .
            ($split ? DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $split) : '') .
            DIRECTORY_SEPARATOR . $class_name . '.php';
    
    if (file_exists($file)) {
        require_once $file;
    }
});