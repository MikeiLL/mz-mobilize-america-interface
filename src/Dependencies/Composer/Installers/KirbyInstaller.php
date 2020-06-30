<?php
namespace MZ_Mobilize_America\Dependencies\Composer\Installers;

class KirbyInstaller extends BaseInstaller
{
    protected $locations = array(
        'plugin'    => 'site/plugins/{$name}/',
        'field'    => 'site/fields/{$name}/',
        'tag'    => 'site/tags/{$name}/'
    );
}
