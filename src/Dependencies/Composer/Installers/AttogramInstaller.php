<?php
namespace MZ_Mobilize_America\Dependencies\Composer\Installers;

class AttogramInstaller extends BaseInstaller
{
    protected $locations = array(
        'module' => 'modules/{$name}/',
    );
}
