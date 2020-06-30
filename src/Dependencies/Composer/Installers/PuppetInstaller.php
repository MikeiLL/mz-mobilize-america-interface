<?php

namespace MZ_Mobilize_America\Dependencies\Composer\Installers;

class PuppetInstaller extends BaseInstaller
{

    protected $locations = array(
        'module' => 'modules/{$name}/',
    );
}
