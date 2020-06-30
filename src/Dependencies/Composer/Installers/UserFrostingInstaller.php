<?php
namespace MZ_Mobilize_America\Dependencies\Composer\Installers;

class UserFrostingInstaller extends BaseInstaller
{
    protected $locations = array(
        'sprinkle' => 'app/sprinkles/{$name}/',
    );
}
