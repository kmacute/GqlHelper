<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitcf31dac105682e0ad6ec94445db1e8f8
{
    public static $prefixLengthsPsr4 = array (
        'K' => 
        array (
            'Kmacute\\GqlHelper\\' => 18,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Kmacute\\GqlHelper\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitcf31dac105682e0ad6ec94445db1e8f8::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitcf31dac105682e0ad6ec94445db1e8f8::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitcf31dac105682e0ad6ec94445db1e8f8::$classMap;

        }, null, ClassLoader::class);
    }
}
