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
    '[shengjing]'     => [
		':id' => ['index/ShengJing/list', ['method' => 'get'],['id'=>'\d+']],
		'' => ['index/ShengJing/index', ['method' => 'get']],
	],
    '[QQhead]'     => [
		'' => ['index/QQhead/index'],
	],
    '[DouYin]'     => [
		'' => ['index/DouYin/index'],
	],
    '[HaHa]'     => [
		'' => ['index/HaHa/index'],
	],
    '[QrCode]'     => [
		'' => ['index/QrCode/index'],
	],
    '[Post]'     => [
		'' => ['index/Post/index'],
	],
    '[ip]'     => [
		'' => ['index/Ip/index'],
	],
    '[KouZhao]'     => [
		'' => ['index/KouZhao/index'],
	],
    '[hxw]'     => [
		'' => ['index/index/hxw'],
	],
    '[wtp]'     => [
		'' => ['index/index/wtp'],
	],
    '[ZhaNan]'     => [
		':list' => ['index/ZhaNan/index', ['method' => 'get'],['list'=>'\d+']],
		'search' => ['index/ZhaNan/search', ['method' => 'get']],
		'' => ['index/ZhaNan/index', ['method' => 'get']],
	],
    '[Xs]'     => [
		':id' => ['index/xs/index', ['method' => 'get'],['id'=>'\d+']],
		'book/:id' => ['index/xs/book', ['method' => 'get'],['id'=>'\d+']],
		'view/:id1/:id2' => ['index/xs/view', ['method' => 'get'],['id1'=>'\d+','id2'=>'\d+']],
		'search' => ['index/xs/search', ['method' => 'get']],
		'xslog' => ['index/xs/xslog', ['method' => 'get']],
		'dellog' => ['index/xs/xslogdel', ['method' => 'post']],
		'' => ['index/xs/index'],
	],
    '[Xs2]'     => [
		':id' => ['index/xs2/index', ['method' => 'get'],['id'=>'\d+']],
		'book/:id' => ['index/xs2/book', ['method' => 'get'],['id'=>'\d+']],
		'view/:id1/:id2' => ['index/xs2/view', ['method' => 'get'],['id1'=>'\d+','id2'=>'\d+']],
		'search' => ['index/xs2/search', ['method' => 'get']],
		'xslog' => ['index/xs2/xslog', ['method' => 'get']],
		'' => ['index/xs2/index'],
	],
    '[MM8]'     => [
		':id' => ['index/MM8/index', ['method' => 'get'],['id'=>'\d+']],
		'book/:id' => ['index/MM8/book', ['method' => 'get'],['id'=>'\d+']],
		'view/:list1/:list2/:id' => ['index/MM8/view', ['method' => 'get'],['id'=>'\d+']],
		'' => ['index/MM8/index'],
	],
    '[yuyan]'     => [
		':id' => ['index/YuYan/index', ['method' => 'get'],['id'=>'\d+']],
		'search' => ['index/YuYan/search', ['method' => 'get']],
		'view/:id' => ['index/YuYan/view', ['method' => 'get'],['id'=>'\d+']],
		'' => ['index/YuYan/index'],
	],
    '[enterdesk]'     => [
		'list/:id' => ['index/EnterDesk/list', ['method' => 'get'],['id'=>'\d+']],
		'list' => ['index/EnterDesk/list', ['method' => 'get']],
		'view/:id' => ['index/EnterDesk/view', ['method' => 'get'],['id'=>'\d+']],
		'' => ['index/EnterDesk/list', ['method' => 'get']],
	],
    '[tupianzj]'     => [
		'list/:id' => ['index/TuPianZj/list', ['method' => 'get']],
		'list' => ['index/TuPianZj/list', ['method' => 'get']],
		'view/:id' => ['index/TuPianZj/view', ['method' => 'get'],['id'=>'\w+']],
		'' => ['index/TuPianZj/list', ['method' => 'get']],
	],
    '[lssdjt]'     => [
		'view/:id' => ['lssdjt/index/view', ['method' => 'get'],['id'=>'\d+']],
		':m/:d' => ['lssdjt/index/index', ['method' => 'get'],['m'=>'\d+','d'=>'\d+']],
	],
    '[duanxin]'     => [
		':id' => ['index/DuanXin/index', ['method' => 'get']],
		'search' => ['index/DuanXin/search', ['method' => 'get']],
		'' => ['index/DuanXin/index', ['method' => 'get']],
	],
    '[xhy]'     => [
		'search' => ['index/Xhy/index', ['method' => 'get']],
		'' => ['index/Xhy/index', ['method' => 'get']],
	],
    '[miyu]'     => [
		'search' => ['index/MiYu/index', ['method' => 'get']],
		'' => ['index/MiYu/index', ['method' => 'get']],
	],
    '[miyu2]'     => [
		'search' => ['index/MiYu2/index', ['method' => 'get']],
		'' => ['index/MiYu2/index', ['method' => 'get']],
	],
    '[naojin]'     => [
		'search' => ['index/NaoJin/index', ['method' => 'get']],
		'' => ['index/NaoJin/index', ['method' => 'get']],
	],
    '[chengyu]'     => [
		'search' => ['index/ChengYu/index', ['method' => 'get']],
		'' => ['index/ChengYu/index', ['method' => 'get']],
	],
    '[pianfang]'     => [
		'search' => ['index/PianFang/index', ['method' => 'get']],
		'' => ['index/PianFang/index', ['method' => 'get']],
	],
    '[baike]'     => [
		'search' => ['index/BaiKe/search', ['method' => 'get']],
		':id' => ['index/BaiKe/index', ['method' => 'get'],['id' => '\d+']],
		'' => ['index/BaiKe/index', ['method' => 'get']],
	],
    '[mymj]'     => [
		'search' => ['index/MyMj/index', ['method' => 'get']],
		'' => ['index/MyMj/index', ['method' => 'get']],
	],
    '[raokouling]'     => [
		'search' => ['index/RaoKouLing/index', ['method' => 'get']],
		'' => ['index/RaoKouLing/index'],
	],
    '[yanyu]'     => [
		'search' => ['index/YanYu/index', ['method' => 'get']],
		'' => ['index/YanYu/index', ['method' => 'get']],
	],
    '[login]'     => [
		'login' => ['index/login/login', ['method' => 'post']],
		'' => ['index/login/index', ['method' => 'get']],
	],
    '[logout]'     => [
		'' => ['index/login/logout', ['method' => 'get']],
	],
    '[reg]'     => [
		'' => ['index/login/reg', ['method' => 'post']],
	],
];
