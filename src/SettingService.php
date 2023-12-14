<?php

namespace SettingBundle;

use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class SettingService{

    private $settings = [];
    private $path = null;
    /** @var Setting[] */
    private $definition = [];

    public function __construct(protected ParameterBagInterface $parameterBag)
    {
        $this->path = $parameterBag->get("setting")["settings_file_path"];

        $definition = require $parameterBag->get("setting")["definition_file_path"];
        if(!is_array($definition)) throw new \Exception("The settings definition file should return an array");
        foreach($definition as $def) $this->definition[$def->getKey()] = $def;

        if(file_exists($this->path)){
            $this->settings = json_decode(file_get_contents($this->path), true) ?? [];
        }else{
            file_put_contents($this->path, "{}");
            if(!is_writable($this->path)){
                unlink($this->path);
                throw new RuntimeException("The file '".$this->path."' doesn't exists. The creation has failed.");
            }else{
                $this->reset();
            }
        }
    }

    public function exists(string $key){
        return array_key_exists($key, $this->definition);
    }

    public function get(string $key){
        if(!$this->exists($key)) throw new InvalidArgumentException("The key '$key' is unknown. Check the settings definition file");
        
        return $this->settings[$key] ?? $this->definition[$key]->getDefaultValue();
    }

    /**
     * Retrive all the settings
     * @return array
     */
    public function all(){
        return $this->settings;
    }

    /**
     * Modifier un paramètre
     * Les modifications sont appliquées lors de l'appel de la méthode flush()
     * @param string $key
     * @param mixed $value
     * 
     * @return self
     */
    public function set(string $key, $value): self{
        if(!$this->exists($key)) throw new InvalidArgumentException("The key '$key' is unknown. Check the settings definition file");
        $this->settings[$key] = $value;
        
        return $this;
    }

    /**
     * Appliquer toute sles modification qui ont été faites
     * @return void
     */
    public function flush(): void{
        file_put_contents($this->path, json_encode($this->settings));
    }

    /**
     * @param string $key The setting that the definition is asked. If null, all the settings definitions are returned in an array
     */
    public function getDefinition(?string $key = null){
        if($key == null) return $this->definition;
        else{
            if(!$this->exists($key)) throw new InvalidArgumentException("The key '$key' is unknown. Check the settings definition file");
            return $this->definition[$key];
        }
    }

    /**
     * @return self
     */
    public function reset():self{
        foreach($this->definition as $key => $def){
            $this->settings[$key] = $def->getDefaultValue();
        }
        $this->flush();

        return $this;
    }
}