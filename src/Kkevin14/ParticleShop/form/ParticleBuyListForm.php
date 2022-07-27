<?php
declare(strict_types=1);

namespace Kkevin14\ParticleShop\form;

use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use Kkevin14\ParticleShop\item\ParticleCoinItem;
use Kkevin14\ParticleShop\ParticleShop;
use pocketmine\form\Form;
use pocketmine\player\Player;

class ParticleBuyListForm implements Form
{
    public ParticleShop $owner;

    public function __construct(ParticleShop $owner)
    {
        $this->owner = $owner;
    }

    #[Pure] #[ArrayShape(['type' => "string", 'title' => "string", 'content' => "string", 'buttons' => "array"])] public function jsonSerialize(): array
    {
        $buttons = array();
        foreach($this->owner->particleQueue as $key => $particle){
            $buttons[] = array('text' => $this->owner->buttonFormat($particle) . "\n" . '§b가격: ' . $this->owner->dataQueue[$key]['price'] . '개');
        }
        $buttons[] = array('text' => $this->owner->buttonFormat('창 닫기'));
        return [
            'type' => 'form',
            'title' => $this->owner->title,
            'content' => $this->owner->contentFormat('구매하실 파티클을 선택하세요.'),
            'buttons' => $buttons
        ];
    }

    public function handleResponse(Player $player, $data): void
    {
        if($data === null || !isset($this->owner->particleQueue[$data])){
            $this->owner->msg($player, '창을 닫으셨습니다.');
            return;
        }
        $name = strtolower($player->getName());
        if(in_array($data, $this->owner->db['player'][$name]['particles'])){
            $this->owner->msg($player, '당신은 이미 해당 파티클을 소유하고있습니다.');
            return;
        }
        $item = new ParticleCoinItem();
        $item->setCount($this->owner->dataQueue[$data]['price']);
        if(!$player->getInventory()->contains($item)){
            $this->owner->msg($player, '파티클코인이 부족합니다.');
            return;
        }
        $player->getInventory()->removeItem($item);
        $this->owner->db['player'][$name]['particles'][] = $data;
        sort($this->owner->db['player'][$name]['particles']);
        $this->owner->msg($player, '성공적으로 ' . $this->owner->particleQueue[$data] . ' 파티클을 구매하셨습니다.');
    }
}