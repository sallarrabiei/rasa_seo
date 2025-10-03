<?php
namespace BSS\Core;

class Autoloader
{
    /** @var string */
    private static $baseDir;

    public static function register(string $baseDir): void
    {
        self::$baseDir = rtrim($baseDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        spl_autoload_register([__CLASS__, 'autoload']);
    }

    public static function autoload(string $class): void
    {
        $prefix = 'BSS\\';
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }

        $relative = substr($class, $len);
        $relativePath = str_replace('\\', DIRECTORY_SEPARATOR, $relative) . '.php';
        $file = self::$baseDir . $relativePath;

        if (is_readable($file)) {
            require $file;
        }
    }
}

