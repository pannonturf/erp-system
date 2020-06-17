<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitde0821245ad18cd56d59900c91fd4ab1
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitde0821245ad18cd56d59900c91fd4ab1::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitde0821245ad18cd56d59900c91fd4ab1::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
