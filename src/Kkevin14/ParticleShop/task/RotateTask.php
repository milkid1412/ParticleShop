<?php
declare(strict_types=1);

namespace Kkevin14\ParticleShop\task;

use pocketmine\scheduler\Task;
use Kkevin14\ParticleShop\ParticleShop;

class RotateTask extends Task
{
    public ParticleShop $owner;

    public string $type = '+';

    public function __construct(ParticleShop $owner)
    {
        $this->owner = $owner;
    }

    public function onRun(): void
    {
        $this->owner->num += pi() / 10;
        if($this->owner->num >= 2 * pi()){
            $this->owner->num -= 2 * pi();
        }
        if($this->type === '+'){
            $this->owner->y += 1;
        }else{
            $this->owner->y -= 1;
        }
        if($this->owner->y <= 0){
            $this->type = '+';
        }elseif($this->owner->y >= 20){
            $this->type = '-';
        }
        foreach($this->owner->getServer()->getOnlinePlayers() as $player){
            $name = strtolower($player->getName());
            $particle = $this->owner->db['player'][$name]['current_particle'] ?? null;
            if(is_null($particle)) continue;
            if($this->owner->db['player'][$name]['multi_particle'] === true){
                foreach($particle as $value){
                    $data = $this->owner->dataQueue[$value];
                    $this->owner->addParticle($player, $data['particle'], $data['type'], $data['data']);
                }
                continue;
            }
            $data = $this->owner->dataQueue[$particle];
            $this->owner->addParticle($player, $data['particle'], $data['type'], $data['data']);
        }
    }
}