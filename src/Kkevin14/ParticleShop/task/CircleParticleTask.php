<?php
declare(strict_types=1);

namespace Kkevin14\ParticleShop\task;

use Kkevin14\ParticleShop\ParticleShop;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;

class CircleParticleTask extends Task
{
    public ParticleShop $owner;
    public Player $player;
    public int $currentTime, $maxTime;
    public int|float $previousY;
    public array $data;

    public function __construct(ParticleShop $owner, Player $player, int $currentTime, int $maxTime, int|float $previousY, array $data)
    {
        $this->owner = $owner;
        $this->player = $player;
        $this->currentTime = $currentTime;
        $this->maxTime = $maxTime;
        $this->previousY = $previousY;
        $this->data = $data;
    }

    public function onRun(): void
    {
        if($this->player->getLocation()->getY() < $this->previousY) return;
        $id = $this->data[0];
        $rgb = $this->data[1];
        for($theta = 0; $theta <= pi() * 2; $theta += pi() / 8)
            $this->owner->getServer()->broadcastPackets($this->player->getWorld()->getPlayers(), [LevelEventPacket::standardParticle($id, is_array($rgb) ? ((255 << 24) | (mt_rand(150, 255) << 16) | (mt_rand(150, 255) << 8) | mt_rand(150, 255)) : $rgb, $this->player->getLocation()->add(cos($theta) * $this->currentTime * 0.2, 0.3, sin($theta) * $this->currentTime * 0.2))]);
        if(++$this->currentTime > $this->maxTime) return;
        $this->owner->getScheduler()->scheduleDelayedTask(new CircleParticleTask($this->owner, $this->player, $this->currentTime, $this->maxTime, $this->player->getLocation()->getY(), $this->data), 1);
    }
}