<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit5bd746070384cfa286dba620435bd105
{
    public static $prefixLengthsPsr4 = array (
        'D' => 
        array (
            'Dotenv\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Dotenv\\' => 
        array (
            0 => __DIR__ . '/..' . '/vlucas/phpdotenv/src',
        ),
    );

    public static $prefixesPsr0 = array (
        'R' => 
        array (
            'Requests' => 
            array (
                0 => __DIR__ . '/..' . '/rmccue/requests/library',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit5bd746070384cfa286dba620435bd105::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit5bd746070384cfa286dba620435bd105::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit5bd746070384cfa286dba620435bd105::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}
