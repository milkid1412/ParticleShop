<?php
declare(strict_types=1);

namespace Kkevin14\ParticleShop\form;

use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use Kkevin14\ParticleShop\ParticleShop;
use pocketmine\form\Form;
use pocketmine\player\Player;

class MainForm implements Form
{
    public ParticleShop $owner;

    public function __construct(ParticleShop $owner)
    {
        $this->owner = $owner;
    }

    /**
     * @return array
     */
    #[Pure] #[ArrayShape(['type' => "string", 'title' => "string", 'content' => "string", 'buttons' => "array[]"])] public function jsonSerialize(): array
    {
        return [
            'type' => 'form',
            'title' => $this->owner->title,
            'content' => $this->owner->contentFormat('원하시는 기능을 선택해주세요.'),
            'buttons' => [
                [
                    'text' => $this->owner->buttonFormat('파티클 구매')
                ],
                [
                    'text' => $this->owner->buttonFormat('파티클 선택')
                ],
                [
                    'text' => $this->owner->buttonFormat('점프파티클 구매')
                ],
                [
                    'text' => $this->owner->buttonFormat('점프파티클 선택')
                ],
                [
                    'text' => $this->owner->buttonFormat('멀티파티클')
                ],
                [
                    'text' => $this->owner->buttonFormat('창 닫기')
                ]
            ]
        ];
    }

    public function handleResponse(Player $player, $data): void
    {
        if($data === null || $data === 5) return;
        $name = strtolower($player->getName());
        if($data === 0){
            $player->sendForm(new ParticleBuyListForm($this->owner));
        }elseif($data === 1){
            $player->sendForm(new SelectParticleForm($this->owner, $player));
        }elseif($data === 2){
            $player->sendForm(new JumpParticleBuyListForm($this->owner));
        }elseif($data === 3){
            $player->sendForm(new SelectJumpParticleForm($this->owner, $player));
        }elseif($data === 4){
            $multi = $this->owner->db['player'][$name]['multi_particle'] ?? null;
            if($multi === null){
                $player->sendForm(new MultiParticlePermissionBuyForm($this->owner));
            }elseif(!$multi){
                $player->sendForm(new SetMultiParticleForm($this->owner, $player));
            }else{
                $this->owner->db['player'][$name]['multi_particle'] = false;
                $this->owner->db['player'][$name]['current_particle'] = null;
                $this->owner->msg($player, '멀티파티클을 비활성화했습니다.');
            }
        }
    }
}