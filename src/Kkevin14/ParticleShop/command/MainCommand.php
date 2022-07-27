<?php
declare(strict_types=1);

namespace Kkevin14\ParticleShop\command;

use Kkevin14\ParticleShop\form\MainForm;
use Kkevin14\ParticleShop\ParticleShop;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class MainCommand extends Command
{
    public ParticleShop $owner;

    public function __construct(ParticleShop $owner)
    {
        parent::__construct('파티클', '파티클을 관리합니다.', '/파티클', ['파티클상점']);
        $this->owner = $owner;
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return mixed|void
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if(!$this->testPermission($sender) || !$sender instanceof Player) return;
        $sender->sendForm(new MainForm($this->owner));
    }
}