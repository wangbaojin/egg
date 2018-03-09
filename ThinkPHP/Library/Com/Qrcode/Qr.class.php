<?php
namespace Com\Qrcode;
require 'phpqrcode.class.php';
/**
 * Created by PhpStorm.
 * User: lkk
 * Date: 18/3/5
 * Time: 下午7:52
 */

class Qr
{
    private $_qr_level   = 'L';
    private $_qr_size    = 6;
    private $_qr_margin  = 2;
    private $_qr_outfile = false;

    public function config($arr) {
        foreach ($arr as $_k => $_v)
            if (isset($this->$_k))
                $this->$_k = $_v;
    }

    public function create($path,$outfile=false)
    {

        $text   = urldecode($path);
        //$outfile = './Public/images/Api/create_qr.jpg';
        //$outfile = false;
        $level  = 'L';
        $size   = 6;
        $margin = 2;
        $p = QRcode::png($text, $this->_qr_outfile, $this->_qr_level, $this->_qr_size, $this->_qr_margin);
        return $p;
    }
}