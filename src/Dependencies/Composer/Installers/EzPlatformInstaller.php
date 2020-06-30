<?php
namespace MZ_Mobilize_America\Dependencies\Composer\Installers;

class EzPlatformInstaller extends BaseInstaller
{
    protected $locations = array(
        'meta-assets' => 'web/assets/ezplatform/',
        'assets' => 'web/assets/ezplatform/{$name}/',
    );
}
