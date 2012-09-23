<?php

return array(
    'router' => array(
        'routes' => array(
            'map' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/map[/:controller][/:action][/:longitude][/:latitude]',
                	'constraints' => array(
                		'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                		'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                		'longitude' => '[0-9]+',
                		'latitude' => '[0-9]+'
                	),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Map\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index'
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Map\Controller\Index' => 'Map\Controller\IndexController',
            'Map\Controller\Mapsquare' => 'Map\Controller\MapsquareController'
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
        	'map' => __DIR__ . '/../view',
        ),
    ),
);

?>