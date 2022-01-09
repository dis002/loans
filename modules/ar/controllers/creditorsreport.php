<?php
/**
 * @filesource modules/ar/controllers/creditorsreport.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Ar\Creditorsreport;

use Gcms\Login;
use Kotchasan\ArrayTool;
use Kotchasan\Html;
use Kotchasan\Http\Request;
use Kotchasan\Language;

/**
 * module=ar-creditorsreport
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Gcms\Controller
{
    /**
     * รายงานสำหรับเจ้าหนี้
     *
     * @param Request $request
     *
     * @return string
     */
    public function render(Request $request)
    {
        // เจ้าหนี้
        $index = (object) array(
            'creditors' => \Ar\Detail\Model::getCreditors(),
        );
        // ค่าที่ส่งมา
        $index->u = $request->request('u', ArrayTool::getFirstKey($index->creditors))->toInt();
        // ข้อความ title bar
        $this->title = Language::get('Creditors report');
        if (isset($index->creditors[$index->u])) {
            $this->title .= ' '.$index->creditors[$index->u];
        }
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
            $ul->appendChild('<li><span>{LNG_Creditors report}</span></li>');
            $section->add('header', array(
                'innerHTML' => '<h2 class="icon-report">'.$this->title.'</h2>',
            ));
            // menu
            $section->appendChild(\Index\Tabmenus\View::render($request, 'report', 'creditorsreport'));
            // แสดงตาราง
            $section->appendChild(\Ar\Creditorsreport\View::create()->render($index));
            // คืนค่า HTML
            return $section->render();
        }
        // 404
        return \Index\Error\Controller::execute($this, $request->getUri());
    }
}
