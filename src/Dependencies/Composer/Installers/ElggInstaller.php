<?php
namespace MZ_Mobilize_America\Dependencies\Composer\Installers;

class ElggInstaller extends BaseInstaller
{
    protected $locations = array(
        'plugin' => 'mod/{$name}/',
    );
}
