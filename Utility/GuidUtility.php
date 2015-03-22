<?php

namespace Manatee\CoreBundle\Utility;

class GuidUtility
{
    /**
     * Generate a random GUID
     *
     * @param integer $length optional, max length 32
     * @return string
     */
    public static function generate($length)
    {
        mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
        //$uuid = strtoupper(md5(uniqid(mt_rand(), true)));
        $uuid = strtolower(md5(uniqid(mt_rand(), true)));
        if($length) $uuid = substr($uuid, 0, $length); // shorten the string
        return $uuid;
    }
}