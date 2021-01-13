<?php

class Utils {
    // 生成订单号
    public static function makeOrderNo () {
        @date_default_timezone_set ( "Asia/Shanghai" );
        $order_id_main = date ( 'YmdHis' ) . rand ( 10000000, 99999999 );
        //订单号码主体长度
        $order_id_len = strlen ( $order_id_main );
        $order_id_sum = 0;
        for ( $i = 0; $i < $order_id_len; $i++ ) {
            $order_id_sum += (int) ( substr ( $order_id_main, $i, 1 ) );
        }
        //唯一订单号码（YYYYMMDDHHIISSNNNNNNNNCC）
        $osn = $order_id_main . str_pad ( ( 100 - $order_id_sum % 100 ) % 100, 2, '0', STR_PAD_LEFT ); //生成唯一订单号

        return $osn;
    }

    // 生成GUID
    public static function makeGuid () {
        mt_srand ( (double) microtime () * 10000 );
        $charid = strtoupper ( md5 ( uniqid ( rand (), true ) ) );
        $hyphen = chr ( 45 );// "-"
        $uuid   = substr ( $charid, 0, 8 ) . $hyphen . substr ( $charid, 8, 4 ) . $hyphen . substr ( $charid, 12, 4 ) . $hyphen . substr ( $charid, 16, 4 ) . $hyphen . substr ( $charid, 20, 12 );

        return $uuid;
    }

    // 格式化时间
    public static function formatDate ( $beforetime ) {
        $time  = time () - $beforetime;
        $today = strtotime ( date ( "M-d-y", mktime ( 0, 0, 0, date ( "m" ), date ( "d" ), date ( "Y" ) ) ) );
        if ( $time <= 60 ) {
            return '刚刚';
        } elseif ( $time >= 60 && $time < 3600 ) {
            $return = intval ( $time / 60 ) . " 分钟前";
        } else {
            if ( $beforetime > $today ) {
                $return = "今天 " . date ( "H:i", $beforetime );
            } elseif ( $beforetime < $today && $beforetime > ( $today - 86400 ) ) {
                $return = "昨天 " . date ( "H:i", $beforetime );
            } else {
                $return = date ( "Y-m-d H:i", $beforetime );
            }
        }

        return $return;
    }

    // 生成随机字符串
    public static function makeRandomString ($length = 6) {
        // 字符集，可任意添加你需要的字符
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789"; // 字母+数字，且字母必须是大写，9位

        $randStr = "";
        for ( $i = 0; $i < $length; $i++ ) {
            $randStr .= $chars [ mt_rand ( 0, strlen ( $chars ) - 1 ) ];
        }

        return $randStr;
    }
    // discuz 加密解密函数
    function authcode($string, $operation = 'DECODE', $key = 'sdfsdf@#(', $expiry = 0) {
        // 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙
        // 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
        // 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
        // 当此值为 0 时，则不产生随机密钥
        $ckey_length = 4;

        // 密匙
        $key = md5($key ? $key : $GLOBALS['discuz_auth_key']);

        // 密匙a会参与加解密
        $keya = md5(substr($key, 0, 16));
        // 密匙b会用来做数据完整性验证
        $keyb = md5(substr($key, 16, 16));
        // 密匙c用于变化生成的密文
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
        // 参与运算的密匙
        $cryptkey = $keya.md5($keya.$keyc);
        $key_length = strlen($cryptkey);
        // 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)，解密时会通过这个密匙验证数据完整性
        // 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确
        $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
        $string_length = strlen($string);
        $result = '';
        $box = range(0, 255);
        $rndkey = array();
        // 产生密匙簿
        for($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }
        // 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上并不会增加密文的强度
        for($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        // 核心加解密部分
        for($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            // 从密匙簿得出密匙进行异或，再转成字符
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        if($operation == 'DECODE') {
            // substr($result, 0, 10) == 0 验证数据有效性
            // substr($result, 0, 10) - time() > 0 验证数据有效性
            // substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16) 验证数据完整性
            // 验证数据有效性，请看未加密明文的格式
            if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            // 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因
            // 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码
            return $keyc.str_replace('=', '', base64_encode($result));
        }
    }
}
