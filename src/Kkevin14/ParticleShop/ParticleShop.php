<?php
declare(strict_types=1);

namespace Kkevin14\ParticleShop;

use JsonException;
use Kkevin14\ParticleShop\item\ParticleCoinItem;
use Kkevin14\ParticleShop\task\AddParticleTask;
use Kkevin14\ParticleShop\task\CircleParticleTask;
use pocketmine\inventory\CreativeInventory;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\types\ParticleIds;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use Kkevin14\ParticleShop\task\RotateTask;
use Kkevin14\ParticleShop\command\MainCommand;
use pocketmine\utils\Config;

class ParticleShop extends PluginBase
{
    public Config $database;

    public array $db, $particleQueue = ['무지개별', '눈꽃', '영혼', '회전하는두개의하트', '회전하는빨파초', '토템', '회전하는눈꽃들', '올라갔다내려오는먼지들', '올라갔다내려오는눈꽃들', '올라갔다내려오는글자들', '올라갔다내려오는영혼들', '올라갔다내려오는별들'], $dataQueue = [
        [
            'type' => 'particle_rainbow_spark',
            'particle' => ParticleIds::SPARKLER,
            'data' => 0,
            'price' => 3
        ],
        [
            'type' => null,
            'particle' => ParticleIds::SHULKER_BULLET,
            'data' => null,
            'price' => 3
        ],
        [
            'type' => null,
            'particle' => ParticleIds::SOUL,
            'data' => null,
            'price' => 5
        ],
        [
            'type' => 'rotate_common',
            'particle' => ParticleIds::HEART,
            'data' => 1,
            'price' => 2
        ],
        [
            'type' => 'rotate_dust',
            'particle' => ParticleIds::DUST,
            'data' => [
                [
                    225, 0, 0
                ],
                [
                    0, 225, 0
                ],
                [
                    0, 0, 225
                ]
            ],
            'price' => 3
        ],
        [
            'type' => null,
            'particle' => ParticleIds::TOTEM,
            'data' => null,
            'price' => 3
        ],
        [
            'type' => 'rotate_common',
            'particle' => ParticleIds::SHULKER_BULLET,
            'data' => 2,
            'price' => 3
        ],
        [
            'type' => 'rotate_x_y_color_particle',
            'particle' => ParticleIds::DUST,
            'data' => [
                [
                    225, 0, 0
                ],
                [
                    0, 225, 0
                ],
                [
                    0, 0, 225
                ]
            ],
            'price' => 3
        ],
        [
            'type' => 'rotate_x_y_common',
            'particle' => ParticleIds::SHULKER_BULLET,
            'data' => 2,
            'price' => 3
        ],
        [
            'type' => 'rotate_x_y_common',
            'particle' => ParticleIds::ENCHANTMENT_TABLE,
            'data' => 2,
            'price' => 3
        ],
        [
            'type' => 'rotate_x_y_common',
            'particle' => ParticleIds::SOUL,
            'data' => 2,
            'price' => 6
        ],
        [
            'type' => 'rotate_x_y_color_particle',
            'particle' => ParticleIds::SPARKLER,
            'data' => [
                [
                    225, 0, 0
                ],
                [
                    0, 225, 0
                ],
                [
                    0, 0, 225
                ]
            ],
            'price' => 6
        ]
    ], $pk = [], $jump_particles = [
        [
            'name' => '뿅',
            'type' => null,
            'particle' => ParticleIds::SPARKLER,
            'data' => [255, 255, 255],
            'price' => 5
        ],
        [
            'name' => '뾰로롱',
            'type' => 'circles_rgb',
            'particle' => ParticleIds::SPARKLER,
            'data' => [255, 255, 255],
            'price' => 5
        ]
    ];
    public string $title = '§7[ §l§f파티클상점 §7]';
    public int|float $num = 0, $y = 0, $multi_particle_cost = 5;

    protected function onEnable(): void
    {
        $this->database = new Config($this->getDataFolder() . 'data.yml', Config::YAML, [
            'player' => []
        ]);
        $this->db = $this->database->getAll();

        $this->getScheduler()->scheduleDelayedRepeatingTask(new RotateTask($this), 4, 4);
        $this->getScheduler()->scheduleDelayedRepeatingTask(new AddParticleTask($this), 2, 4);

        $this->getServer()->getCommandMap()->register('파티클상점', new MainCommand($this));

        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);

