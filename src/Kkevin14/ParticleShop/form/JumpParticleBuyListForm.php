<?php
declare(strict_types=1);

namespace Kkevin14\ParticleShop\form;

use Kkevin14\ParticleShop\item\ParticleCoinItem;
use Kkevin14\ParticleShop\ParticleShop;
use pocketmine\form\Form;
use pocketmine\player\Player;

class JumpParticleBuyListForm implements Form
{
    public ParticleShop $owner;

    public function __construct(ParticleShop $owner)
    {
        $this->owner = $owner;
    }

    public function jsonSerialize(): array
    {
        $buttons = [];
        foreach($this->owner->jump_particles as $value){
            $buttons[] = array('text' => $this->owner->buttonFormat($value['name']) . "\n" . '§b가격: ' . $value['price'] . '개');
        }
        $buttons[] = array('text' => $this->owner->buttonFormat('창 닫기'));
        return [
            'type' => 'form',
            'title' => $this->owner->title,
            'content' => $this->owner->contentFormat('구매하실 점프파티클을 선택하세요.'),
            'buttons' => $buttons
        ];
    }

    public function handleResponse(Player $player, $data): void
    {
        if($data === null || !isset($this->owner->jump_particles[$data])){
            $this->owner->msg($player, '창을 닫으셨습니다.');
            return;
        }
        $name = strtolower($player->getName());
        if(in_array($data, $this->owner->db['player'][$name]['jump_particles'])){
            $this->owner->msg($player, '당신은 이미 해당 점프 파티클을 소유하고있습니다.');
            return;
        }
        $item = new ParticleCoinItem();
        $item->setCount($this->owner->jump_particles[$data]['price']);
        if(!$player->getInventory()->contains($item)){
            $this->owner->msg($player, '파티클코인이 부족합니다.');
            return;
        }
        $player->getInventory()->removeItem($item);
        $this->owner->db['player'][$name]['jump_particles'][] = $data;
        sort($this->owner->db['player'][$name]['jump_particles']);
        $this->owner->msg($player, '성공적으로 ' . $this->owner->jump_particles[$data]['name'] . ' 파티클을 구매하셨습니다.');
    }
}