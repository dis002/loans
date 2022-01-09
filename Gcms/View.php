<?php
/**
 * @filesource Gcms/View.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Gcms;

use Kotchasan\Collection;
use Kotchasan\Language;

/**
 * View base class สำหรับ GCMS
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Kotchasan\View
{
    /**
     * ฟังก์ชั่น แทนที่ query string ด้วยข้อมูลจาก GET และ POST สำหรับส่งต่อไปยัง URL ถัดไป
     * โดยการรับค่าจาก preg_replace
     * คืนค่า URL
     *
     * @param array $f รับค่าจากตัวแปรที่ส่งมาจาก preg_replace มาสร้าง query string
     *
     * @return string
     */
    public static function back($f)
    {
        $query_url = array();
        foreach (self::$request->getQueryParams() as $key => $value) {
            if ($value != '' && !preg_match('/^(module|.*?username|.*?password)$/', $key) && (is_string($value) || is_int($value))) {
                $key = ltrim($key, '_');
                $query_url[$key] = $value;
            }
        }
        foreach (self::$request->getParsedBody() as $key => $value) {
            if ($value != '' && !preg_match('/^(module|.*?username|.*?password)$/', $key) && (is_string($value) || is_int($value))) {
                $key = ltrim($key, '_');
                $query_url[$key] = $value;
            }
        }
        if (isset($f[2])) {
            foreach (explode('&', $f[2]) as $item) {
                if (preg_match('/^([a-zA-Z0-9_\-]+)=([^$]{1,})$/', $item, $match)) {
                    if ($match[2] === '0') {
                        unset($query_url[$match[1]]);
                    } else {
                        $query_url[$match[1]] = $match[2];
                    }
                }
            }
        }
        return WEB_URL.'index.php?'.http_build_query($query_url, '', '&amp;');
    }

    /**
     * ouput เป็น HTML
     *
     * @param string|null $template HTML Template ถ้าไม่กำหนด (null) จะใช้ index.html
     *
     * @return string
     */
    public function renderHTML($template = null)
    {
        // เนื้อหา
        parent::setContents(array(
            // url สำหรับกลับไปหน้าก่อนหน้า
            '/{BACKURL(\?([a-zA-Z0-9=&\-_@\.]+))?}/e' => '\Gcms\View::back',
            /* ภาษา */
            '/{LNG_([^}]+)}/e' => '\Kotchasan\Language::parse(array(1=>"$1"))',
            /* ภาษา ที่ใช้งานอยู่ */
            '/{LANGUAGE}/' => Language::name(),
        ));
        return parent::renderHTML($template);
    }

    /**
     * คืนค่าลิงค์รูปแบบโทรศัพท์
     *
     * @param string $phone_number
     *
     * @return string
     */
    public static function showPhone($phone_number)
    {
        if ($phone_number === null) {
            return '';
        }
        $result = array();
        foreach (explode(',', $phone_number) as $phone) {
            $result[] = '<a href="tel:'.$phone.'">'.$phone.'</a>';
        }
        return empty($result) ? '' : implode(', ', $result);
    }

    /**
     * คืนค่ารูปดาว 1-5
     *
     * @param int $n
     *
     * @return string
     */
    public static function showStars($n)
    {
        $ret = '<span class="hotel_stars">';
        for ($i = 1; $i < 6; $i++) {
            if ($i <= $n) {
                $ret .= '<i class="icon-star2"></i>';
            } else {
                $ret .= '<i class="icon-star0"></i>';
            }
        }

        return $ret.'</span>';
    }

    /**
     * คืนค่า ROI card
     *
     * @param float $capital
     * @param float $return_interest
     * @param float $commission
     * @param object $index
     *
     * @return string
     */
    public static function showRoi($capital, $return_interest, $commission, $index)
    {
        // roi
        $roi1 = $capital == 0 ? 0 : (($return_interest - $capital) * 100 / $capital);
        $roi2 = $capital == 0 ? 0 : (($return_interest - $capital - $commission) * 100 / $capital);
        $card = new Collection();
        \Index\Home\Controller::renderCard($card, 'icon-money', 'ROI 1', number_format($roi1, 2).'%', '{LNG_Exclude commission}', null);
        \Index\Home\Controller::renderCard($card, 'icon-money', 'ROI 2', number_format($roi2, 2).'%', '{LNG_Include commission}', null);
        if ($index->collateral_status == 4) {
            $roi2 = $capital == 0 ? 0 : (($index->sale_price + $return_interest - $capital - $commission) * 100 / $capital);
            \Index\Home\Controller::renderCard($card, 'icon-money', 'ROI 3', number_format($roi2, 2).'%', 'ROI 2 + {LNG_Collateral}', null);
        }
        $row = '<div class="dashboard ggrid row clear">';
        foreach ($card as $item) {
            $row .= '<div class="card float-left block4">'.$item.'</div>';
        }
        $row .= '</div>';
        return $row;
    }
}
