<?php

namespace xenialdan\MapAPI;

class Color extends \pocketmine\utils\Color{

	const COLOR_DYE_BLACK = 0;
	const COLOR_DYE_RED = 1;
	const COLOR_DYE_GREEN = 2;
	const COLOR_DYE_BROWN = 3;
	const COLOR_DYE_BLUE = 4;
	const COLOR_DYE_PURPLE = 5;
	const COLOR_DYE_CYAN = 6;
	const COLOR_DYE_LIGHT_GRAY = 7;
	const COLOR_DYE_GRAY = 8;
	const COLOR_DYE_PINK = 9;
	const COLOR_DYE_LIME = 10;
	const COLOR_DYE_YELLOW = 11;
	const COLOR_DYE_LIGHT_BLUE = 12;
	const COLOR_DYE_MAGENTA = 13;
	const COLOR_DYE_ORANGE = 14;
	const COLOR_DYE_WHITE = 15;
	/** @var \SplFixedArray */
	public static $dyeColors = null;

	public static function init(){
		if (self::$dyeColors === null){
			self::$dyeColors = new \SplFixedArray(16);
			self::$dyeColors[self::COLOR_DYE_BLACK] = new Color(30, 27, 27);
			self::$dyeColors[self::COLOR_DYE_RED] = new Color(179, 49, 44);
			self::$dyeColors[self::COLOR_DYE_GREEN] = new Color(61, 81, 26);
			self::$dyeColors[self::COLOR_DYE_BROWN] = new Color(81, 48, 26);
			self::$dyeColors[self::COLOR_DYE_BLUE] = new Color(37, 49, 146);
			self::$dyeColors[self::COLOR_DYE_PURPLE] = new Color(123, 47, 190);
			self::$dyeColors[self::COLOR_DYE_CYAN] = new Color(40, 118, 151);
			self::$dyeColors[self::COLOR_DYE_LIGHT_GRAY] = new Color(153, 153, 153);
			self::$dyeColors[self::COLOR_DYE_GRAY] = new Color(67, 67, 67);
			self::$dyeColors[self::COLOR_DYE_PINK] = new Color(216, 129, 152);
			self::$dyeColors[self::COLOR_DYE_LIME] = new Color(65, 205, 52);
			self::$dyeColors[self::COLOR_DYE_YELLOW] = new Color(222, 207, 42);
			self::$dyeColors[self::COLOR_DYE_LIGHT_BLUE] = new Color(102, 137, 211);
			self::$dyeColors[self::COLOR_DYE_MAGENTA] = new Color(195, 84, 205);
			self::$dyeColors[self::COLOR_DYE_ORANGE] = new Color(235, 136, 68);
			self::$dyeColors[self::COLOR_DYE_WHITE] = new Color(240, 240, 240);
		}
	}

	/**
	 * Returns the logical distance between 2 colors, respecting weight
	 * @param Color $c1
	 * @param Color $c2
	 * @return int
	 */
	public static function getDistance(Color $c1, Color $c2){
		$rmean = ($c1->getR() + $c2->getR()) / 2.0;
		$r = $c1->getR() - $c2->getR();
		$g = $c1->getG() - $c2->getG();
		$b = $c1->getB() - $c2->getB();
		$weightR = 2 + $rmean / 256;
		$weightG = 4;
		$weightB = 2 + (255 - $rmean) / 256;
		return $weightR * $r * $r + $weightG * $g * $g + $weightB * $b * $b;
	}

	/**
	 * Returns a HSV color array
	 * @return array['h','s','v']
	 */
	public function toHSV(){
		$r = $this->getR();
		$g = $this->getG();
		$b = $this->getB();
		$max = max($r, $g, $b);
		$min = min($r, $g, $b);

		$hsv = array('v' => $max / 2.55, 's' => (!$max) ? 0 : (1 - ($min / $max)) * 100, 'h' => 0);
		$dmax = $max - $min;

		if (!$dmax) return $hsv;

		if ($max == $r){
			if ($g < $b){
				$hsv['h'] = ($g - $b) * 60;

			} elseif ($g == $b){
				$hsv['h'] = 360;
			} else{
				$hsv['h'] = ((($g - $b) / $dmax) * 60) + 360;
			}

		} elseif ($max == $g){
			$hsv['h'] = ((($b - $r) / $dmax) * 60) + 120;
		} else{
			$hsv['h'] = ((($r - $g) / $dmax) * 60) + 240;
		}

		return $hsv;
	}

	public function toArray(){
		return [$this->getR(), $this->getG(), $this->getB(), $this->getA()];
	}
}