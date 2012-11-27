<?php

namespace Composer;

use Sensio\Bundle\DistributionBundle\Composer\ScriptHandler as BaseScriptHandler;

class ScriptHandler extends BaseScriptHandler
{
    public static function installParametersFile($event)
    {
        $options = self::getOptions($event);
        $appDir = $options['symfony-app-dir'];

        if (!is_dir($appDir)) {
            echo 'The symfony-app-dir ('.$appDir.') specified in composer.json was not found in '.getcwd().', can not install the parameters.yml file.'.PHP_EOL;

            return;
        }

        if (is_file($appDir.'/config/parameters.yml.dist') && !is_file($appDir.'/config/parameters.yml')) {
            copy($appDir.'/config/parameters.yml.dist', $appDir.'/config/parameters.yml');
        }
    }
}
