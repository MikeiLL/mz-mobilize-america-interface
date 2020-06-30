<?php

namespace MZ_Mobilize_America\Dependencies\Composer\Installers;

class DframeInstaller extends BaseInstaller
{
    protected $locations = array(
        'module'  => 'modules/{$vendor}/{$name}/',
    );
}
