<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'router' => array(
        'routes' => array(
            'map' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/map',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Map\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Map\Controller\Index' => 'Map\Controller\IndexController'
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
        	'map' => __DIR__ . '/../view',
        ),
    ),
);
