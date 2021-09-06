<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInitf1640175f625fada43e0bece341f846d
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\AutoloadWPMediaImagifyWordPressPlugin\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\AutoloadWPMediaImagifyWordPressPlugin\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        spl_autoload_register(array('ComposerAutoloaderInitf1640175f625fada43e0bece341f846d', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\AutoloadWPMediaImagifyWordPressPlugin\ClassLoader();
        spl_autoload_unregister(array('ComposerAutoloaderInitf1640175f625fada43e0bece341f846d', 'loadClassLoader'));

        $useStaticLoader = PHP_VERSION_ID >= 50600 && !defined('HHVM_VERSION') && (!function_exists('zend_loader_file_encoded') || !zend_loader_file_encoded());
        if ($useStaticLoader) {
            require_once __DIR__ . '/autoload_static.php';

            call_user_func(\Composer\Autoload\ComposerStaticInitf1640175f625fada43e0bece341f846d::getInitializer($loader));
        } else {
            $classMap = require __DIR__ . '/autoload_classmap.php';
            if ($classMap) {
                $loader->addClassMap($classMap);
            }
        }

        $loader->setClassMapAuthoritative(true);
        $loader->register(true);

        return $loader;
    }
}
