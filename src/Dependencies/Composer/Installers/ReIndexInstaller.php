<?php
namespace MZ_Mobilize_America\Dependencies\Composer\Installers;

class ReIndexInstaller extends BaseInstaller
{
    protected $locations = array(
        'theme'     => 'themes/{$name}/',
        'plugin'    => 'plugins/{$name}/'
    );
}
