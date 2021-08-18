<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    '__pattern__' => [
        'name' => '\w+',
        'id' => '\d+',
    ],
    '[dwz]'     => [
		'list/:id' => ['dwz/index/list', ['method' => 'get'],['id'=>'\d+']],
		'list' => ['dwz/index/list', ['method' => 'get']],
		'search' => ['dwz/index/list', ['method' => 'get']],		
		'view/:id' => ['dwz/index/view', ['method' => 'get'],['id'=>'\d+']],
	],
    '[ggs]'     => [
		'list/:id' => ['ggs/index/list', ['method' => 'get'],['id'=>'\d+']],
		'list' => ['ggs/index/list', ['method' => 'get']],
		'search' => ['ggs/index/list', ['method' => 'get']],		
		'view/:id' => ['ggs/index/view', ['method' => 'get'],['id'=>'\d+']],
	],
    '[lizhi]'     => [
		'list/:id' => ['lizhi/index/list', ['method' => 'get'],['id'=>'\d+']],
		'list' => ['lizhi/index/list', ['method' => 'get']],
		'search' => ['lizhi/index/list', ['method' => 'get']],		
		'view/:id' => ['lizhi/index/view', ['method' => 'get'],['id'=>'\d+']],
	],
    '[rizhi]'     => [
		'list/:id' => ['rizhi/index/list', ['method' => 'get'],['id'=>'\d+']],
		'list' => ['rizhi/index/list', ['method' => 'get']],
		'search' => ['rizhi/index/search', ['method' => 'get']],		
		'view/:id' => ['rizhi/index/view', ['method' => 'get'],['id'=>'\d+']],
	],
    '[qqhead]'     => [
		':id' => ['index/qqhead/index', ['method' => 'get'],['id'=>'\d+']],
		'' => ['index/qqhead/index', ['method' => 'get']],
	],
    '[enterdesk]'     => [
		'list/:id' => ['enterdesk/index/list', ['method' => 'get'],['id'=>'\d+']],
		'list' => ['enterdesk/index/list', ['method' => 'get']],
		'view/:id' => ['enterdesk/index/view', ['method' => 'get'],['id'=>'\d+']],
	],
    '[tupianzj]'     => [
		'list/:id' => ['tupianzj/index/list', ['method' => 'get']],
		'list' => ['tupianzj/index/list', ['method' => 'get']],
		'view/:id' => ['tupianzj/index/view', ['method' => 'get'],['id'=>'\w+']],
	],
    '[lssdjt]'     => [
		'view/:id' => ['lssdjt/index/view', ['method' => 'get'],['id'=>'\d+']],
		':m/:d' => ['lssdjt/index/index', ['method' => 'get'],['m'=>'\d+','d'=>'\d+']],
	],
    '[duanxin]'     => [
		':id' => ['duanxin/index/index', ['method' => 'get']],
		'search' => ['duanxin/index/search', ['method' => 'get']],
	],
    '[xhy]'     => [
		'search' => ['xhy/index/search', ['method' => 'get']],
	],
    '[miyu]'     => [
		'search' => ['miyu/index/search', ['method' => 'get']],
	],
    '[miyu2]'     => [
		'search' => ['miyu2/index/search', ['method' => 'get']],
	],
    '[naojin]'     => [
		'search' => ['naojin/index/search', ['method' => 'get']],
	],
    '[chengyu]'     => [
		'search' => ['chengyu/index/search', ['method' => 'get']],
	],
    '[pianfang]'     => [
		'search' => ['pianfang/index/search', ['method' => 'get']],
	],
    '[baike]'     => [
		'search' => ['baike/index/search', ['method' => 'get']],
		':id' => ['baike/index/index', ['method' => 'get'],['id' => '\d+']],
	],
    '[mymj]'     => [
		'search' => ['mymj/index/search', ['method' => 'get']],
	],
    '[raokouling]'     => [
		'search' => ['raokouling/index/search', ['method' => 'get']],
	],
    '[yanyu]'     => [
		'search' => ['yanyu/index/search', ['method' => 'get']],
	],
    '[login]'     => [
		'login' => ['login/index/login', ['method' => 'post']],
	],
    '[logout]'     => [
		'' => ['login/index/logout', ['method' => 'get']],
	],
    '[reg]'     => [
		'' => ['login/index/reg', ['method' => 'post']],
	],
];
