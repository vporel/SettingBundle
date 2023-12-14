<?php
namespace SettingBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

/**
 * @author Vivian NKOUANANG (https://github.com/vporel) <dev.vporel@gmail.com>
 */
class SettingBundle extends AbstractBundle{

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
        ->children()
            ->scalarNode("settings_file_path")->isRequired()->end() //The config file that defines the settings keys
            ->scalarNode("definition_file_path")->isRequired()->end() //The config file that defines the settings keys
        ->end();

        /**
         * Definition file example
         * 
         * use SettingBundle\Entity\Setting;
         * 
         * return [
         *  Setting::int(...),
         *  Setting::string(...)
         * ];
         */
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import(dirname(__DIR__)."/config/services.yaml");
        $container->parameters()->set("setting", $config);
    }
    
}