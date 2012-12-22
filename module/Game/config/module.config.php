<?php

return array(
    'router' => array(
        'routes' => array(
            'game' => array(
                'type'    => 'segment',
                'options' => array(
                    'route' => '/game[/:controller][/:action][/:longitude][/:latitude]',
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
        	
        	'guild' => array(
        		'type'	  => 'segment',
        		'options' => array(
        			'route' => '/game/guild[/:action][/:id]',
        			'constraints' => array(
        				'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                		'id' => '[0-9]+'
        			),
        			'defaults' => array(
                        '__NAMESPACE__' => 'Game\Controller',
                        'controller'    => 'Guild',
                        'action'        => 'index'
                    ),
        		)
        	),
        		
        	'message' => array(
        		'type'	  => 'segment',
        		'options' => array(
        			'route' => '/game/message[/:action][/:id]',
        			'constraints' => array(
        				'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
        				'id' => '[0-9]+'
        			),
        			'defaults' => array(
        				'__NAMESPACE__' => 'Game\Controller',
        				'controller'    => 'Message',
        				'action'        => 'index'
        			),
        		)
        	),
        	
        	'fort' => array(
        		'type'	  => 'segment',
        		'options' => array(
        			'route' => '/game/fort[/:action][/:id][/:playerId]',
        			'constraints' => array(
        				'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
        				'id' => '[0-9]+',
        				'playerId' => '[0-9]+'
        			),
        			'defaults' => array(
        				'__NAMESPACE__' => 'Game\Controller',
        				'controller'    => 'Fort',
        				'action'        => 'index'
        			),
        		)
        	)
        ),
    ),
	
    'controllers' => array(
        'invokables' => array(
            'Game\Controller\Map' => 'Game\Controller\MapController',
            'Game\Controller\MapSquare' => 'Game\Controller\MapSquareController',
            'Game\Controller\Forest' => 'Game\Controller\ForestController',
            'Game\Controller\Inventory' => 'Game\Controller\InventoryController',
            'Game\Controller\Town' => 'Game\Controller\TownController',
            'Game\Controller\City' => 'Game\Controller\CityController',
            'Game\Controller\Traininggrounds' => 'Game\Controller\TraininggroundsController',
            'Game\Controller\Quest' => 'Game\Controller\QuestController',
            'Game\Controller\Guild' => 'Game\Controller\GuildController',
            'Game\Controller\Message' => 'Game\Controller\MessageController',
            'Game\Controller\Fort' => 'Game\Controller\FortController'
        ),
    ),
	
    'view_manager' => array(
        'template_path_stack' => array(
        	'game' => __DIR__ . '/../view',
        ),
    ),
);

?>