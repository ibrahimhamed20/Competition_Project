<?php

	function lang($phrase) {

		static $lang = array(

			// Navbar Links

			'HOME_ADMIN' 	=> 'الصفحة الرئيسة',
			'CATEGORIES' 	=> 'الاقسام',
			'ITEMS' 		=> 'المنتجات',
			'MEMBERS' 		=> 'الاعضاء',
			'COMMENTS'		=> 'التعليقات',
			'STATISTICS' 	=> 'الاحصائيات',
			'LOGS' 			=> 'التسجيلات',
			'' => '',
			'' => '',
			'' => '',
			'' => '',
			'' => ''
		);

		return $lang[$phrase];

	}
