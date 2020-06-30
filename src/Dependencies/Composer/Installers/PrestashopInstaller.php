<?php
namespace MZ_Mobilize_America\Dependencies\Composer\Installers;

class PrestashopInstaller extends BaseInstaller
{
    protected $locations = array(
        'module' => 'modules/{$name}/',
        'theme'  => 'themes/{$name}/',
    );
}
