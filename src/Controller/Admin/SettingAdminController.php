<?php
namespace SettingBundle\Controller\Admin;

use RootBundle\Controller\AbstractController;
use SettingBundle\SettingService;
use Symfony\Component\Routing\Annotation\Route;

class SettingAdminController extends AbstractController{

    #[Route("/admin/settings", name:"admin.settings", methods: ["GET"], priority: 100)]
    public function index()
    {
        return $this->render("@Setting/admin-index");
    }
}