<?php

namespace SettingBundle\Entity;

use Symfony\Component\Serializer\Annotation as Serializer;
use ApiPlatform\Metadata as Api;
use SettingBundle\Controller\SettingApiController;

#[
    Api\ApiResource(paginationEnabled: false),
    Api\GetCollection(
        controller: SettingApiController::class."::getAll", read: false,
        openapiContext: [
            "responses" => ["200" => ["content" => ["application/json" => ["schema" => ["type" => "object", "properties" => [
                "key" => ["type" => "string"],
                "value" => ["type" => "string", "description" => "Dépend du type du paramètre"],
                "name" => ["type" => "string"],
                "type" => ["type" => "string"],
                "defaultValue" => ["type" => "string", "description" => "Dépend du type du paramètre"],
                "helpText" => ["type" => "string"],
            ]]]]]]
        ]
    ),
    Api\Post(
        routeName: "api.settings.edit",
        denormalizationContext: ["groups" => []], 
        openapiContext: [
            "requestBody" => ["content" => ["multipart/form-data" => ["schema" => ["type" => "object", "properties" => [
                "value" => ["type" => "string", "description" => "Si ce n'est pas une image (dépend du type du paramètre)"],
                "image" => ["type" => "string", "description" => "Si le paramètre ets une image. Pour plusieurs images, pas besoin de clés. Toutes les images dans la requête seront prises en compte"]
            ]]]]],
            "responses" => ["200" => ["content" => ["application/json" => ["schema" => ["type" => "string|array", "description" => "The value formatted or the image(s) file(s) name(s)"]]]]]
        ]
    )
]

class Setting
{
    public const TYPES = ["bool", "int", "float", "string", "text", "array"];

    public function __construct(
        #[Serializer\Groups(["default"])]
        protected string $type,
        #[Serializer\Groups(["default"])]
        protected string $key,
        #[Serializer\Groups(["default"])]
        protected string $name,
        #[Serializer\Groups(["default"])]
        protected mixed $defaultValue,
        #[Serializer\Groups(["default"])]
        protected string $helpText = ""
    ) {
        if (!in_array($type, self::TYPES)) throw new \InvalidArgumentException("The type '$type' is unknown");
    }

    /**
     * Get the value of name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the value of type
     */
    public function getType()
    {
        return $this->type;
    }
    /**
     * Get the value of defaultValue
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * Get the value of helpText
     */
    public function getHelpText()
    {
        return $this->helpText;
    }


    /**
     * Get the value of key
     */
    public function getKey()
    {
        return $this->key;
    }
}
