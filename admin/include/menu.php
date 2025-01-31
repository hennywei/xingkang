<?php
/**
 * 菜单
 *
 * @copyright  Copyright (c) 2014-2020 SZGR Inc. (http://www.szgr.com.cn)
 * @license    http://www.szgr.com.cn
 * @link       http://www.szgr.com.cn
 * @since      File available since Release v1.1
 */
defined('InShopNC') or exit('Access Invalid!');
/**
 * top 数组是顶部菜单 ，left数组是左侧菜单
 * left数组中'args'=>'welcome,dashboard,dashboard',三个分别为op,act,nav，权限依据act来判断
 */
$arr = array(
		'top' => array(

			0 => array(
				'args' 	=> 'dashboard',
				'text' 	=> $lang['nc_console']),
			1 => array(
				'args' 	=> 'setting',
				'text' 	=> $lang['nc_config']),

        2 => array(
            'args' 	=> 'goods',
            'text' 	=> $lang['nc_goods']),
        3 => array(
            'args' 	=> 'store',
            'text' 	=> $lang['nc_store']),
        4 => array(
            'args'	=> 'member',
            'text'	=> $lang['nc_member']),
        5 => array(
            'args' 	=> 'trade',
            'text'	=> $lang['nc_trade']),
        6 => array(
            'args'	=> 'website',
            'text' 	=> $lang['nc_website']),
        7 => array(
            'args'	=> 'operation',
            'text'	=> $lang['nc_operation']),
        8 => array(
            'args'	=> 'stat',
            'text'	=> $lang['nc_stat']),
            9 => array(
                'args'	=> 'healthplatform',
                'text'	=> $lang['nc_healthplatform']),
            10 => array(
                'args'	=> 'storehouse',
                'text'	=> '仓库'),
            11 => array(
                'args'	=> 'community',
                'text'	=> '社区'),
            12 => array(
                'args' => 'finance',
                'text' => '财务'
            )
		),
		'left' =>array(

			0 => array(
				'nav' => 'dashboard',
				'text' => $lang['nc_normal_handle'],
				'list' => array(
					array('args'=>'welcome,dashboard,dashboard',			'text'=>'系统总览'),
					array('args'=>'chartpage,dashboard,dashboard',			'text'=>'图表'),
//					array('args'=>'aboutus,dashboard,dashboard',			'text'=>$lang['nc_aboutus']),
//					array('args'=>'base,setting,dashboard',	'text'=>$lang['nc_web_set']),
//					array('args'=>'member,member,dashboard',				'text'=>$lang['nc_member_manage']),
//					array('args'=>'store,store,dashboard',					'text'=>$lang['nc_store_manage']),
//					array('args'=>'goods,goods,dashboard',					'text'=>$lang['nc_goods_manage']),
//					array('args'=>'index,order,dashboard',			        'text'=>$lang['nc_order_manage']),
				)
			),
			1 => array(
				'nav' => 'setting',
				'text' => $lang['nc_config'],
				'list' => array(
					array('args'=>'base,setting,setting',			'text'=>$lang['nc_web_set']),
					array('args'=>'qq,account,setting',		        'text'=>$lang['nc_web_account_syn']),
					array('args'=>'param,upload,setting',			'text'=>$lang['nc_upload_set']),
					array('args'=>'seo,setting,setting',			'text'=>$lang['nc_seo_set']),
					array('args'=>'email,message,setting',			'text'=>$lang['nc_message_set']),
					array('args'=>'system,payment,setting',			'text'=>$lang['nc_pay_method']),
					array('args'=>'admin,admin,setting',			'text'=>$lang['nc_limit_manage']),
					array('args'=>'index,express,setting',			'text'=>$lang['nc_admin_express_set']),
					array('args'=>'index,offpay_area,setting',		'text'=>$lang['nc_admin_offpay_area_set']),
					array('args'=>'clear,cache,setting',			'text'=>$lang['nc_admin_clear_cache']),
					array('args'=>'perform,perform,setting',		'text'=>$lang['nc_admin_perform_opt']),
					array('args'=>'search,search,setting',			'text'=>$lang['nc_admin_search_set']),
					array('args'=>'list,admin_log,setting',			'text'=>$lang['nc_admin_log']),
				)
			),

			2 => array(
				'nav' => 'goods',
				'text' => $lang['nc_goods'],
				'list' => array(
					array('args'=>'goods_class,goods_class,goods',			'text'=>$lang['nc_class_manage']),
					array('args'=>'brand,brand,goods',						'text'=>$lang['nc_brand_manage']),
					array('args'=>'goods,goods,goods',						'text'=>$lang['nc_goods_manage']),
                    array('args'=>'stock,goods,goods',						'text'=>'库存管理'),
					array('args'=>'type,type,goods',						'text'=>$lang['nc_type_manage']),
					array('args'=>'spec,spec,goods',						'text'=>$lang['nc_spec_manage']),
					array('args'=>'list,goods_album,goods',					'text'=>$lang['nc_album_manage']),
					array('args'=>'changeprice,goods,goods',						'text'=>'商品调价审核'),
				)
			),
			3 => array(
				'nav' => 'store',
				'text' => $lang['nc_store'],
				'list' => array(
					array('args'=>'store,store,store',						'text'=>$lang['nc_store_manage']),
					array('args'=>'store_grade,store_grade,store',			'text'=>$lang['nc_store_grade']),
					array('args'=>'store_class,store_class,store',			'text'=>$lang['nc_store_class']),
					array('args'=>'store_domain_setting,domain,store',		'text'=>$lang['nc_domain_manage']),
					array('args'=>'stracelist,sns_strace,store',			'text'=>$lang['nc_s_snstrace']),
				)
			),
			4 => array(
				'nav' => 'member',
				'text' => $lang['nc_member'],
				'list' => array(
					array('args'=>'member,member,member',					'text'=>$lang['nc_member_manage']),
					array('args'=>'notice,notice,member',					'text'=>$lang['nc_member_notice']),
					array('args'=>'addpoints,points,member',				'text'=>$lang['nc_member_pointsmanage']),
					array('args'=>'predeposit,predeposit,member',			'text'=>$lang['nc_member_predepositmanage']),
					array('args'=>'sharesetting,sns_sharesetting,member',	'text'=>$lang['nc_binding_manage']),
					array('args'=>'class_list,sns_malbum,member',			'text'=>$lang['nc_member_album_manage']),
					array('args'=>'tracelist,snstrace,member',				'text'=>$lang['nc_snstrace']),
					array('args'=>'member_tag,sns_member,member',			'text'=>$lang['nc_member_tag']),
                    array('args'=>'consumesum,member,member',			'text'=>'消费汇总'),
                    array('args'=>'rechargesum,member,member',			'text'=>'充值下账汇总'),
                    array('args'=>'check,member,member',			'text'=>'会员储值积分对账')
				)
			),
			5 => array(
				'nav' => 'trade',
				'text' => $lang['nc_trade'],
				'list' => array(
					array('args'=>'index,order,trade',				'text'=>$lang['nc_order_manage']),
					array('args'=>'refund_manage,refund,trade',				'text'=>'退款管理'),
					array('args'=>'return_manage,return,trade',				'text'=>'退货管理'),
					array('args'=>'consulting,consulting,trade',			'text'=>$lang['nc_consult_manage']),
					array('args'=>'inform_list,inform,trade',				'text'=>$lang['nc_inform_config']),
					array('args'=>'evalgoods_list,evaluate,trade',			'text'=>$lang['nc_goods_evaluate']),
					array('args'=>'complain_new_list,complain,trade',		'text'=>$lang['nc_complain_config']),					
				)
			),
			6 => array(
				'nav' => 'website',
				'text' => $lang['nc_website'],
				'list' => array(
					array('args'=>'article_class,article_class,website',	'text'=>$lang['nc_article_class']),
					array('args'=>'article,article,website',				'text'=>$lang['nc_article_manage']),
					array('args'=>'document,document,website',				'text'=>$lang['nc_document']),
					array('args'=>'navigation,navigation,website',			'text'=>$lang['nc_navigation']),
					array('args'=>'ap_manage,adv,website',					'text'=>$lang['nc_adv_manage']),
					array('args'=>'web_config,web_config,website',			'text'=>$lang['nc_web_index']),
					array('args'=>'rec_list,rec_position,website',			'text'=>$lang['nc_admin_res_position']),
				)
			),
			7 => array(
				'nav' => 'operation',
				'text' => $lang['nc_operation'],
				'list' => array(
					array('args'=>'setting,operation,operation',			    'text'=>$lang['nc_operation_set']),
					array('args'=>'groupbuy_template_list,groupbuy,operation',	'text'=>$lang['nc_groupbuy_manage']),
					array('args'=>'xianshi_apply,promotion_xianshi,operation',	'text'=>$lang['nc_promotion_xianshi']),
					array('args'=>'mansong_apply,promotion_mansong,operation',	'text'=>$lang['nc_promotion_mansong']),
					array('args'=>'bundling_list,promotion_bundling,operation',	'text'=>$lang['nc_promotion_bundling']),
					array('args'=>'goods_list,promotion_booth,operation',		'text'=>$lang['nc_promotion_booth']),
					array('args'=>'voucher_apply,voucher,operation',            'text'=>$lang['nc_voucher_price_manage']),
					array('args'=>'index,bill,operation',					    'text'=>$lang['nc_bill_manage']),
					array('args'=>'activity,activity,operation',				'text'=>$lang['nc_activity_manage']),
					array('args'=>'pointprod,pointprod,operation',				'text'=>$lang['nc_pointprod']),
				)
			),
			8 => array(
				'nav' => 'stat',
				'text' => $lang['nc_stat'],
				'list' => array(
					array('args'=>'newmember,stat_member,stat',			'text'=>$lang['nc_statmember']),
					array('args'=>'newstore,stat_store,stat',			'text'=>$lang['nc_statstore']),
					array('args'=>'goods,stat_trade,stat',				'text'=>$lang['nc_stattrade']),
					array('args'=>'promotion,stat_marketing,stat',		'text'=>$lang['nc_statmarketing']),
					array('args'=>'refund,stat_aftersale,stat',	'text'=>$lang['nc_stataftersale']),
				)
			),
            9 => array(
                'nav' => 'healthplatform',
                'text' => '业务督导',
                'list' => array(
                    array('args'=>'call,healthplatform,healthplatform',			'text'=>'呼叫中心'),
                    array('args'=>'index,healthplatform,healthplatform',			'text'=>'回访抽查'),
					array('args'=>'sleep,healthplatform,healthplatform',			'text'=>'睡眠顾客查询'),
					array('args'=>'birthday,healthplatform,healthplatform',			'text'=>'顾客生日提醒'),
                    array('args'=>'statistical,healthplatform,healthplatform',			'text'=>'统计'),
                    array('args'=>'test,healthplatform,healthplatform',			'text'=>'测试')
                )
            ),
            10 => array(
                'nav' => 'storehouse',
                'text' => '仓库',
                'list' => array(
                    array('args'=>'detail,storehouse,storehouse',			'text'=>'仓库单据明细'),
                    array('args'=>'sum,storehouse,storehouse',			'text'=>'仓库单据汇总')
                )
            ),
            11 => array(
                'nav' => 'community',
                'text' => '社区',
                'list' => array(
                    array('args'=>'incomedetail,community,community',			'text'=>'结算单明细查询'),
                    array('args'=>'incomesum,community,community',			'text'=>'门诊收费员报表'),
                    array('args'=>'prescriptiondetail,community,community',			'text'=>'就诊明细查询'),
                    array('args'=>'prescriptionsum,community,community',			'text'=>'就诊情况汇总'),
                    array('args'=>'clinicstatistic,community,community',			'text'=>'门诊收入分析'),
                    array('args'=>'consumesum,member,community',			'text'=>'消费汇总'),
                    array('args'=>'rechargesum,member,community',			'text'=>'充值下账汇总'),
                    array('args'=>'query,healthfile,community',	'text'=>'健康档案查询'),
                    array('args'=>'sum,healthfile,community',	'text'=>'健康档案汇总'),
                )
            ),
            12 => array(
                'nav' => 'finance',
                'text' => '财务',
                'list' => array(
                    array('args'=>'saledetail,finance,finance',			'text'=>'销售明细查询'),
                    array('args'=>'saledetailmanager,finance,finance',			'text'=>'销售明细财务分类管理'),
                    array('args'=>'financesum,finance,finance',			'text'=>'门诊收入统计'),
                    array('args'=>'financeinsum,finance,finance',			'text'=>'住院收入统计'),
                    array('args'=>'financegoodsum,finance,finance',			'text'=>'单品毛利分析'),
                    array('args'=>'detail,storehouse,finance',			'text'=>'仓库单据明细'),
                    array('args'=>'sum,storehouse,finance',			'text'=>'仓库单据汇总')
                )
            )
		)
);

