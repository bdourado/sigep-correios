<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit4bbcddffffd4512f164abb04b0070f48
{
    public static $prefixesPsr0 = array (
        'B' => 
        array (
            'Bdourado\\SigepCorreios' => 
            array (
                0 => __DIR__ . '/../..' . '/src',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixesPsr0 = ComposerStaticInit4bbcddffffd4512f164abb04b0070f48::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}