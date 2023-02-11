<?php

declare(strict_types = 1);

namespace alvin0319\BixbyMarket\entity;

use pocketmine\player\Player;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\{StringTag, CompoundTag};
use pocketmine\entity\{Skin, Entity, Location, EntitySizeInfo};
use pocketmine\event\entity\EntityDamageEvent;

use pocketmine\data\SavedDataLoadingException;
use Ramsey\Uuid\{Uuid, UuidInterface};

use pocketmine\network\mcpe\protocol\{AddPlayerPacket,
    MovePlayerPacket,
    PlayerListPacket,
    PlayerSkinPacket,
    AdventureSettingsPacket,
    types\command\CommandPermissions,
    types\PlayerPermissions,
    UpdateAbilitiesPacket};
use pocketmine\network\mcpe\protocol\types\skin\SkinData;
use pocketmine\network\mcpe\protocol\types\PlayerListEntry;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStackWrapper;
use pocketmine\network\mcpe\protocol\types\entity\{EntityIds, EntityMetadataProperties};
use pocketmine\network\mcpe\convert\{TypeConverter, SkinAdapterSingleton};

use function in_array;

class CustomHuman extends Entity{

    protected ?Player $owner = null;

    protected Skin $skin;

    protected SkinData $skinData;

    protected UuidInterface $uuid;

    protected int $closeTimer = 0;

    public static function getNetworkTypeId() : string{
        return EntityIds::PLAYER;
    }

    public function __construct(Location $pos, Skin $skin, ?CompoundTag $nbt = null){
        $this->skin = $skin;
        $this->skinData = SkinAdapterSingleton::get()->toSkinData($skin);
        parent::__construct($pos, $nbt);
    }

    protected function getInitialSizeInfo() : EntitySizeInfo{ return new EntitySizeInfo(1.8, 0.6, 1.62); }

    protected function initEntity(CompoundTag $nbt) : void{
        parent::initEntity($nbt);
        if(($nameTagTag = $nbt->getTag("NameTag")) instanceof StringTag){
            $this->setNameTag($nameTagTag->getValue());
        }
        $this->uuid = Uuid::uuid3(Uuid::NIL, ((string) $this->getId()) . $this->skin->getSkinData() . $this->getNameTag());
    }

    public function saveNBT() : CompoundTag{
        $skin = $this->skin;
        $nbt = parent::saveNBT();
        if($this->skin !== null){
            $nbt->setTag('Skin', CompoundTag::create()
                ->setString('Name', $skin->getSkinId())
                ->setByteArray('Data', $skin->getSkinData())
                ->setByteArray('CapeData', $skin->getCapeData())
                ->setString('GeometryName', $skin->getGeometryName())
                ->setByteArray('GeometryData', $skin->getGeometryData())
            );
        }
        return $nbt;
    }

    protected final function sendSpawnPacket(Player $player) : void{
        $network = $player->getNetworkSession();

        $playerListAddPacket = new PlayerListPacket();
        $playerListAddPacket->type = PlayerListPacket::TYPE_ADD;
        $playerListAddPacket->entries = [PlayerListEntry::createAdditionEntry(
            $this->uuid,
            $this->id,
            $this->nameTag,
            $this->skinData
        )];
        $network->sendDataPacket($playerListAddPacket);

        $pos = $this->location;
        $addPlayerPacket = new AddPlayerPacket();
        $addPlayerPacket->uuid = $this->uuid;
        $addPlayerPacket->username = '';
        $addPlayerPacket->actorRuntimeId = $this->id;
        $addPlayerPacket->position = $pos->asVector3();
        $addPlayerPacket->pitch = $pos->pitch;
        $addPlayerPacket->yaw = $addPlayerPacket->headYaw = $pos->yaw;
        $addPlayerPacket->item = ItemStackWrapper::legacy(TypeConverter::getInstance()->coreItemStackToNet(VanillaItems::AIR()));
        $this->getNetworkProperties()->setByte(EntityMetadataProperties::COLOR, 0);
        $addPlayerPacket->metadata = $this->getAllNetworkData();
        //$addPlayerPacket->adventureSettingsPacket = AdventureSettingsPacket::create(0, 0, 0, 0, 0, $this->getId());
        $addPlayerPacket->gameMode = 1;
        $addPlayerPacket->abilitiesPacket = UpdateAbilitiesPacket::create(CommandPermissions::NORMAL,PlayerPermissions::MEMBER,$this->getId(),[]);
        $network->sendDataPacket($addPlayerPacket);

        $playerListRemovePacket = new PlayerListPacket();
        $playerListRemovePacket->type = PlayerListPacket::TYPE_REMOVE;
        $playerListRemovePacket->entries = [PlayerListEntry::createRemovalEntry($this->uuid)];
        $network->sendDataPacket($playerListRemovePacket);
    }

}