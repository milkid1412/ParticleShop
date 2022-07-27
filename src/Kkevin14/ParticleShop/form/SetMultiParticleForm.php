<?php
declare(strict_types=1);

namespace Kkevin14\ParticleShop\form;

use Kkevin14\ParticleShop\ParticleShop;
use pocketmine\form\Form;
use pocketmine\player\Player;

class SetMultiParticleForm implements Form
{
    public ParticleShop $owner;

    public Player $player;

    public function __construct(ParticleShop $owner, Player $player)
    {
        $this->owner = $owner;
        $this->player = $player;
    }

    public function jsonSerialize()
    {
        $option = [];
        foreach($this->owner->db['player'][strtolower($this->player->getName())]['particles'] as $value){
            $option[] = $this->owner->particleQueue[$value];
        }
        return [
            'type' => 'custom_form',
            'title' => $this->owner->title,
            'content' => [
                [
                    'type' => 'label',
                    'text' => '멀티파티클을 설정합니다. 아래에서 두 개의 파티클을 선택해주세요.' . "\n\n" . '멀티파티클이란?' . "\n" . '한 번에 두 종류의 파티클을 사용하는 기능입니다.'
                ],
                [
                    'type' => 'dropdown',
                    'text' => '§b첫 번째 파티클을 선택해주세요.',
                    'options' => $option
                ],
                [
                    'type' => 'dropdown',
                    'text' => '§b두 번째 파티클을 선택해주세요.',
                    'options' => $option
                ]
            ]
        ];
    }

    public function handleResponse(Player $player, $data): void
    {
        if(!isset($data[1]) || !isset($data[2])){
            $this->owner->msg($player, '파티클을 두 개 모두 선택해주세요.');
            return;
        }
        if($data[1] === $data[2]){
            $this->owner->msg($player, '서로 다른 파티클을 선택해주세요.');
            return;
        }
        $name = strtolower($player->getName());
        $this->owner->db['player'][$name]['multi_particle'] = true;
        $this->owner->db['player'][$name]['current_particle'] = [
            $this->owner->db['player'][$name]['particles'][$data[1]],
            $this->owner->db['player'][$name]['particles'][$data[2]]
        ];
        $this->owner->msg($player, '멀티파티클이 설정되었습니다.');
        $this->owner->msg($player, '설정된 파티클: ' . $this->owner->particleQueue[$this->owner->db['player'][$name]['particles'][$data[1]]] . ' | ' . $this->owner->particleQueue[$this->owner->db['player'][$name]['particles'][$data[2]]]);
    }
}