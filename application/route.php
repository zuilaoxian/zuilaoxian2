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
//use think\Route;
//Route::controller('dwz','index/Dwz');
return [
    '[dwz]'     => [
		':id' => ['index/Dwz/index', ['method' => 'get'],['id'=>'\d+']],
		'search' => ['index/Dwz/index', ['method' => 'get']],		
		'view/:id' => ['index/Dwz/view', ['method' => 'get'],['id'=>'\d+']],
		'/' => ['index/Dwz/index', ['method' => 'get']],
	],
    '[ggs]'     => [
		':id' => ['index/Ggs/index', ['method' => 'get'],['id'=>'\d+']],
		'search' => ['index/Ggs/index', ['method' => 'get']],		
		'view/:id' => ['index/Ggs/view', ['method' => 'get'],['id'=>'\d+']],
		'/' => ['index/Ggs/index', ['method' => 'get']],
	],
    '[lizhi]'     => [
		':id' => ['index/LiZhi/index', ['method' => 'get'],['id'=>'\d+']],
		'search' => ['index/LiZhi/index', ['method' => 'get']],		
		'view/:id' => ['index/LiZhi/view', ['method' => 'get'],['id'=>'\d+']],
		'/' => ['index/LiZhi/index', ['method' => 'get']],
	],
    '[rizhi]'     => [
		':id' => ['index/RiZhi/index', ['method' => 'get'],['id'=>'\d+']],
		'search' => ['index/RiZhi/index', ['method' => 'get']],		
		'view/:id' => ['index/RiZhi/view', ['method' => 'get'],['id'=>'\d+']],
		'/' => ['index/RiZhi/index', ['method' => 'get']],
	],
    '[shengjing]'     => [
		':id' => ['index/ShengJing/list', ['method' => 'get'],['id'=>'\d+']],
		'/' => ['index/ShengJing/index', ['method' => 'get']],
	],
    '[QQhead]'     => [
		'/' => ['index/QQhead/index'],
	],
    '[wallpaper]'     => [
		'/' => ['index/index/wallpaper'],
	],
    '[DouYin]'     => [
		'/' => ['index/DouYin/index'],
	],
    '[HaHa]'     => [
		'/' => ['index/HaHa/index'],
	],
    '[QrCode]'     => [
		'/' => ['index/QrCode/index'],
	],
    '[Post]'     => [
		'/' => ['index/Post/index'],
	],
    '[ip]'     => [
		'/' => ['index/Ip/index'],
	],
    '[KouZhao]'     => [
		'/' => ['index/KouZhao/index'],
	],
    '[hxw]'     => [
		'/' => ['index/index/hxw'],
	],
    '[wtp]'     => [
		'/' => ['index/index/wtp'],
	],
    '[ZhaNan]'     => [
		':id' => ['index/ZhaNan/index', ['method' => 'get'],['id'=>'\d+']],
		'search' => ['index/ZhaNan/index', ['method' => 'get']],
		'/' => ['index/ZhaNan/index', ['method' => 'get']],
	],
    '[Xs]'     => [
		':id' => ['index/xs/index', ['method' => 'get'],['id'=>'\d+']],
		'book/:id' => ['index/xs/book', ['method' => 'get'],['id'=>'\d+']],
		'view/:id1/:id2' => ['index/xs/view', ['method' => 'get'],['id1'=>'\d+','id2'=>'\d+']],
		'search' => ['index/xs/search', ['method' => 'get']],
		'xslog' => ['index/xs/xslog', ['method' => 'get']],
		'dellog' => ['index/xs/xslogdel', ['method' => 'post']],
		'/' => ['index/xs/index', ['method' => 'get']],
	],
    '[Xs2]'     => [
		':id' => ['index/xs2/index', ['method' => 'get'],['id'=>'\d+']],
		'book/:id' => ['index/xs2/book', ['method' => 'get'],['id'=>'\d+']],
		'view/:id1/:id2' => ['index/xs2/view', ['method' => 'get'],['id1'=>'\d+','id2'=>'\d+']],
		'search' => ['index/xs2/search', ['method' => 'get']],
		'xslog' => ['index/xs2/xslog', ['method' => 'get']],
		'/' => ['index/xs2/index', ['method' => 'get']],
	],
    '[Xs3]'     => [
		':id' => ['index/xs3/index', ['method' => 'get'],['id'=>'\d+']],
		'book/:id' => ['index/xs3/book', ['method' => 'get'],['id'=>'\d+']],
		'view/:id1/:id2' => ['index/xs3/view', ['method' => 'get'],['id1'=>'\d+','id2'=>'\d+']],
		'search' => ['index/xs3/search', ['method' => 'get']],
		'xslog' => ['index/xs3/xslog', ['method' => 'get']],
		'/' => ['index/xs3/index', ['method' => 'get']],
	],
    '[Xs4]'     => [
		':id' => ['index/Xs4/index', ['method' => 'get'],['id'=>'\d+']],
		'book/:id' => ['index/Xs4/book', ['method' => 'get'],['id'=>'\d+']],
		'view/:id1/:id2' => ['index/Xs4/view', ['method' => 'get'],['id1'=>'\d+','id2'=>'\d+']],
		'search' => ['index/Xs4/search', ['method' => 'get']],
		'xslog' => ['index/Xs4/xslog', ['method' => 'get']],
		'/' => ['index/Xs4/index', ['method' => 'get']],
	],
    '[MM8]'     => [
		':id' => ['index/MM8/index', ['method' => 'get'],['id'=>'\d+']],
		'book/:id' => ['index/MM8/book', ['method' => 'get'],['id'=>'\d+']],
		'view/:list1/:list2/:id' => ['index/MM8/view', ['method' => 'get'],['id'=>'\d+']],
		'/' => ['index/MM8/index', ['method' => 'get']],
	],
    '[yuyan]'     => [
		':id' => ['index/YuYan/index', ['method' => 'get'],['id'=>'\d+']],
		'search' => ['index/YuYan/search', ['method' => 'get']],
		'view/:id' => ['index/YuYan/view', ['method' => 'get'],['id'=>'\d+']],
		'/' => ['index/YuYan/index', ['method' => 'get']],
	],
    '[enterdesk]'     => [
		'list/:id' => ['index/EnterDesk/list', ['method' => 'get'],['id'=>'\d+']],
		'list' => ['index/EnterDesk/list', ['method' => 'get']],
		'view/:id' => ['index/EnterDesk/view', ['method' => 'get'],['id'=>'\d+']],
		'/' => ['index/EnterDesk/list', ['method' => 'get']],
	],
    '[tupianzj]'     => [
		'list/:id' => ['index/TuPianZj/list', ['method' => 'get']],
		'list' => ['index/TuPianZj/list', ['method' => 'get']],
		'view/:id' => ['index/TuPianZj/view', ['method' => 'get'],['id'=>'\w+']],
		'/' => ['index/TuPianZj/list', ['method' => 'get']],
	],
    '[lssdjt]'     => [
		'view/:id' => ['index/Lssdjt/view', ['method' => 'get'],['id'=>'\d+']],
		':m/:d' => ['index/Lssdjt/index', ['method' => 'get'],['m'=>'\d+','d'=>'\d+']],
		'/' => ['index/Lssdjt/index', ['method' => 'get']],
	],
    '[duanxin]'     => [
		':id' => ['index/DuanXin/index', ['method' => 'get']],
		'search' => ['index/DuanXin/index', ['method' => 'get']],
		'/' => ['index/DuanXin/index', ['method' => 'get']],
	],
    '[xhy]'     => [
		'search' => ['index/Xhy/index', ['method' => 'get']],
		'/' => ['index/Xhy/index', ['method' => 'get']],
	],
    '[miyu]'     => [
		'search' => ['index/MiYu/index', ['method' => 'get']],
		'/' => ['index/MiYu/index', ['method' => 'get']],
	],
    '[miyu2]'     => [
		'search' => ['index/MiYu2/index', ['method' => 'get']],
		'/' => ['index/MiYu2/index', ['method' => 'get']],
	],
    '[naojin]'     => [
		'search' => ['index/NaoJin/index', ['method' => 'get']],
		'/' => ['index/NaoJin/index', ['method' => 'get']],
	],
    '[chengyu]'     => [
		'search' => ['index/ChengYu/index', ['method' => 'get']],
		'/' => ['index/ChengYu/index', ['method' => 'get']],
	],
    '[pianfang]'     => [
		'search' => ['index/PianFang/index', ['method' => 'get']],
		'/' => ['index/PianFang/index', ['method' => 'get']],
	],
    '[baike]'     => [
		'search' => ['index/BaiKe/search', ['method' => 'get']],
		':id' => ['index/BaiKe/index', ['method' => 'get'],['id' => '\d+']],
		'/' => ['index/BaiKe/index', ['method' => 'get']],
	],
    '[mymj]'     => [
		'/' => ['index/MyMj/index', ['method' => 'get']],
		'search' => ['index/MyMj/index', ['method' => 'get']],
	],
    '[raokouling]'     => [
		'/' => ['index/RaoKouLing/index', ['method' => 'get']],
		'search' => ['index/RaoKouLing/index', ['method' => 'get']],
	],
    '[yanyu]'     => [
		'/' => ['index/YanYu/index', ['method' => 'get']],
		'search' => ['index/YanYu/index', ['method' => 'get']],
	],
    '[login]'     => [
		'/' => ['index/login/index', ['method' => 'get']],
		'login' => ['index/login/login', ['method' => 'post']],
	],
    '[logout]'     => [
		'/' => ['index/login/logout', ['method' => 'get']],
	],
    '[reg]'     => [
		'/' => ['index/login/reg', ['method' => 'post']],
	],
    '[user]'     => [
		'/' => ['index/User/index', ['method' => 'get']],
	],
    '[edit]'     => [
		'/' => ['index/User/edit', ['method' => 'get']],
	],
    '[edit2]'     => [
		'/' => ['index/User/edit2', ['method' => 'post']],
	],
    '[qiruiyaoye]'     => [
		'book/:id' => ['qiruiyaoye/index/book', ['method' => 'get']],
		'view/:bookid/:viewid' => ['qiruiyaoye/index/view', ['method' => 'get']],
		':id' => ['qiruiyaoye/index/index', ['method' => 'get']],
	],
    '[cat]'     => [
		'/' => ['index/index/cat', ['method' => 'get']],
	],
    '[QrDecode]'     => [
		'/' => ['index/QrDecode/index', ['method' => 'get']],
	],
];
