<?php
/**
 * @filesource modules/ar/views/customer.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Ar\Customer;

use Kotchasan\Currency;
use Kotchasan\DataTable;
use Kotchasan\Date;
use Kotchasan\Http\Request;
use Kotchasan\Language;

/**
 * module=ar-customer
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Gcms\View
{
    /**
     * @var int
     */
    private $time;
    /**
     * @var string
     */
    private $currency_unit;
    /**
     * @var array
     */
    private $actions;

    /**
     * ตารางรายการลูกค้า
     *
     * @param Request $request
     *
     * @return string
     */
    public function render(Request $request)
    {
        $this->actions = Language::get('AR_ACTIONS');
        $this->time = time();
        $this->currency_unit = Language::find('CURRENCY_UNITS', null, self::$cfg->currency_unit);
        // Uri
        $uri = $request->createUriWithGlobals(WEB_URL.'index.php');
        // ตาราง
        $table = new DataTable(array(
            /* Uri */
            'uri' => $uri,
            /* Model */
            'model' => \Ar\Customer\Model::toDataTable(),
            /* รายการต่อหน้า */
            'perPage' => $request->cookie('customer_perPage', 30)->toInt(),
            /* เรียงลำดับ */
            'sort' => $request->cookie('customer_sort', 'last_transaction desc')->toString(),
            /* ฟังก์ชั่นจัดรูปแบบการแสดงผลแถวของตาราง */
            'onRow' => array($this, 'onRow'),
            /* คอลัมน์ที่ไม่ต้องแสดงผล */
            'hideColumns' => array('id', 'status', 'action_date'),
            /* ตั้งค่าการกระทำของของตัวเลือกต่างๆ ด้านล่างตาราง ซึ่งจะใช้ร่วมกับการขีดถูกเลือกแถว */
            'action' => 'index.php/ar/model/customer/action',
            'actionCallback' => 'dataTableActionCallback',
            'actions' => array(
                array(
                    'id' => 'action',
                    'class' => 'ok',
                    'text' => '{LNG_With selected}',
                    'options' => array(
                        'delete' => '{LNG_Delete}',
                    ),
                ),
            ),
            /* คอลัมน์ที่สามารถค้นหาได้ */
            'searchColumns' => array('name', 'detail'),
            /* ส่วนหัวของตาราง และการเรียงลำดับ (thead) */
            'headers' => array(
                'name' => array(
                    'text' => '{LNG_Name}',
                    'sort' => 'name',
                ),
                'phone' => array(
                    'text' => '{LNG_Phone}',
                ),
                'create_date' => array(
                    'text' => '{LNG_Transaction date}',
                    'class' => 'center',
                    'sort' => 'create_date',
                ),
                'last_transaction' => array(
                    'text' => '{LNG_Recent Transactions}',
                    'class' => 'center',
                    'sort' => 'last_transaction',
                ),
                'comment' => array(
                    'text' => '{LNG_Other details}',
                ),
                'total' => array(
                    'text' => '{LNG_Total amount}',
                    'class' => 'center',
                    'sort' => 'total',
                ),
                'detail' => array(
                    'text' => '',
                ),
            ),
            /* รูปแบบการแสดงผลของคอลัมน์ (tbody) */
            'cols' => array(
                'create_date' => array(
                    'class' => 'center date',
                ),
                'last_transaction' => array(
                    'class' => 'center date',
                ),
                'total' => array(
                    'class' => 'right',
                ),
                'detail' => array(
                    'class' => 'right',
                ),
            ),
            /* ปุ่มแสดงในแต่ละแถว */
            'buttons' => array(
                'action' => array(
                    'class' => 'icon-comments button blue notext',
                    'id' => ':id',
                    'title' => '{LNG_Add New} {LNG_Action}',
                ),
                'actions' => array(
                    'class' => 'icon-list button pink notext',
                    'href' => $uri->createBackUri(array('module' => 'ar-actions', 'id' => ':id')),
                    'title' => '{LNG_List of} {LNG_Action}',
                ),
                'transaction' => array(
                    'class' => 'icon-money button orange notext',
                    'href' => $uri->createBackUri(array('module' => 'ar-transaction', 'id' => ':id')),
                    'title' => '{LNG_Transaction details}',
                ),
                'detail' => array(
                    'class' => 'icon-edit button green notext',
                    'href' => $uri->createBackUri(array('module' => 'ar-detail', 'id' => ':id')),
                    'title' => '{LNG_Account details}',
                ),
            ),
            /* ปุ่มเพิ่ม */
            'addNew' => array(
                'class' => 'float_button icon-new',
                'href' => $uri->createBackUri(array('module' => 'ar-detail')),
                'title' => '{LNG_Add New} {LNG_Customer}',
            ),
        ));
        // save cookie
        setcookie('customer_perPage', $table->perPage, time() + 2592000, '/', HOST, HTTPS, true);
        setcookie('customer_sort', $table->sort, time() + 2592000, '/', HOST, HTTPS, true);
        // คืนค่า HTML
        return $table->render();
    }

    /**
     * จัดรูปแบบการแสดงผลในแต่ละแถว.
     *
     * @param array $item
     *
     * @return array
     */
    public function onRow($item, $o, $prop)
    {
        $item['name'] = '<span class=nowrap>'.$item['name'].'</span>';
        $item['phone'] = self::showPhone($item['phone']);
        if ($item['create_date'] == 0) {
            $item['create_date'] = '-';
        } else {
            $diff = Date::compare($item['create_date'], $this->time);
            $item['create_date'] = Date::format($item['create_date'], 'd M Y').' ('.(($diff['year'] * 12) + $diff['month']).'&nbsp;{LNG_month}&nbsp;'.$diff['day'].'&nbsp{LNG_days})';
        }
        if ($item['last_transaction'] == 0) {
            $item['last_transaction'] = '-';
        } else {
            $diff = Date::compare($item['last_transaction'], $this->time);
            $item['last_transaction'] = Date::format($item['last_transaction'], 'd M Y').' ('.(($diff['year'] * 12) + $diff['month']).'&nbsp;{LNG_month}&nbsp;'.$diff['day'].'&nbsp{LNG_days})';
        }
        $item['total'] = empty($item['total']) ? '' : '<span class=nowrap>'.Currency::format($item['total']).' '.$this->currency_unit.'</span>';
        $item['detail'] = '<span id=detail_'.$item['id'].'>'.(isset($this->actions[$item['status']]) ? $this->actions[$item['status']].Date::format($item['action_date'], ' d M Y H:i ').$item['detail'] : '').'</span>';
        return $item;
    }
}
