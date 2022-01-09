<?php
/**
 * @filesource modules/ar/models/actions.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Ar\Actions;

use Gcms\Login;
use Kotchasan\Http\Request;
use Kotchasan\Language;

/**
 * module=ar-actions
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
     * @param array $params
     *
     * @return \Kotchasan\Database\QueryBuilder
     */
    public static function toDataTable($params)
    {
        $where = array();
        if ($params['id'] > 0) {
            $where[] = array('S.ar_id', $params['id']);
        }

        return static::createQuery()
            ->select('S.id', 'A.name', 'S.ar_id', 'S.create_date', 'S.status', 'S.detail')
            ->from('actions S')
            ->join('ar A', 'LEFT', array('A.id', 'S.ar_id'))
            ->where($where);
    }

    /**
     * action ของตาราง (actions.php)
     */
    public function action(Request $request)
    {
        $ret = array();
        // session, referer, member
        if ($request->initSession() && $request->isReferer() && $login = Login::isMember()) {
            if (Login::notDemoMode($login) && Login::checkPermission($login, 'accountant')) {
                // รับค่าจากการ POST
                $action = $request->post('action')->toString();
                if (preg_match_all('/,?([0-9]+),?/', $request->post('id')->toString(), $match)) {
                    if ($action === 'delete') {
                        // ลบ
                        $this->db()->delete($this->getTableName('actions'), array('id', $match[1]), 0);
                        // reload
                        $ret['location'] = 'reload';
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
