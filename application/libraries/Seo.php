<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Seo {

    public function __construct() {
        // url get method macro.
        define('G_PR_GET_TYPE_FILE', 1); // use fopen() function
        define('G_PR_GET_TYPE_SOCKET', 2); // use standard fsocketopen function
    }

    public function get_pr($_url, $gettype=G_PR_GET_TYPE_SOCKET) {
        $url = 'info:' . $_url;
        $ch = $this->GCH($this->strord($url));
        $ch = $this->NewGCH($ch);
        $url = str_replace("_", "%5F", 'info:' . urlencode($_url));
        $googlePRUrl =
                "http://toolbarqueries.google.com/search?client=navclient-auto&ch=6"
                . $ch . "&ie=UTF-8&oe=UTF-8&features=Rank&q=" . $url;
        $pr_str = $this->retrieveURLContent($googlePRUrl, $gettype);
        return substr($pr_str, strrpos($pr_str, ":") + 1);
    }

    //unsigned shift right
    private function zeroFill($a, $b) {
        $z = hexdec('8' . implode('', array_fill(0, PHP_INT_SIZE * 2 - 1, '0')));
        if ($z & $a) {
            $a = ($a >> 1);
            $a &= ( ~$z);
            $a |= hexdec('4' . implode('', array_fill(0, PHP_INT_SIZE * 2 - 1, '0')));
            $a = ($a >> ($b - 1));
        } else {
            $a = ($a >> $b);
        }
        return $a;
    }

    // discard bits beyonds 32 bit.
    private function trunkbitForce32bit($n) {
        if (PHP_INT_SIZE <= 4) {
            settype($n, 'float');
            if ($n < 0)
                $n += 4294967296;
            return $n;
        }
        else {
            $clearbit = '';
            for ($i = 0; $i < PHP_INT_SIZE - 4; $i++) {
                $clearbit .= '00';
            }
            for ($i = 0; $i < 4; $i++) {
                $clearbit .= 'ff';
            }
            return ($n & hexdec($clearbit));
        }
    }

    private function bigxor($m, $n) {
        //if(function_exists('gmp_init')){
        // return floatval(gmp_strval(gmp_xor($m,$n)));
        //}
        //else{
        return $m ^ $n;
        //}
    }

    private function mix($a, $b, $c) {
        $a = $this->trunkbitForce32bit($a);
        $b = $this->trunkbitForce32bit($b);
        $c = $this->trunkbitForce32bit($c);
        $a -= $b;
        $a = $this->trunkbitForce32bit($a);
        $a -= $c;
        $a = $this->trunkbitForce32bit($a);
        $a = $this->bigxor($a, ($this->zeroFill($c, 13)));
        $a = $this->trunkbitForce32bit($a);
        $b -= $c;
        $b = $this->trunkbitForce32bit($b);
        $b -= $a;
        $b = $this->trunkbitForce32bit($b);
        $b = $this->bigxor($b, $this->trunkbitForce32bit($a << 8));
        $b = $this->trunkbitForce32bit($b);
        $c -= $a;
        $c = $this->trunkbitForce32bit($c);
        $c -= $b;
        $c = $this->trunkbitForce32bit($c);
        $c = $this->bigxor($c, ($this->zeroFill($b, 13)));
        $c = $this->trunkbitForce32bit($c);
        $a -= $b;
        $a = $this->trunkbitForce32bit($a);
        $a -= $c;
        $a = $this->trunkbitForce32bit($a);
        $a = $this->bigxor($a, ($this->zeroFill($c, 12)));
        $a = $this->trunkbitForce32bit($a);
        $b -= $c;
        $b = $this->trunkbitForce32bit($b);
        $b -= $a;
        $b = $this->trunkbitForce32bit($b);
        $b = $this->bigxor($b, $this->trunkbitForce32bit($a << 16));
        $c -= $a;
        $c = $this->trunkbitForce32bit($c);
        $c -= $b;
        $c = $this->trunkbitForce32bit($c);
        $c = $this->bigxor($c, ($this->zeroFill($b, 5)));
        $c = $this->trunkbitForce32bit($c);
        $a -= $b;
        $a = $this->trunkbitForce32bit($a);
        $a -= $c;
        $a = $this->trunkbitForce32bit($a);
        $a = $this->bigxor($a, ($this->zeroFill($c, 3)));
        $a = $this->trunkbitForce32bit($a);
        $b -= $c;
        $b = $this->trunkbitForce32bit($b);
        $b -= $a;
        $b = $this->trunkbitForce32bit($b);
        $b = $this->bigxor($b, $this->trunkbitForce32bit($a << 10));
        $c -= $a;
        $c = $this->trunkbitForce32bit($c);
        $c -= $b;
        $c = $this->trunkbitForce32bit($c);
        $c = $this->bigxor($c, ($this->zeroFill($b, 15)));
        $c = $this->trunkbitForce32bit($c);
        return array($a, $b, $c);
    }

    private function NewGCH($ch) {
        $ch = ( $this->trunkbitForce32bit(( $ch / 7 ) << 2) |
                ( ( $this->myfmod($ch, 13) ) & 7 ) );
        $prbuf = array();
        $prbuf[0] = $ch;
        for ($i = 1; $i < 20; $i++) {
            $prbuf[$i] = $prbuf[$i - 1] - 9;
        }
        $ch = $this->GCH($this->c32to8bit($prbuf));
        return $ch;
    }

    private function myfmod($x, $y) {
        $i = floor($x / $y);
        return ( $x - $i * $y );
    }

    private function c32to8bit($arr32) {
        $arr8 = array();
        for ($i = 0; $i < count($arr32); $i++) {
            for ($bitOrder = $i * 4; $bitOrder <= $i * 4 + 3; $bitOrder++) {
                $arr8[$bitOrder] = $arr32[$i] & 255;
                $arr32[$i] = $this->zeroFill($arr32[$i], 8);
            }
        }
        return $arr8;
    }

    private function GCH($url, $length=null) {
        if (is_null($length)) {
            $length = sizeof($url);
        }
        $init = 0xE6359A60;
        $a = 0x9E3779B9;
        $b = 0x9E3779B9;
        $c = 0xE6359A60;
        $k = 0;
        $len = $length;
        $mixo = array();
        while ($len >= 12) {
            $a += ( $url[$k + 0] + $this->trunkbitForce32bit($url[$k + 1] << 8)
                    + $this->trunkbitForce32bit($url[$k + 2] << 16)
                    + $this->trunkbitForce32bit($url[$k + 3] << 24));
            $b += ( $url[$k + 4] + $this->trunkbitForce32bit($url[$k + 5] << 8)
                    + $this->trunkbitForce32bit($url[$k + 6] << 16)
                    + $this->trunkbitForce32bit($url[$k + 7] << 24));
            $c += ( $url[$k + 8] + $this->trunkbitForce32bit($url[$k + 9] << 8)
                    + $this->trunkbitForce32bit($url[$k + 10] << 16)
                    + $this->trunkbitForce32bit($url[$k + 11] << 24));
            $mixo = $this->mix($a, $b, $c);
            $a = $mixo[0];
            $b = $mixo[1];
            $c = $mixo[2];
            $k += 12;
            $len -= 12;
        }
        $c += $length;
        switch ($len) {
            case 11:
                $c += $this->trunkbitForce32bit($url[$k + 10] << 24);
            case 10:
                $c+=$this->trunkbitForce32bit($url[$k + 9] << 16);
            case 9 :
                $c+=$this->trunkbitForce32bit($url[$k + 8] << 8);
            case 8 :
                $b+=$this->trunkbitForce32bit($url[$k + 7] << 24);
            case 7 :
                $b+=$this->trunkbitForce32bit($url[$k + 6] << 16);
            case 6 :
                $b+=$this->trunkbitForce32bit($url[$k + 5] << 8);
            case 5 :
                $b+=$this->trunkbitForce32bit($url[$k + 4]);
            case 4 :
                $a+=$this->trunkbitForce32bit($url[$k + 3] << 24);
            case 3 :
                $a+=$this->trunkbitForce32bit($url[$k + 2] << 16);
            case 2 :
                $a+=$this->trunkbitForce32bit($url[$k + 1] << 8);
            case 1 :
                $a+=$this->trunkbitForce32bit($url[$k + 0]);
        }
        $mixo = $this->mix($a, $b, $c);
        $mixo[2] = $this->trunkbitForce32bit($mixo[2]);
        if ($mixo[2] < 0) {
            return (
            hexdec('1' .
                    implode('', array_fill(0, PHP_INT_SIZE * 2, '0')))
            + $mixo[2] );
        } else {
            return $mixo[2];
        }
    }

    // converts a string into an array of integers  
    // containing the numeric value of the char
    private function strord($string) {
        for ($i = 0; $i < strlen($string); $i++) {
            $result[$i] = ord($string{$i});
        }
        return $result;
    }

    // return url page content or false if failed.
    private function retrieveURLContent($url, $gettype) {
        switch ($gettype) {
            case G_PR_GET_TYPE_FILE:
                return $this->retrieveURLContentByFile($url);
                break;
            default:
                return $this->retrieveURLContentBySocket($url);
                break;
        }
    }

    private function retrieveURLContentByFile($url) {
        $fd = @fopen($url, "r");
        if (!$fd) {
            return false;
        }
        $result = "";
        while ($buffer = fgets($fd, 4096)) {
            $result .= $buffer;
        }
        fclose($fd);
        return $result;
    }

    private function retrieveURLContentBySocket($url, $host="", $port=80, $timeout=30) {
        if ($host == "") {
            if (!($pos = strpos($url, '://'))) {
                return false;
            }
            $host = substr($url, $pos + 3, strpos($url, '/', $pos + 3) - $pos - 3);
            $uri = substr($url, strpos($url, '/', $pos + 3));
        } else {
            $uri = $url;
        }
        $request = "GET " . $uri . " HTTP/1.0\r\n"
                . "Host: " . $host . "\r\n"
                . "Accept: */*\r\n"
                . "User-Agent: ZealGet\r\n"
                . "\r\n";
        $sHnd = @fsockopen($host, $port, $errno, $errstr, $timeout);
        if (!$sHnd) {
            return false;
        }
        @fputs($sHnd, $request);
        // Get source
        $result = "";
        while (!feof($sHnd)) {
            $result .= fgets($sHnd, 4096);
        }
        fclose($sHnd);
        $headerend = strpos($result, "\r\n\r\n");
        if (is_bool($headerend)) {
            return $result;
        } else {
            return substr($result, $headerend + 4);
        }
    }

}