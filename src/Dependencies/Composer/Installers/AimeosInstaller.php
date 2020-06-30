<?php
namespace MZ_Mobilize_America\Dependencies\Composer\Installers;

class AimeosInstaller extends BaseInstaller
{
    protected $locations = array(
        'extension'   => 'ext/{$name}/',
    );
}
