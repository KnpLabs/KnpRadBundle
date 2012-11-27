<?php

namespace Knp\Bundle\RadBundle\DependencyInjection\Extension;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * ContianerExtension configuration dumper.
 */
class ConfigurationDumper
{
    private $extension;
    private $configPath;
    private $templatesPath;

    public function __construct(ExtensionInterface $extension, $configPath)
    {
        $this->extension     = $extension;
        $this->configPath    = $configPath;
        $this->templatesPath = realpath(__DIR__.'/../../Resources/skeleton/config/'.$this->getAlias());

        if (!is_dir($this->templatesPath)) {
            $this->templatesPath = null;
        }
    }

    public function getAlias()
    {
        return $this->extension->getAlias();
    }

    public function isAlreadyDumped()
    {
        return file_exists($this->configPath.'/config/'.$this->getAlias().'.yml');
    }

    public function dump()
    {
        $output = sprintf('<comment>%s:</comment>', $this->getAlias())."\n";

        if (!file_exists($dir = $this->configPath.'/config')) {
            mkdir($dir, 0777, true);
        }
        if (!file_exists($dir = $this->configPath.'/routing')) {
            mkdir($dir, 0777, true);
        }

        if ($this->templatesPath) {
            $output .= $this->dumpProvidedConfig();
        } else {
            $output .= $this->dumpEmptyConfig();
        }

        return $output;
    }

    public function dumpProvidedConfig()
    {
        $configPath = $this->configPath.'/config/'.$this->getAlias().'.yml';
        file_put_contents($configPath, "\n".file_get_contents(
            $this->templatesPath.'/config.yml'
        ), FILE_APPEND);

        $output = sprintf('  <info>+config</info> %s',
            $this->getAlias().'.yml'
        );

        if (is_dir($routingTemplatesPath = $this->templatesPath.'/routing')) {
            foreach (glob($routingTemplatesPath.'/*.yml') as $name) {
                $configName = basename((string) $name);
                $configPath = $this->configPath.'/routing/'.$configName;

                file_put_contents($configPath, "\n".file_get_contents($name), FILE_APPEND);

                $output .= sprintf("\n".'  <info>+route</info>  %s',
                    $configName
                );
            }
        }

        if (file_exists($path = $this->templatesPath.'/information.txt')) {
            $output .= "\n\n  <info>information:</info>\n".rtrim(file_get_contents($path));
        }

        return $output;
    }

    public function dumpEmptyConfig()
    {
        $path = $this->configPath.'/config/'.$this->getAlias().'.yml';

        file_put_contents($path, <<<YAML
all:  ~
dev:  ~
test: ~
prod: ~

YAML
        );

        $output = sprintf('  <info>+config</info> %s', $this->getAlias().'.yml');
    }
}
