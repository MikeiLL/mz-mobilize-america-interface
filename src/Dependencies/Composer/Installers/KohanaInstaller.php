<?php
namespace MZ_Mobilize_America\Dependencies\Composer\Installers;

class KohanaInstaller extends BaseInstaller
{
    protected $locations = array(
        'module' => 'modules/{$name}/',
    );
}
