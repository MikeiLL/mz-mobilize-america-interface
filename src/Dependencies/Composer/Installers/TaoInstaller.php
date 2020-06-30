<?php
namespace MZ_Mobilize_America\Dependencies\Composer\Installers;

/**
 * An installer to handle TAO extensions.
 */
class TaoInstaller extends BaseInstaller
{
    protected $locations = array(
        'extension' => '{$name}'
    );
}
