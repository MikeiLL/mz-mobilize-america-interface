<?php
namespace MZ_Mobilize_America\Dependencies\Composer\Installers;

class ZikulaInstaller extends BaseInstaller
{
    protected $locations = array(
        'module' => 'modules/{$vendor}-{$name}/',
        'theme'  => 'themes/{$vendor}-{$name}/'
    );
}
