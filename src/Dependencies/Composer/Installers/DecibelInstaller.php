<?php
namespace MZ_Mobilize_America\Dependencies\Composer\Installers;

class DecibelInstaller extends BaseInstaller
{
    /** @var array */
    protected $locations = array(
        'app'    => 'app/{$name}/',
    );
}
