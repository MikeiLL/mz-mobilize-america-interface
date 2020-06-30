<?php
namespace MZ_Mobilize_America\Dependencies\Composer\Installers;

class PPIInstaller extends BaseInstaller
{
    protected $locations = array(
        'module' => 'modules/{$name}/',
    );
}
