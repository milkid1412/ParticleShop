<?php
declare(strict_types=1);

namespace Kkevin14\ParticleShop\form;

use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use Kkevin14\ParticleShop\ParticleShop;
use pocketmine\form\Form;
use pocketmine\player\Player;

class SelectParticleForm implements Form
{
    public ParticleShop $owner;
    
    public Player $player;
    
    public function __construct(ParticleShop $owner, Player $player)
    {
        $this->owner = $owner;
        $this->player = $player;
    }

    #[Pure] #[ArrayShape(['type' => "string", 'title' => "string", 'content' => "string", 'buttons' => "array"])] public function jsonSerialize(): array
    {
        $buttons = array();
        $buttons[] = array('text' => $this->owner->buttonFormat('파티클 제거'));
        if(!empty($this->owner->db['player'][strtolower($this->player->getName())])){
            foreach($this->owner->db['player'][strtolower($this->player->getName())]['particles'] as $data){
                $buttons[] = array('text' => $this->owner->buttonFormat($this->owner->particleQueue[$data]));
            }
        }
        return [
            'type' => 'form',
            'title' => $this->owner->title,
            'content' => $this->owner->contentFormat('적용하실 파티클을 골라주세요.'),
            'buttons' => $buttons
        ];
    }

    public function handleResponse(Player $player, $data): void
    {
        if($data === null) return;
        $name = strtolower($player->getName());
        if($this->owner->db['player'][$name]['multi_particle'] === true)
            $this->owner->db['player'][$name]['multi_particle'] = false;
        if($data === 0){
            $this->owner->db['player'][$name]['current_particle'] = null;
            $this->owner->msg($player, '성공적으로 파티클을 제거했습니다.');
            return;
        }
        $this->owner->db['player'][$name]['current_particle'] = $this->owner->db['player'][$name]['particles'][--$data];
        $this->owner->msg($player, '성공적으로 파티클을 ' . $this->owner->particleQueue[$this->owner->db['player'][$name]['current_particle']] . '(으)로 설정하였습니다.');
    }
}