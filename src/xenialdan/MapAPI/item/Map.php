<?php

namespace xenialdan\MapAPI;

use pocketmine\network\mcpe\protocol\ClientboundMapItemDataPacket;
use pocketmine\Server;
use pocketmine\utils\Color;

class Map {

	/**
	 * @var int $id
	 * @var Color[][] $colors
	 * @var int $scale
	 * @var int $width
	 * @var int $height
	 * @var array $decorations
	 * @var int $xOffset
	 * @var int $yOffset
	 */
	public $id, $colors = [], $scale, $width, $height, $decorations = [], $xOffset, $yOffset;


	public function __construct(int $id = -1, array $colors = [], int $scale = 1, int $width = 128, int $height = 128, $decorations = [], int $xOffset = 0, int $yOffset = 0) {
		$this->id = $id;
		$this->colors = $colors;
		$this->scale = $scale;
		$this->width = $width;
		$this->height = $height;
		$this->decorations = $decorations;
		$this->xOffset = $xOffset;
		$this->yOffset = $yOffset;
	}

	/**
	 * @return int $id
	 */
	public function getMapId() {
		return $this->id;
	}

	public function setMapId(int $id) {
		$this->id = $id;
		//TODO: update?? i guess resend.. client would request?
	}

	public function getScale() {
		return $this->scale;
	}

	public function setScale(int $scale) {
		$this->scale = $scale;
		$this->update(ClientboundMapItemDataPacket::BITFLAG_TEXTURE_UPDATE);
	}

	public function getDecorations() {
		return $this->decorations;
	}

	public function addDecoration($decorations) {
		$this->decorations[] = $decorations;
		end($this->decorations);
		$this->update(ClientboundMapItemDataPacket::BITFLAG_DECORATION_UPDATE);
		return key($this->decorations);
	}

	public function removeDecoration(int $id) {
		unset($this->decorations[$id]);
		$this->update(ClientboundMapItemDataPacket::BITFLAG_DECORATION_UPDATE);
	}

	public function getWidth() {
		return $this->width;
	}

	public function setWidth(int $width) {
		$this->width = $width;
		$this->update(ClientboundMapItemDataPacket::BITFLAG_TEXTURE_UPDATE);
	}

	public function getHeight() {
		return $this->height;
	}

	public function setHeight(int $height) {
		$this->height = $height;
		$this->update(ClientboundMapItemDataPacket::BITFLAG_TEXTURE_UPDATE);
	}

	public function getXOffset() {
		return $this->xOffset;
	}

	public function setXOffset(int $xOffset) {
		$this->xOffset = $xOffset;
		$this->update(ClientboundMapItemDataPacket::BITFLAG_TEXTURE_UPDATE);
	}

	public function getYOffset() {
		return $this->yOffset;
	}

	public function setYOffset(int $yOffset) {
		$this->yOffset = $yOffset;
		$this->update(ClientboundMapItemDataPacket::BITFLAG_TEXTURE_UPDATE);
	}

	/**
	 * @return Color[][]
	 */
	public function getColors() {
		return $this->colors;
	}

	public function getColorAt(int $x, int $y) {
		return $this->colors[$y][$x];
	}

	public function setColors(array $colors) {
		$this->colors = $colors;
		$this->update(ClientboundMapItemDataPacket::BITFLAG_TEXTURE_UPDATE);
	}

	public function setColorAt(Color $color, int $x, int $y) {
		$this->colors[$y][$x] = $color;
		$this->update(ClientboundMapItemDataPacket::BITFLAG_TEXTURE_UPDATE);
	}

	public function update($type = 0x00) {
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
		Server::getInstance()->broadcastPacket(Server::getInstance()->getOnlinePlayers(), $pk);
	}

	public function save() {
		//TODO. -> see maputils export
	}
}