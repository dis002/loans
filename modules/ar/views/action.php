<?php
/**
 * @filesource modules/ar/views/action.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Ar\Action;

use Kotchasan\Html;
use Kotchasan\Http\Request;
use Kotchasan\Language;

/**
 * module=ar-action
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Gcms\View
{
    /**
     * ฟอร์ม เพิ่ม Action
     *
     * @param Request $request
     * @param object  $index
     *
     * @return string
     */
    public function render(Request $request, $index)
    {
        $form = Html::create('form', array(
            'id' => 'setup_frm',
            'class' => 'setup_frm',
            'autocomplete' => 'off',
            'action' => 'index.php/ar/model/action/submit',
            'onsubmit' => 'doFormSubmit',
            'ajax' => true,
            'token' => true,
        ));
        $form->add('header', array(
            'innerHTML' => '<h3>'.$index->name.'</h3>',
        ));
        $fieldset = $form->add('fieldset');
        // status
        $fieldset->add('select', array(
            'id' => 'status',
            'labelClass' => 'g-input icon-valid',
            'itemClass' => 'item',
            'options' => Language::get('AR_ACTIONS'),
            'label' => '{LNG_Action}',
        ));
        // create_date
        $fieldset->add('date', array(
            'id' => 'create_date',
            'labelClass' => 'g-input icon-calendar',
            'itemClass' => 'item',
            'label' => '{LNG_Date}',
            'value' => date('Y-m-d'),
        ));
        // detail
        $fieldset->add('text', array(
            'id' => 'detail',
            'labelClass' => 'g-input icon-edit',
            'itemClass' => 'item',
            'label' => '{LNG_Detail}',
        ));
        $fieldset = $form->add('fieldset', array(
            'class' => 'submit',
        ));
        // submit
        $fieldset->add('submit', array(
            'class' => 'button save large icon-save',
            'value' => '{LNG_Save}',
        ));
        // ar_id
        $fieldset->add('hidden', array(
            'name' => 'ar_id',
            'value' => $index->id,
        ));
        // คืนค่า HTML
        return Language::trans($form->render());
    }
}
