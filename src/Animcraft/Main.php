<?php
namespace Animcraft;
use Animcraft\Events\EventListener;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener{
    private $config;
    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
        if(!file_exists($this->getDataFolder() . "config.yml")){
            $this->saveResource("config.yml");
        }
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
    }
    public function getMyConfig(): Config{
        return $this->config;
    }
    public function changeItem(PlayerInteractEvent $event){
        $item = $event->getItem();
        if(in_array($item->getId(), array_keys($this->getMyConfig()->getAll()))){
            $itemArray = $this->getMyConfig()->getAll()[$item->getId()];
            if(isset($itemArray["effects"])){
                $effects = $itemArray["effects"];
                foreach($effects as $effectId => $effectArray){
                $effect = Effect::getEffect($effectId);
                $duration = $effectArray["duration"];
                $amplifier = $effectArray["amplifier"];
                $visible = $effectArray["visible"];
                $effectInstance = new EffectInstance($effect, $duration * 20, $amplifier, $visible);
                $event->getPlayer()->addEffect($effectInstance);
                }
            }
            if(isset($itemArray["heal"])){
                if(!$event->getPlayer()->getHealth() + $itemArray["heal"] > 20){
                    $event->getPlayer()->setHealth(20);
                }
            else{
                $event->getPlayer()->setHealth($event->getPlayer()->getHealth() + $itemArray["heal"]);
                }
            }
        }
    }
}