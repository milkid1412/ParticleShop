<?php
declare(strict_types=1);

namespace Kkevin14\ParticleShop\item;

use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;

class ParticleCoinItem extends Item
{
    public function __construct()
    {
        parent::__construct(new ItemIdentifier(ItemIds::NETHER_STAR, 1023), 'ParticleCoin');
    }
}