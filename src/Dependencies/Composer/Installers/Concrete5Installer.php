<?php
namespace MZ_Mobilize_America\Dependencies\Composer\Installers;

class Concrete5Installer extends BaseInstaller
{
    protected $locations = array(
        'core'       => 'concrete/',
        'block'      => 'application/blocks/{$name}/',
        'package'    => 'packages/{$name}/',
        'theme'      => 'application/themes/{$name}/',
        'update'     => 'updates/{$name}/',
    );
}
