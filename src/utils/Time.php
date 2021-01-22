<?php
namespace Utils;
/**
 * Class Time
 * @author: peng <peng_du2007@qq.com>
 * @date  : 2021/1/22 15:43
 */
class Time {
	/**
	 * 计算从$time开始到现在为止过去了多长时间
	 *
	 * @param int $time UNIX时间戳
	 * @param bool $isHuman 是否返回人类易读的方式
	 *
	 * @return false|string
	 */
	public static function passedTime ( $time, $isHuman = true ) {
		$t  = time () - $time;
		if ($isHuman) {
			$today = strtotime ( date ( "M-d-y", mktime ( 0, 0, 0, date ( "m" ), date ( "d" ), date ( "Y" ) ) ) );
			if ( $t <= 60 ) {
				return '刚刚';
			} elseif ( $t >= 60 && $t < 3600 ) {
				$return = intval ( $t / 60 ) . " 分钟前";
			} else {
				if ( $time > $today ) {
					$return = "今天 " . date ( "H:i", $time );
				} elseif ( $time < $today && $time > ( $today - 86400 ) ) {
					$return = "昨天 " . date ( "H:i", $time );
				} else {
					$return = date ( "Y-m-d H:i", $time );
				}
			}

			return $return;
		}else {
			return $time;
		}
	}
}