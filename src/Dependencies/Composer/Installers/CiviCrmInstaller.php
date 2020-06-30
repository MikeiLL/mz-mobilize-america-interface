<?php
namespace MZ_Mobilize_America\Dependencies\Composer\Installers;

class CiviCrmInstaller extends BaseInstaller
{
    protected $locations = array(
        'ext'    => 'ext/{$name}/'
    );
}
