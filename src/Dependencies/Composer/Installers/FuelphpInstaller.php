<?php
namespace MZ_Mobilize_America\Dependencies\Composer\Installers;

class FuelphpInstaller extends BaseInstaller
{
    protected $locations = array(
        'component'  => 'components/{$name}/',
    );
}
