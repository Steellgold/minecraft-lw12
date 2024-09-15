<?php

namespace hackaton\resource;

use GdImage;
use pocketmine\utils\BinaryStream;

class Resource {

    /**
     * @param $bytes
     * @return int[]|null
     */
    public static function getSize($bytes): ?array {
        switch (strlen($bytes)) {
            case 64 * 64 * 4:
                $l = 64;
                $L = 64;
                return [$l, $L];
            case 64 * 32 * 4:
                $l = 64;
                $L = 32;
                return [$l, $L];
            case 128 * 128 * 4:
                $l = 128;
                $L = 128;
                return [$l, $L];
            default :
                return null;

        }
    }

    /**
     * @param $bytes
     * @return string
     */
    public static function getHeadBYTEStoEncodedIMG($bytes): string {
        $size = self::getSize($bytes);
        $l = $size[0] / 8;
        $L = $size[1] / 8;
        $img = self::BYTEStoIMG($bytes);
        $crop = @imagecrop($img, ['x' => $L, 'y' => $l, 'width' => $L, 'height' => $l]);
        @imagedestroy($img);

        ob_start();
        imagepng($crop);
        $imageData = ob_get_contents();
        ob_end_clean();
        return base64_encode($imageData);
    }

    /**
     * @param string $image
     * @return string
     */
    public static function PNGtoBYTES(string $image): string {
        $path = self::getResourcesPath() . "images/" . $image . ".png";
        $img = @imagecreatefrompng($path);
        $bytes = "";
        $L = (int)@getimagesize($path)[0];
        $l = (int)@getimagesize($path)[1];
        for ($y = 0; $y < $l; $y++) {
            for ($x = 0; $x < $L; $x++) {
                $rgba = @imagecolorat($img, $x, $y);
                $a = ((~((int)($rgba >> 24))) << 1) & 0xff;
                $r = ($rgba >> 16) & 0xff;
                $g = ($rgba >> 8) & 0xff;
                $b = $rgba & 0xff;
                $bytes .= chr($r) . chr($g) . chr($b) . chr($a);
            }
        }
        @imagedestroy($img);
        return $bytes;
    }

    /**
     * @param string $bytes
     * @return false|GdImage
     */
    public static function BYTEStoIMG(string $bytes): bool|GdImage {
        $size = self::getSize($bytes);
        $l = $size[0];
        $L = $size[1];

        $img = @imagecreatetruecolor($l, $L);
        @imagealphablending($img, false);
        @imagesavealpha($img, true);

        $stream = new BinaryStream($bytes);

        for ($y = 0; $y < $L; ++$y) {

            for ($x = 0; $x < $l; ++$x) {

                $r = $stream->getByte();
                $g = $stream->getByte();
                $b = $stream->getByte();
                $a = 127 - (int)floor($stream->getByte() / 2);

                $colour = @imagecolorallocatealpha($img, $r, $g, $b, $a);
                @imagesetpixel($img, $x, $y, $colour);
            }
        }

        return $img;
    }

    /**
     * @return string
     */
    public static function getResourcesPath(): string {
        return "plugins/Hackaton/src/resource/";
    }
}