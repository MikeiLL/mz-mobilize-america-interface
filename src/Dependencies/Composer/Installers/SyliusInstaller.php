<?php
namespace MZ_Mobilize_America\Dependencies\Composer\Installers;

class SyliusInstaller extends BaseInstaller
{
    protected $locations = array(
        'theme' => 'themes/{$name}/',
    );
}
