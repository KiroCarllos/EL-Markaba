<?php
//'categories' => 'c,r,u,d',
//            'products' => 'c,r,u,d',
//            'clients' => 'c,r,u,d',
//            'orders' => 'c,r,u,d',
return [
    'role_structure' => [
        'super_admin' => [
            'users' => 'c,r,u,d',
        ],
        'admin' => [],
        'student' => [],
        'company' => [],
    ],

    'permissions_map' => [
        'c' => 'create',
        'r' => 'read',
        'u' => 'update',
        'd' => 'delete'
    ]
];

