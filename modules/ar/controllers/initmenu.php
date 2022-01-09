<?php
/**
 * @filesource modules/ar/controllers/initmenu.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Ar\Initmenu;

use Kotchasan\Http\Request;

/**
 * Init Menu
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Kotchasan\KBase
{
    /**
     * ฟังก์ชั่นเริ่มต้นการทำงานของโมดูลที่ติดตั้ง
     * และจัดการเมนูของโมดูล
     *
     * @param Request                $request
     * @param \Index\Menu\Controller $menu
     */
    public static function execute(Request $request, $menu, $login)
    {
        // repair module
        $menu->addTopLvlMenu('ar', '{LNG_Account Receivable}', 'index.php?module=ar-customer', null, 'member');
        $menu->add('report', '{LNG_Monthly report}', 'index.php?module=ar-monthlyreport', null, 'monthlyreport');
        $menu->add('report', '{LNG_Creditors report}', 'index.php?module=ar-creditorsreport', null, 'creditorsreport');
        $menu->add('report', '{LNG_Action}', 'index.php?module=ar-actions', null, 'actions');
        // ตั้งค่าโมดูล
        $menu->add('settings', '{LNG_Creditor}', 'index.php?module=ar-creditor', null, 'creditor');
        $menu->add('settings', '{LNG_Settings} {LNG_Account Receivable}', 'index.php?module=ar-settings', null, 'ar');
    }
}
