<?php
/**
 * @filesource modules/ar/models/creditorsreport.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Ar\Creditorsreport;

use Kotchasan\Database\Sql;

/**
 * โมเดลสำหรับแสดงรายงานของเจ้าหนี้ (creditorsreport.php).
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{
    /**
     * query เจ้าหนี้ทั้งหมด.
     *
     * @param int $id
     *
     * @return array
     */
    public static function get($id)
    {
        $model = new static();
        $q1 = $model->db()->createQuery()
            ->select(Sql::MIN('create_date', 'create_date'), Sql::SUM('amount', 'amount'), 'office_id id')
            ->from('ar_details')
            ->where(array(
                array('type', 'out'),
                array('member_id', $id),
            ))
            ->groupBy('office_id');

        return $model->db()->createQuery()
            ->select('D.id', 'D.create_date', 'O.name', 'D.amount')
            ->from('ar O')
            ->join(array($q1, 'D'), 'INNER', array('D.id', 'O.id'))
            ->order('create_date DESC')
            ->toArray()
            ->execute();
    }
}