        $item = new ParticleCoinItem();
        CreativeInventory::getInstance()->add($item->setCustomName('§r§b§l▶ §f파티클 코인')->setLore(['', '§r§d§l• §r§7/파티클']));
    }

    public function contentFormat(string $str): string
    {
        return "\n" . $str . "\n\n";
    }

    public function buttonFormat(string $str): string
    {
        return '§7[ §f' . $str . ' §7]';
    }

    public function msg(Player|string $player, string $msg)
    {
        if(!$player instanceof Player){
            $this->getServer()->getOfflinePlayer($player);
        }
        if($player->isOnline()){
            $player->sendMessage('§7[ §l§c! §r§7] §f' . $msg);
        }
    }

    public function addParticle(Player $player, int $id, null|string $type = null, null|int|array $rgb = null): void
    {
        if($type === null){
            $pk = LevelEventPacket::standardParticle($id, is_null($rgb) ? 0 : ((255 << 24) | ((int) $rgb[0] << 16) | ((int) $rgb[1] << 8) | (int) $rgb[2]), $player->getPosition()->add(mt_rand(-10, 10) * 0.1, mt_rand(0, 10) * 0.1, mt_rand(-10, 10) * 0.1));
            $this->pk[] = $pk;
        }elseif($type === 'rotate_dust'){
            $times = is_null($rgb) ? 0 : count($rgb) - 1;
            for($i = 0; $i <= $times; $i++){
                $data = is_null($rgb) ? 0 : $rgb[$i];
                $pk = LevelEventPacket::standardParticle($id, is_null($rgb) ? 0 : ((225 & 0xff) << 24) | (($data[0] & 0xff) << 16) | (($data[1] & 0xff) << 8) | ($data[2] & 0xff), $player->getPosition()->add(cos($this->num + $i * (2 * pi() / ($times + 1))), 0.3, sin($this->num + $i * (2 * pi() / ($times + 1)))));
                $this->pk[] = $pk;
                unset($data);
            }
        }elseif($type === 'rotate_common'){
            $data = is_null($rgb) ? 0 : $rgb;
            for($i = 0; $i <= $data; $i++){
                $pk = LevelEventPacket::standardParticle($id, 0, $player->getPosition()->add(cos($this->num + $i * ($data * 2 * pi() / ($data + 1))), 0.3, sin($this->num + $i * ($data * 2 * pi() / ($data + 1)))));
                $this->pk[] = $pk;
            }
        }elseif($type === 'rotate_x_y_color_particle'){
            $times = is_null($rgb) ? 0 : count($rgb) - 1;
            for($i = 0; $i <= $times; $i++){
                $data = is_null($rgb) ? 0 : $rgb[$i];
                $pk = LevelEventPacket::standardParticle($id, is_null($rgb) ? 0 : ((225 & 0xff) << 24) | (($data[0] & 0xff) << 16) | (($data[1] & 0xff) << 8) | ($data[2] & 0xff), $player->getPosition()->add(cos($this->num + $i * (2 * pi() / ($times + 1))), 0.55 + $this->y * 0.05, sin($this->num + $i * (2 * pi() / ($times + 1)))));
                $this->pk[] = $pk;
                unset($data);
            }
        }elseif($type === 'rotate_x_y_common'){
            $data = is_null($rgb) ? 0 : $rgb;
            for($i = 0; $i <= $data; $i++){
                $pk = LevelEventPacket::standardParticle($id, 0, $player->getPosition()->add(cos($this->num + $i * ($data * 2 * pi() / ($data + 1))), 0.55 + $this->y * 0.05, sin($this->num + $i * ($data * 2 * pi() / ($data + 1)))));
                $this->pk[] = $pk;
            }
        }elseif($type === 'particle_rainbow_spark'){
            $pk = LevelEventPacket::standardParticle($id, ((255 << 24) | (mt_rand(150, 255) << 16) | (mt_rand(150, 255) << 8) | mt_rand(150, 255)), $player->getPosition()->add(mt_rand(-10, 10) * 0.1, mt_rand(0, 10) * 0.1, mt_rand(-10, 10) * 0.1));
            $this->pk[] = $pk;
        }
    }

    public function addJumpParticle(Player $player, int $id, ?string $type = null, array|int $rgb = null): void
    {
        $pk = array();
        if($type === null){
            for($x = -1; $x <= 1; $x += 0.1)
                for($z = -1; $z <= 1; $z += 0.1)
                    if($player->getLocation()->distance($player->getLocation()->add($x, 0.3, $z)) <= 1)
                    $pk[] = LevelEventPacket::standardParticle($id, is_array($rgb) ? ((255 << 24) | (mt_rand(150, 255) << 16) | (mt_rand(150, 255) << 8) | mt_rand(150, 255)) : $rgb, $player->getLocation()->add($x, 0.3, $z));
                    //$pk[] = LevelEventPacket::standardParticle($id, is_array($rgb) ? ((255 << 24) | ($rgb[0] << 16) | ($rgb[1] << 8) | $rgb[2]) : $rgb, $player->getLocation()->add($x, 0.3, $z));
        }elseif($type === 'circles_rgb'){
            $this->getScheduler()->scheduleDelayedTask(new CircleParticleTask($this, $player, 0, 3, $player->getLocation()->getY() - 1, [$id, $rgb]), 1);
        }
        if(!empty($pk))
            $this->getServer()->broadcastPackets($player->getWorld()->getPlayers(), $pk);
    }

    /**
     * @throws JsonException
     */
    public function onDisable(): void
    {
        $this->database->setAll($this->db);
        $this->database->save();
    }
}
