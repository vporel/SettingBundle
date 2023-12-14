<?php

namespace SettingBundle\Entity;

use Symfony\Component\Serializer\Annotation as Serializer;
use ApiPlatform\Metadata as Api;


#[Api\ApiResource(operations: [])]
class SettingImage extends Setting
{

    public function __construct(
        protected string $key,
        protected string $name,
        
        /** From public folder */
        #[Serializer\Groups(["default"])]
        protected string $folder,

        /** extensions with dots */
        #[Serializer\Groups(["default"])]
        protected array $extensions,

        #[Serializer\Groups(["default"])]
        protected bool $multiple = false,
        
        #[Serializer\Groups(["default"])]
        protected string $helpText = ""
    ) {
        $this->type = "image";
        $this->defaultValue = $multiple ? [] : null;
    }

    public function getFolder(){ return $this->folder; }

    public function getExtensions(){ return $this->extensions; }

    public function isMultiple(){ return $this->multiple; }
}
