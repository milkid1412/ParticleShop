<?php
declare(strict_types=1);

namespace Kkevin14\ParticleShop\form;

use Kkevin14\ParticleShop\item\ParticleCoinItem;
use Kkevin14\ParticleShop\ParticleShop;
use pocketmine\form\Form;
use pocketmine\player\Player;

class MultiParticlePermissionBuyForm implements Form
{
    public ParticleShop $owner;

    public function __construct(ParticleShop $owner)
    {
        $this->owner = $owner;
    }

    public function jsonSerialize()
    {
        return [
            'type' => 'modal',
            'title' => $this->owner->title,
            'content' => '§f멀티파티클 권한을 구매하려면 ' . $this->owner->multi_particle_cost . '개의 코인을 사용해야 합니다. 진행하시겠습니까?',
            'button1' => '네',
            'button2' => '아니요'
        ];
    }

    public function handleResponse(Player $player, $data): void
    {
        if($data){
            $item = new ParticleCoinItem();
            $item->setCount($this->owner->multi_particle_cost);
            if(!$player->getInventory()->contains($item)){
                $this->owner->msg($player, '파티클코인이 부족합니다.');
                return;
            }
            $player->getInventory()->removeItem($item);
            $this->owner->db['player'][strtolower($player->getName())]['multi_particle'] = false;
            $this->owner->msg($player, '멀티파티클 권한을 구매했습니다.');
        }
    }
}