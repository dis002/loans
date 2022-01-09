<?php
/**
 * @filesource modules/ar/models/action.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Ar\Action;

use Gcms\Login;
use Kotchasan\Date;
use Kotchasan\Http\Request;
use Kotchasan\Language;

/**
 * module=ar-action
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{
    /**
     * อ่านข้อมูลบัญชี
     *
     * @param int $id
     *
     * @return object|null คืนค่าผลลัพท์ที่พบเพียงรายการเดียว ไม่พบข้อมูลคืนค่า null
     */
    public static function get($id)
    {
        return static::createQuery()
            ->from('ar')
            ->where(array('id', $id))
            ->first();
    }

    /**
     * บันทึกข้อมูล (action.php)
     *
     * @param Request $request
     */
    public function submit(Request $request)
    {
        $ret = array();
        // referer, session, accountant
        if ($request->initSession() && $request->isReferer() && $login = Login::isMember()) {
            if (Login::notDemoMode($login) && Login::checkPermission($login, 'accountant')) {
                try {
                    // POST
                    $save = array(
                        'detail' => $request->post('detail')->topic(),
                        'status' => $request->post('status')->toInt(),
                        'create_date' => $request->post('create_date')->date(),
                        'ar_id' => $request->post('ar_id')->toInt(),
                    );
                    $index = self::get($save['ar_id']);
                    if ($index) {
                        if (empty($save['create_date'])) {
                            $ret['ret_create_date'] = 'Please select';
                        } else {
                            $save['member_id'] = $login['id'];
                            $save['create_date'] .= date(' H:i:s');
                            // บันทึก
                            $this->db()->insert($this->getTableName('actions'), $save);
                            // ส่งค่ากลับ
                            $ret['alert'] = Language::get('Saved successfully');
                            $ret['detail_'.$index->id] = Language::find('AR_ACTIONS', '', $save['status']).Date::format($save['create_date'], ' d M Y H:i ').$save['detail'];
                            $ret['modal'] = 'close';
                        }
                    }
                } catch (\Kotchasan\InputItemException $e) {
                    $ret['alert'] = $e->getMessage();
                }
            }
        }
        if (empty($ret)) {
            $ret['alert'] = Language::get('Unable to complete the transaction');
        }
        // คืนค่าเป็น JSON
        echo json_encode($ret);
    }
}
