<?php
namespace MZ_Mobilize_America\Dependencies\Composer\Installers;

class ItopInstaller extends BaseInstaller
{
    protected $locations = array(
        'extension'    => 'extensions/{$name}/',
    );
}
