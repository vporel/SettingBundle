<?php
namespace SettingBundle\Controller;

use RootBundle\Library\FileUpload;
use RootBundle\Library\FileUploadException;
use RootBundle\Controller\AbstractApiController;
use SettingBundle\Entity\SettingImage;
use SettingBundle\SettingService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SettingApiController extends AbstractApiController{

    public function __construct(private ParameterBagInterface $parameterBag, private SettingService $settingService){}

    public function getAll()
    {
        $data = [];
        foreach($this->settingService->getDefinition() as $key => $def) $data[] = array_merge(json_decode($this->serialize($def, "default"), true), ["value" => $this->settingService->get($key)]);
        return $this->success($data);
    }

    #[Route("/api/settings/{key}/edit", name: "api.settings.edit", requirements: ["key" => "[a-zA-Z-0-9-]{2,}"], priority: 100)]
    public function edit(Request $request, string $key)
    {
        if(!$this->settingService->exists($key)) return $this->error("The key '$key' is unknown");
        $value = $request->request->get("value");
        $definition = $this->settingService->getDefinition($key);
        if($definition instanceof SettingImage)
            return new JsonResponse($definition->isMultiple() ? $this->editMultipleImages($request, $definition) : $this->editUniqueImage($request, $definition));
        $this->settingService->set($key, $value);
        $this->settingService->flush();
        return new JsonResponse($this->success($value));
    }

    private function editUniqueImage(Request $request, SettingImage $definition){
        $this->settingService = $this->settingService;
        $currentImage = $this->settingService->get($definition->getKey());
        if (!$request->files->get("image")) return $this->error("no_image");
        try {
            $imageName = FileUpload::uploadFile($request->files->get("image"), $this->parameterBag->get("public_dir").$definition->getFolder(), [
                "extensions" => $definition->getExtensions()
            ]);
            $this->settingService->set($definition->getKey(), $imageName);
            $this->settingService->flush();
            if($currentImage ?? "" != "" && file_exists($this->parameterBag->get("public_dir").$definition->getFolder() . "/" . $currentImage)) unlink($this->parameterBag->get("public_dir").$definition->getFolder() . "/" . $currentImage);
            return $this->success($imageName);
        } catch (FileUploadException $e) {
            if ($e->getCode() == FileUploadException::EXTENSION) return $this->error("bad_file_extension [". $e->getParam().']');
        }
    }

    public function editMultipleImages(Request $request, SettingImage $definition){
        $this->settingService = $this->settingService;
        $currentImages = $this->settingService->get($definition->getKey());
        $imagesNames = [];
        if ($request->files) {
            foreach ($request->files as $image) {
                try {
                    $imagesNames[] = FileUpload::uploadFile($image, $this->parameterBag->get("public_dir").$definition->getFolder(), [
                        "extensions" => [".jpg", ".png", ".jpeg"]
                    ]);
                } catch (FileUploadException $e) {
                    if ($e->getCode() == FileUploadException::EXTENSION) return $this->error("bad_file_extension [". $e->getParam().']');
                }
            }
        }
        $imagesToRemove = [];
        if ($request->request->get("imagesToRemove") != "") {
            $imagesToRemove = explode(",", $request->request->get("imagesToRemove"));
            $currentImages = array_filter($currentImages, function ($img) use ($imagesToRemove) {
                return !in_array($img, $imagesToRemove);
            });
        }
        $newImages = array_merge($currentImages, $imagesNames);
        $this->settingService->set($definition->getKey(), $newImages);
        $this->settingService->flush();
        foreach ($imagesToRemove as $file) {
            if($file ?? "" != "" && file_exists($this->parameterBag->get("public_dir").$definition->getFolder() . "/" . $file))  unlink($this->parameterBag->get("public_dir").$definition->getFolder() . "/" . $file); //Delete the files to remove
        }
        return $this->success($newImages);
    }
}