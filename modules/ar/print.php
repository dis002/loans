<?php
/**
 * modules/ar/print.php.
 *
 * @author Goragod Wiriya <admin@goragod.com>
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */
// load Kotchasan
include '../../load.php';
// Initial Kotchasan Framework
$app = Kotchasan::createWebApplication(Gcms\Config::create());
$app->defaultController = 'Ar\Export\Controller';
$app->run();
