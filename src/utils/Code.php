<?php
namespace Utils;
/**
 * Class Code
 * @author: peng <peng_du2007@qq.com>
 * @date  : 2021/1/22 15:34
 */

class Code {
	/**
	 * 生成订单号(24位)
	 * @return Code
	 */
	public static function orderCode () {
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
	/**
	 * 生成随机字符串
	 *
	 * @param int $length 字符串长度
	 *
	 * @return Code
	 */
	public static function random($length = 6) {
		// 字符集
		$chars = "abcdefghjkmnpqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456789";

		$randStr = "";
		for ( $i = 0; $i < $length; $i++ ) {
			$randStr .= $chars [ mt_rand ( 0, strlen ( $chars ) - 1 ) ];
		}

		return $randStr;
	}

	/**
	 * 生成UUID
	 * @return Code
	 */
	public function uuid() {
		mt_srand ( (double) microtime () * 10000 );
		$charid = strtoupper ( md5 ( uniqid ( rand (), true ) ) );
		$hyphen = chr ( 45 );// "-"
		$uuid   = substr ( $charid, 0, 8 ) . $hyphen .
			substr ( $charid, 8, 4 ) . $hyphen .
			substr ( $charid, 12, 4 ) . $hyphen .
			substr ( $charid, 16, 4 ) . $hyphen .
			substr ( $charid, 20, 12 );

		return $uuid;
	}
}