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