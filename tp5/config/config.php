<?php

	return [
			// 应用调试模式
			'app_debug'  => true,
			'url_route_on'  =>  true,
			'url_route_must'=>  false,
			
			// 会话设置
			
			'session'                => [
					'prefix'         => 'think',
					// 驱动方式 支持redis memcache memcached
					'type'           => '',
					// 是否自动开启 SESSION
					'auto_start'     => true,
			],
			
			
			'view_replace_str' => [
					'__UPLOAD__' => '/uploads',
					'__STATIC__' => '/static',
					'__IMAGES__' => '/static/img',
					'__JS__' => '/static/js',
					'__CSS__' => '/static/css',
			],
			
			//分页配置
			'paginate'               => [
					'type'     => 'bootstrap',
					'var_page' => 'page',
			],
			
			
			
			//验证码
      	'captcha'  => [
        // 验证码字符集合
        'codeSet'  => '2345678abcdefhijkmnpqrstuvwxyzABCDEFGHJKLMNPQRTUVWXY',
        // 验证码字体大小(px)，根据所需进行设置验证码字体大小
        'fontSize' => 30,
        // 是否画混淆曲线
        'useCurve' => true,
        // 验证码图片高度，根据所需进行设置高度
        'imageH'   => '',
        // 验证码图片宽度，根据所需进行设置宽度
        'imageW'   => '',
        // 验证码位数，根据所需设置验证码位数
        'length'   => 4,
        // 验证成功后是否重置
        'reset'    => true
    ],


			
			
			
		/*	
			'template'      => [
					// 模板引擎
					'type'   => 'think',
					//标签库标签开始标签
					'taglib_begin'  =>  '<',
					//标签库标签结束标记
					'taglib_end'    =>  '>',
			],
		*/	
			
	];