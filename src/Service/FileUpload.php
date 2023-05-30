<?php
/**
 * Created by PhpStorm.
 * User: daiandry
 * Date: 05/11/2020
 * Time: 10:26
 */

namespace App\Service;

/**
 * Class fileUpload
 * @package App\Service
 */
class FileUpload
{
    /**
     * @param $base64
     * @param $outputFile
     * @return mixed
     */
    public function base64ToFile($base64, $outputFile) {
        $file = fopen($outputFile, "wb");
        fwrite($file, base64_decode($base64));
        fclose($file);

        return $outputFile;
    }

    /**
     * @param $file
     * @return string
     */
    public function fileToBase64($file)
    {
        $file = @file_get_contents($file);
        $base64 = base64_encode($file);

        return $base64;
    }
}