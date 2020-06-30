<?php
namespace MZ_Mobilize_America\Dependencies\Composer\Installers;

class LaravelInstaller extends BaseInstaller
{
    protected $locations = array(
        'library' => 'libraries/{$name}/',
    );
}
