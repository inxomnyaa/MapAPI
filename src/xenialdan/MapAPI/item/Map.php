<?php

namespace xenialdan\MapAPI\item;

use pocketmine\item\Item;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\mcpe\protocol\ClientboundMapItemDataPacket;
use pocketmine\Server;
use pocketmine\utils\Color;
use xenialdan\MapAPI\Loader;

class Map extends Item
{

    /**
     * @var int $map_id
     * @var Color[][] $colors
     * @var int $scale
     * @var int $width
     * @var int $height
     * @var array $decorations
     * @var int $xOffset
     * @var int $yOffset
     */
    public $map_id, $colors = [], $scale, $width, $height, $decorations = [], $xOffset, $yOffset;

    public function __construct(int $map_id = -1, array $colors = [], int $scale = 0, int $width = 128, int $height = 128, $decorations = [], int $xOffset = 0, int $yOffset = 0)
    {
        try {
            parent::__construct(self::FILLED_MAP, 0, "Filled Map");
        } catch (\InvalidArgumentException $e) {
        }
        $this->setMapId($map_id);
        $this->colors = $colors;
        $this->scale = $scale;
        $this->width = $width;
        $this->height = $height;
        $this->decorations = $decorations;
        $this->xOffset = $xOffset;
        $this->yOffset = $yOffset;
        $this->update(ClientboundMapItemDataPacket::BITFLAG_TEXTURE_UPDATE);
        Loader::getMapUtils()->cacheMap($this);
    }

    /**
     * @return int $id
     */
    public function getMapId()
    {
        return intval($this->map_id === -1 ? $this->getNamedTagEntry('map_uuid')->getValue() : $this->map_id);
    }

    public function setMapId(int $map_id)
    {
        $this->map_id = $map_id;
        try {
            $this->setNamedTagEntry(new StringTag("map_uuid", strval($map_id)));
        } catch (\InvalidArgumentException $e) {
        }
    }

    public function getScale()
    {
        return $this->scale;
    }

    public function setScale(int $scale)
    {
        $this->scale = $scale;
        $this->update(ClientboundMapItemDataPacket::BITFLAG_TEXTURE_UPDATE);
    }

    public function getDecorations()
    {
        return $this->decorations;
    }

    public function setDecorations($decorations)
    {
        $this->decorations = $decorations;
        $this->update(ClientboundMapItemDataPacket::BITFLAG_DECORATION_UPDATE);
    }

    public function addDecoration($decoration)
    {
        $this->decorations[] = $decoration;
        end($this->decorations);
        $this->update(ClientboundMapItemDataPacket::BITFLAG_DECORATION_UPDATE);
        return key($this->decorations);
    }

    public function removeDecoration(int $id)
    {
        unset($this->decorations[$id]);
        $this->update(ClientboundMapItemDataPacket::BITFLAG_DECORATION_UPDATE);
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function setWidth(int $width)
    {
        $this->width = $width;
        $this->update(ClientboundMapItemDataPacket::BITFLAG_TEXTURE_UPDATE);
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function setHeight(int $height)
    {
        $this->height = $height;
        $this->update(ClientboundMapItemDataPacket::BITFLAG_TEXTURE_UPDATE);
    }

    public function getXOffset()
    {
        return $this->xOffset;
    }

    public function setXOffset(int $xOffset)
    {
        $this->xOffset = $xOffset;
        $this->update(ClientboundMapItemDataPacket::BITFLAG_TEXTURE_UPDATE);
    }

    public function getYOffset()
    {
        return $this->yOffset;
    }

    public function setYOffset(int $yOffset)
    {
        $this->yOffset = $yOffset;
        $this->update(ClientboundMapItemDataPacket::BITFLAG_TEXTURE_UPDATE);
    }

    /**
     * @return Color[][]
     */
    public function getColors()
    {
        return $this->colors;
    }

    /**
     * Returns a color at a position, transparent black if not found or "out of map"
     * @param int $x
     * @param int $y
     * @return Color
     */
    public function getColorAt(int $x, int $y)
    {
        if (isset($this->getColors()[$y]) && isset($this->getColors()[$y][$x]))
            return $this->getColors()[$y][$x];
        return Loader::getMapUtils()->getBaseMapColors()[0];
    }

    public function setColors(array $colors)
    {
        $this->colors = $colors;
        $this->update(ClientboundMapItemDataPacket::BITFLAG_TEXTURE_UPDATE);
    }

    public function setColorAt(Color $color, int $x, int $y)
    {
        $this->colors[$y][$x] = $color;
        $this->update(ClientboundMapItemDataPacket::BITFLAG_TEXTURE_UPDATE);
    }

    public function update($type = 0x00)
    {
        $pk = new ClientboundMapItemDataPacket();
        $pk->mapId = $this->getMapId();
        $pk->type = $type;
        $pk->eids = [];
        $pk->scale = $this->getScale();
        $pk->decorations = $this->getDecorations();
        $pk->width = $this->getWidth();
        $pk->height = $this->getHeight();
        $pk->xOffset = $this->getXOffset();
        $pk->yOffset = $this->getYOffset();
        $pk->colors = $this->getColors();
        try {
            Server::getInstance()->broadcastPacket(Server::getInstance()->getOnlinePlayers(), $pk);
        } catch (\RuntimeException $e) {
        }
    }

    public function save()
    {
        return Loader::getMapUtils()->exportToNBT($this, $this->getMapId());
    }
}