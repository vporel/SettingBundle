<?php
namespace SettingBundle\Twig;

use SettingBundle\SettingService;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class TwigExtension extends AbstractExtension implements GlobalsInterface{

    
    public function __construct(private SettingService $settingService){}

    public function getGlobals(): array
    {
        return [
            "SETTINGS" => $this->settingService
        ];
    }

}