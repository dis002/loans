<?php
/**
 * @filesource modules/ar/controllers/monthlyreport.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Ar\Monthlyreport;

use Gcms\Login;
use Kotchasan\Html;
use Kotchasan\Http\Request;
use Kotchasan\Language;

/**
 * module=ar-monthlyreport
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Gcms\Controller
{
    /**
     * รายงานประจำดือน
     *
     * @param Request $request
     *
     * @return string
     */
    public function render(Request $request)
    {
        // อ่านภาษาใส่ลงในตัวแปรไว้
        $index = (object) Language::getItems(array(
            'MONTH_LONG',
            'YEAR_OFFSET',
            'CURRENCY_UNITS',
            'AR_TYPIES',
        ));
        // ค่าที่ส่งมา
        $index->month = $request->request('month', date('m'))->toInt();
        $index->year = $request->request('year', date('Y'))->toInt();
        // ข้อความ title bar
        $this->title = Language::trans('{LNG_Monthly report} ').$index->MONTH_LONG[$index->month].' '.($index->year + $index->YEAR_OFFSET);
        // เลือกเมนู
        $this->menu = 'ar';
        // พนักงานบัญชี
        if (Login::checkPermission(Login::isMember(), 'accountant')) {
            // แสดงผล
            $section = Html::create('section', array(
                'class' => 'content_bg',
            ));
            // breadcrumbs
            $breadcrumbs = $section->add('div', array(
                'class' => 'breadcrumbs',
            ));
            $ul = $breadcrumbs->add('ul');
            $ul->appendChild('<li><span class="icon-office">{LNG_Report}</span></li>');
            $ul->appendChild('<li><a href="{BACKURL?module=ar-customer&id=0}">{LNG_Customer}</a></li>');
            $ul->appendChild('<li><span>{LNG_Monthly report}</span></li>');
            $section->add('header', array(
                'innerHTML' => '<h2 class="icon-report">'.$this->title.'</h2>',
            ));
            // menu
            $section->appendChild(\Index\Tabmenus\View::render($request, 'report', 'monthlyreport'));
            // แสดงตาราง
            $section->appendChild(\Ar\Monthlyreport\View::create()->render($index));
            // คืนค่า HTML
            return $section->render();
        }
        // 404
        return \Index\Error\Controller::execute($this, $request->getUri());
    }
}
