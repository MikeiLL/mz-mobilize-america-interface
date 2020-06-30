<?php
namespace MZ_Mobilize_America\Dependencies\Composer\Installers;

/**
 *
 * Installer for kanboard plugins
 *
 * kanboard.net
 *
 * Class KanboardInstaller
 * @package Composer\Installers
 */
class KanboardInstaller extends BaseInstaller
{
    protected $locations = array(
        'plugin'  => 'plugins/{$name}/',
    );
}
