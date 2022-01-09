<?php
/**
 * @filesource modules/ar/models/customer.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Ar\Customer;

use Gcms\Login;
use Kotchasan\Database\Sql;
use Kotchasan\Http\Request;
use Kotchasan\Language;

/**
 * module=ar-customer
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{
    /**
     * อ่านข้อมูลสำหรับใส่ลงในตาราง
     *
     * @return \Kotchasan\Database\QueryBuilder
     */
    public static function toDataTable()
    {
        $q1 = static::createQuery()
            ->select('ar_id', Sql::MAX('create_date', 'create_date'))
            ->from('actions')
            ->groupBy('ar_id');
        $q2 = static::createQuery()
            ->select('S.ar_id', 'S.status', 'S.detail', 'S.create_date')
            ->from('actions S')
            ->join(array($q1, 'A'), 'INNER', array(
                array('A.ar_id', 'S.ar_id'),
                array('A.create_date', 'S.create_date'),
            ));
        $q1 = static::createQuery()
            ->select('office_id', Sql::MIN('create_date', 'create_date'), Sql::SUM('amount', 'total'))
            ->from('ar_details')
            ->where(array('type', 'out'))
            ->groupBy('office_id');

        return static::createQuery()
            ->select(
                'O.id',
                'O.name',
                'O.phone',
                'D.create_date',
                Sql::MAX('D2.create_date', 'last_transaction'),
                'D.total',
                'A.detail',
                'A.status',
                'A.create_date action_date'
            )
            ->from('ar O')
            ->join(array($q1, 'D'), 'LEFT', array('D.office_id', 'O.id'))
            ->join('ar_details D2', 'LEFT', array('D2.office_id', 'O.id'))
            ->join(array($q2, 'A'), 'LEFT', array('A.ar_id', 'O.id'))
            ->groupBy('O.id');
    }

    /**
     * action ของตาราง (customer.php)
     */
    public function action(Request $request)
    {
        $ret = array();
        // session, referer, member
        if ($request->initSession() && $request->isReferer() && $login = Login::isMember()) {
            if (Login::notDemoMode($login) && Login::checkPermission($login, 'accountant')) {
                // รับค่าจากการ POST
                $id = $request->post('id')->toString();
                $action = $request->post('action')->toString();
                // Model
                $model = new \Kotchasan\Model();
                if ($action === 'delete' && preg_match('/^[0-9,]+$/', $id)) {
                    // ลบรายการที่เลือก
                    $id = explode(',', $id);
                    $model->db()->createQuery()->delete('ar_details', array('office_id', $id))->execute();
                    $model->db()->createQuery()->delete('ar', array('id', $id))->execute();
                    // คืนค่า
                    $ret['location'] = 'reload';
                } elseif ($action === 'action') {
                    // เพิ่ม Action ตรวจสอบรายการที่เลือก
                    $index = \Ar\Action\Model::get((int) $id);
                    if ($index) {
                        $ret['modal'] = \Ar\Action\View::create()->render($request, $index);
                    }
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
