<?php
namespace MZ_Mobilize_America\Dependencies\Composer\Installers;

class BonefishInstaller extends BaseInstaller
{
    protected $locations = array(
        'package'    => 'Packages/{$vendor}/{$name}/'
    );
}
