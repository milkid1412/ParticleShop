<?php
declare(strict_types=1);

namespace Kkevin14\ParticleShop;

use Kkevin14\ParticleShop\item\ParticleCoinItem;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerJumpEvent;

class EventListener implements Listener
{
    public ParticleShop $owner;

    public function __construct(ParticleShop $owner)
    {
        $this->owner = $owner;
    }

    public function onJoin(PlayerJoinEvent $event): void
    {
        $player = $event->getPlayer();
        $name = strtolower($player->getName());
        if(!isset($this->owner->db['player'][$name])){
            $this->owner->db['player'][$name] = [
                'particles' => [],
                'jump_particles' => [],
                'current_particle' => null,
                'current_jump_particle' => null,
                'multi_particle' => null
            ];
        }
    }

    public function onJump(PlayerJumpEvent $event): void
    {
        $player = $event->getPlayer();
        $name = strtolower($player->getName());
        $jump_particle = $this->owner->db['player'][$name]['current_jump_particle'] ?? null;
        if(!is_null($jump_particle)){
            $data = $this->owner->jump_particles[$jump_particle];
            $this->owner->addJumpParticle($player, $data['particle'], $data['type'], $data['data']);
        }
    }
}