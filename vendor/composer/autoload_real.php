<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInit02f22e6da33b9a4cb98757e73ece8a57
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        require __DIR__ . '/platform_check.php';

        spl_autoload_register(array('ComposerAutoloaderInit02f22e6da33b9a4cb98757e73ece8a57', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInit02f22e6da33b9a4cb98757e73ece8a57', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInit02f22e6da33b9a4cb98757e73ece8a57::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
