<?php
/**
 * @filesource modules/ar/views/actions.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Ar\Actions;

use Kotchasan\DataTable;
use Kotchasan\Date;
use Kotchasan\Http\Request;
use Kotchasan\Language;

/**
 * module=ar-actions
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Gcms\View
{
    /**
     * @var array
     */
    private $actions;

    /**
     * รายการ action ทั้งหมด
     *
     * @param Request $request
     *
     * @return string
     */
    public function render(Request $request)
    {
        $params = array(
            'id' => $request->request('id')->toInt(),
        );
        $this->actions = Language::get('AR_ACTIONS');
        // Uri
        $uri = $request->createUriWithGlobals(WEB_URL.'index.php');
        // ตาราง
        $table = new DataTable(array(
            /* Uri */
            'uri' => $uri,
            /* Model */
            'model' => \Ar\Actions\Model::toDataTable($params),
            /* รายการต่อหน้า */
            'perPage' => $request->cookie('actions_perPage', 30)->toInt(),
            /* เรียงลำดับ */
            'sort' => $request->cookie('actions_sort', 'create_date desc')->toString(),
            /* ฟังก์ชั่นจัดรูปแบบการแสดงผลแถวของตาราง */
            'onRow' => array($this, 'onRow'),
            /* คอลัมน์ที่ไม่ต้องแสดงผล */
            'hideColumns' => array('id', 'status'),
            /* ตั้งค่าการกระทำของของตัวเลือกต่างๆ ด้านล่างตาราง ซึ่งจะใช้ร่วมกับการขีดถูกเลือกแถว */
            'action' => 'index.php/ar/model/actions/action',
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
            'searchColumns' => array('name', 'ar_id', 'detail'),
            /* ส่วนหัวของตาราง และการเรียงลำดับ (thead) */
            'headers' => array(
                'name' => array(
                    'text' => '{LNG_Name}',
                    'sort' => 'name',
                ),
                'ar_id' => array(
                    'text' => 'ID',
                    'class' => 'center',
                    'sort' => 'ar_id',
                ),
                'create_date' => array(
                    'text' => '{LNG_Action}',
                    'sort' => 'create_date',
                ),
                'detail' => array(
                    'text' => '{LNG_Detail}',
                ),
            ),
            /* รูปแบบการแสดงผลของคอลัมน์ (tbody) */
            'cols' => array(
                'ar_id' => array(
                    'class' => 'center',
                ),
            ),
        ));
        // save cookie
        setcookie('actions_perPage', $table->perPage, time() + 2592000, '/', HOST, HTTPS, true);
        setcookie('actions_sort', $table->sort, time() + 2592000, '/', HOST, HTTPS, true);
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
        $item['name'] = '<a href="index.php?module=ar-detail&amp;id='.$item['ar_id'].'">'.$item['name'].'</a>';
        $item['ar_id'] = '<a href="index.php?module=ar-transaction&amp;id='.$item['ar_id'].'">'.$item['ar_id'].'</a>';
        $item['create_date'] = isset($this->actions[$item['status']]) ? $this->actions[$item['status']].Date::format($item['create_date'], ' d M Y H:i ') : '';
        return $item;
    }
}
