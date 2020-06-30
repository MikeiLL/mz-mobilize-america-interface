<?php
namespace MZ_Mobilize_America\Dependencies\Composer\Installers;

class KnownInstaller extends BaseInstaller
{
    protected $locations = array(
        'plugin'    => 'IdnoPlugins/{$name}/',
        'theme'     => 'Themes/{$name}/',
        'console'   => 'ConsolePlugins/{$name}/',
    );
}