if(C('mobile_isuse')){
	$arr['top'][] = array(
				'args'	=> 'mobile',
				'text'	=> $lang['nc_mobile']);
	$arr['left'][] = array(
				'nav' => 'mobile',
				'text' => $lang['nc_mobile'],
				'list' => array(
					array('args'=>'mb_ad_list,mb_ad,mobile',				'text'=>$lang['nc_mobile_adset']),
					array('args'=>'mb_home_list,mb_home,mobile',			'text'=>$lang['nc_mobile_homeset']),
					array('args'=>'mb_category_list,mb_category,mobile',	'text'=>$lang['nc_mobile_catepic']),
                    //前台还没坐反馈，暂时隐藏
					//array('args'=>'flist,mb_feedback,mobile',					'text'=>$lang['nc_mobile_feedback'])
				)
			);
}
if(C('microshop_isuse') !== null){
	$arr['top'][] = array(
				'args'	=> 'microshop',
				'text'	=> $lang['nc_microshop']);
	$arr['left'][] = array(
				'nav' => 'microshop',
				'text' => $lang['nc_microshop'],
				'list' => array(
					0 => array('args'=>'manage,microshop,microshop','text'=>$lang['nc_microshop_manage']),
					1 => array('args'=>'goods_manage,microshop,microshop','text'=>$lang['nc_microshop_goods_manage']),
					2 => array('args'=>'goodsclass_list,microshop,microshop','text'=>$lang['nc_microshop_goods_class']),
					3 => array('args'=>'personal_manage,microshop,microshop','text'=>$lang['nc_microshop_personal_manage']),
					4 => array('args'=>'personalclass_list,microshop,microshop','text'=>$lang['nc_microshop_personal_class']),
					5 => array('args'=>'store_manage,microshop,microshop','text'=>$lang['nc_microshop_store_manage']),
					6 => array('args'=>'comment_manage,microshop,microshop','text'=>$lang['nc_microshop_comment_manage']),
					7 => array('args'=>'adv_manage,microshop,microshop','text'=>$lang['nc_microshop_adv_manage']),
				)
			);
}
if(C('cms_isuse') !== null){
	$arr['top'][] = array(
				'args'	=> 'cms',
				'text'	=> $lang['nc_cms']);
	$arr['left'][] = array(
				'nav' => 'cms',
				'text' => $lang['nc_cms'],
				'list' => array(
					0 => array('args'=>'cms_manage,cms_manage,cms','text'=>$lang['nc_cms_manage']),
                    1 => array('args'=>'cms_index,cms_index,cms','text'=>$lang['nc_cms_index_manage']),
                    2 => array('args'=>'cms_article_list,cms_article,cms','text'=>$lang['nc_cms_article_manage']),
                    3 => array('args'=>'cms_article_class_list,cms_article_class,cms','text'=>$lang['nc_cms_article_class']),
                    4 => array('args'=>'cms_picture_list,cms_picture,cms','text'=>$lang['nc_cms_picture_manage']),
                    5 => array('args'=>'cms_picture_class_list,cms_picture_class,cms','text'=>$lang['nc_cms_picture_class']),
                    6 => array('args'=>'cms_special_list,cms_special,cms','text'=>$lang['nc_cms_special_manage']),
                    7 => array('args'=>'cms_navigation_list,cms_navigation,cms','text'=>$lang['nc_cms_navigation_manage']),
                    8 => array('args'=>'cms_tag_list,cms_tag,cms','text'=>$lang['nc_cms_tag_manage']),
                    9 => array('args'=>'comment_manage,cms_comment,cms','text'=>$lang['nc_cms_comment_manage']),
				)
			);
}
	
 if(C('circle_isuse') !== null){
	$arr['top'][] = array(
			'args'	=> 'circle',
			'text'	=> $lang['nc_circle']);
	$arr['left'][] = array(
			'nav'	=> 'circle',
			'text'	=> $lang['nc_circle'],
			'list'	=> array(
					0 => array('args'=>'index,circle_setting,circle','text'=>$lang['nc_circle_setting']),
					1 => array('args'=>'index,circle_memberlevel,circle','text'=>$lang['nc_circle_memberlevel']),
					2 => array('args'=>'class_list,circle_class,circle','text'=>$lang['nc_circle_classmanage']),
					3 => array('args'=>'circle_list,circle_manage,circle','text'=>$lang['nc_circle_manage']),
					4 => array('args'=>'theme_list,circle_theme,circle','text'=>$lang['nc_circle_thememanage']),
					5 => array('args'=>'member_list,circle_member,circle','text'=>$lang['nc_circle_membermanage']),
					6 => array('args'=>'inform_list,circle_inform,circle','text'=>$lang['nc_circle_informnamage']),
					7 => array('args'=>'adv_manage,circle_setting,circle','text'=>$lang['nc_circle_advmanage']),
					8 => array('args'=>'index,circle_cache,circle','text'=>$lang['nc_circle_cache'])
			)
	);
 }

return $arr;
?>
