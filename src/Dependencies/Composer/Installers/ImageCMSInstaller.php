<?php
namespace MZ_Mobilize_America\Dependencies\Composer\Installers;

class ImageCMSInstaller extends BaseInstaller
{
    protected $locations = array(
        'template'    => 'templates/{$name}/',
        'module'      => 'application/modules/{$name}/',
        'library'     => 'application/libraries/{$name}/',
    );
}
