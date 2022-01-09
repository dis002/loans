<?php
/**
 * @filesource modules/ar/controllers/transaction.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Ar\Transaction;

use Gcms\Login;
use Kotchasan\Date;
use Kotchasan\Html;
use Kotchasan\Http\Request;
use Kotchasan\Language;

/**
 * module=ar-transaction
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Gcms\Controller
{
    /**
     * แสดงรายละเอียดของบัญชี
     *
     * @param Request $request
     *
     * @return string
     */
    public function render(Request $request)
    {
        // อ่านข้อมูลที่เลือก
        $index = \Ar\Transaction\Model::all($request->request('id')->toInt());
        // ข้อความ title bar
        $this->title = Language::trans('{LNG_Account details} '.$index->name.' {LNG_Date} '.Date::format(time(), 'd M Y'));
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
            $ul->appendChild('<li><span class="icon-office">{LNG_Account Receivable}</span></li>');
            $ul->appendChild('<li><a href="{BACKURL?module=ar-customer&id=0}">{LNG_Customer}</a></li>');
            $ul->appendChild('<li><a href="{BACKURL?module=ar-detail&id='.$index->id.'}">'.$index->name.'</a></li>');
            $ul->appendChild('<li><span>{LNG_Transaction details}</span></li>');
            $section->add('header', array(
                'innerHTML' => '<h2 class="icon-report">'.$this->title.'</h2>',
            ));
            // แสดงตาราง
            $section->appendChild(\Ar\Transaction\View::create()->render($index));
            // คืนค่า HTML
            return $section->render();
        }
        // 404
        return \Index\Error\Controller::execute($this, $request->getUri());
    }
}
