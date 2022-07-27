<?php
declare(strict_types=1);

namespace Kkevin14\ParticleShop\form;

use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use Kkevin14\ParticleShop\ParticleShop;
use pocketmine\form\Form;
use pocketmine\player\Player;

class SelectJumpParticleForm implements Form
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
        $buttons[] = array('text' => $this->owner->buttonFormat('점프 파티클 제거'));
        if(!empty($this->owner->db['player'][strtolower($this->player->getName())])){
            foreach($this->owner->db['player'][strtolower($this->player->getName())]['jump_particles'] as $data){
                $buttons[] = array('text' => $this->owner->buttonFormat($this->owner->jump_particles[$data]['name']));
            }
        }
        return [
            'type' => 'form',
            'title' => $this->owner->title,
            'content' => $this->owner->contentFormat('적용하실 점프 파티클을 골라주세요.'),
            'buttons' => $buttons
        ];
    }

    public function handleResponse(Player $player, $data): void
    {
        if($data === null) return;
        $name = strtolower($player->getName());
        if($data === 0){
            $this->owner->db['player'][$name]['current_jump_particle'] = null;
            $this->owner->msg($player, '성공적으로 점프 파티클을 제거했습니다.');
            return;
        }
        $this->owner->db['player'][$name]['current_jump_particle'] = $this->owner->db['player'][$name]['jump_particles'][--$data];
        $this->owner->msg($player, '성공적으로 파티클을 ' . $this->owner->jump_particles[$this->owner->db['player'][$name]['current_jump_particle']]['name'] . '(으)로 설정하였습니다.');
    }
}