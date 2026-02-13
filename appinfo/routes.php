<?php

declare(strict_types=1);

return [
	'routes' => [
		['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
		['name' => 'page#dashboard', 'url' => '/api/dashboard', 'verb' => 'GET'],
		['name' => 'page#completeTask', 'url' => '/api/task/{id}/complete', 'verb' => 'POST'],
	],
];
