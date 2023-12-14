<?php

namespace SettingBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;

/**
 * To be registered in the app admin dashboard
 */
class SettingAdminMenu
{
    public static function getMenu(): array{
        return [
            MenuItem::section(""),
            MenuItem::linkToRoute("Paramètres du site", "fas fa-cog", "admin.settings")
        ];
    }
}