<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/api/health' => [[['_route' => 'api_health', '_controller' => 'App\\Controller\\HealthController::health'], null, ['GET' => 0], null, false, false, null]],
        '/api/money/insert' => [[['_route' => 'api_money_insert', '_controller' => 'App\\Controller\\MoneyController::insert'], null, ['POST' => 0], null, false, false, null]],
        '/api/money/return' => [[['_route' => 'api_money_return', '_controller' => 'App\\Controller\\MoneyController::return'], null, ['POST' => 0], null, false, false, null]],
    ],
    [ // $regexpList
    ],
    [ // $dynamicRoutes
    ],
    null, // $checkCondition
];
