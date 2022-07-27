<?php
declare(strict_types=1);

namespace Kkevin14\ParticleShop\task;

use Kkevin14\ParticleShop\ParticleShop;
use pocketmine\scheduler\Task;

class AddParticleTask extends Task
{
    public ParticleShop $owner;

    public function __construct(ParticleShop $owner)
    {
        $this->owner = $owner;
    }

    public function onRun(): void
    {
        if(!empty($this->owner->pk)){
            $this->owner->getServer()->broadcastPackets($this->owner->getServer()->getOnlinePlayers(), $this->owner->pk);
            $this->owner->pk = [];
        }
    }
}