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

    'name' => env('DZ_APP_NAME', 'Catering Pro'),


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
				'vendor/datatables/css/jquery.dataTables.min.css',
				'css/style.css',
			],
			'js' => [
				'top'=>[
					'vendor/global/global.min.js',
					'vendor/jquery-nice-select/js/jquery.nice-select.min.js',	
				],
				'bottom'=>[
					'vendor/datatables/js/jquery.dataTables.min.js',
					'js/custom.js',
					'js/dlabnav-init.js',
				],
			],
		],
		'pagelevel' => [
			'css' => [
				'ReportController_profitLoss' => [
				],
				'ReportController_payments' => [
				],
				'ReportController_orders' => [
				],
				'ReportController_expenses' => [
				],
				'ReportController_customers' => [
				],
				'OrderController_calendar' => [
					'vendor/fullcalendar/lib/main.min.css',
				],
			],
			'js' => [
				'ReportController_profitLoss' => [
					'vendor/chart.js/Chart.bundle.min.js',
					'vendor/apexchart/apexchart.js',
				],
				'ReportController_payments' => [
					'vendor/chart.js/Chart.bundle.min.js',
					'vendor/apexchart/apexchart.js',
				],
				'ReportController_orders' => [
					'vendor/chart.js/Chart.bundle.min.js',
					'vendor/apexchart/apexchart.js',
				],
				'ReportController_expenses' => [
					'vendor/chart.js/Chart.bundle.min.js',
					'vendor/apexchart/apexchart.js',
				],
				'ReportController_customers' => [
					'vendor/chart.js/Chart.bundle.min.js',
					'vendor/apexchart/apexchart.js',
				],
				'OrderController_calendar' => [
					'vendor/fullcalendar/lib/main.min.js',
				],
			]
		],
	]
];

