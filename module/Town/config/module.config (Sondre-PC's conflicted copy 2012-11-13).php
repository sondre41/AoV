<?php

return array(
    'router' => array(
        'routes' => array(
            'town' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/town[/:controller][/:action][/:longitude][/:latitude]',
                	'constraints' => array(
                		'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                		'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                		'longitude' => '[0-9]+',
                		'latitude' => '[0-9]+'
                	),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Town\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index'
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Town\Controller\Index' => 'Town\Controller\IndexController'
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
        	'town' => __DIR__ . '/../view',
        ),
    ),
);

?>