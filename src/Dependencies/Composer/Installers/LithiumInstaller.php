<?php
namespace MZ_Mobilize_America\Dependencies\Composer\Installers;

class LithiumInstaller extends BaseInstaller
{
    protected $locations = array(
        'library' => 'libraries/{$name}/',
        'source'  => 'libraries/_source/{$name}/',
    );
}
