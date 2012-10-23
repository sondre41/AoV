<?php

return array(
    'router' => array(
        'routes' => array(
            'game' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/game[/:controller][/:action][/:longitude][/:latitude]',
                	'constraints' => array(
                		'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                		'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                		'longitude' => '[0-9]+',
                		'latitude' => '[0-9]+'
                	),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Game\Controller',
                        'controller'    => 'Map',
                        'action'        => 'index'
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Game\Controller\Map' => 'Game\Controller\MapController',
            //'Game\Controller\Mapsquare' => 'Game\Controller\MapsquareController',
            'Game\Controller\Inventory' => 'Game\Controller\InventoryController',
            'Game\Controller\Town' => 'Game\Controller\TownController',
            'Game\Controller\City' => 'Game\Controller\CityController',
            'Game\Controller\Traininggrounds' => 'Game\Controller\TraininggroundsController'
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
        	'game' => __DIR__ . '/../view',
        ),
    ),
);

?>