<?php
namespace MZ_Mobilize_America\Dependencies\Composer\Installers;

class LavaLiteInstaller extends BaseInstaller
{
    protected $locations = array(
        'package' => 'packages/{$vendor}/{$name}/',
        'theme'   => 'public/themes/{$name}/',
    );
}
