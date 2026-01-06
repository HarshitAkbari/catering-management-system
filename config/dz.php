<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'name' => env('APP_NAME', 'Catering Management'),


    'public' => [
        'favicon' => 'images/favicon.png',
        'fonts' => [
            'google' => [
                'families' => [
                    'Poppins:300,400,500,600,700',
                ]
            ]
        ],
		'global' => [
			'css' => [
				'vendor/jquery-nice-select/css/nice-select.css',
				'css/style.css',
			],
			'js' => [
				'top'=>[
					'vendor/global/global.min.js',
					'vendor/jquery-nice-select/js/jquery.nice-select.min.js',	
				],
				'bottom'=>[
					'js/custom.min.js',
					'js/dlabnav-init.js',
				],
			],
		],
		'pagelevel' => [
			'css' => [
			],
			'js' => [
			]
		],
	]
];

