<?php
/**
 * 会员管理
 *
 * 
 *
 *
 * @copyright  Copyright (c) 2014-2020 SZGR Inc. (http://www.szgr.com.cn)
 * @license    http://www.szgr.com.cn
 * @link       http://www.szgr.com.cn
 * @since      File available since Release v1.1
 */
defined ( 'InShopNC' ) or exit ( 'Access Invalid!' );
class memberControl extends SystemControl {
	const EXPORT_SIZE = 5000;
	public function __construct() {
		parent::__construct ();
		Language::read ( 'member' );
		$conn = require (BASE_DATA_PATH . '/../core/framework/db/mssqlpdo.php');
		$treesql = 'select  b.id , b.name,b.districtnumber,b.parentid pId from map_org_wechat a, Organization b where a.orgid = b.id ';
		$treestmt = $conn->query ( $treesql );
		$this->treedata_list = array ();
		while ( $row = $treestmt->fetch ( PDO::FETCH_OBJ ) ) {
			array_push ( $this->treedata_list, $row );
		}
		Tpl::output ( 'treelist', $this->treedata_list );
	}
	
	/**
	 * 会员管理
	 */
	public function memberOp() {
		$lang = Language::getLangContent ();
		$orderbys = array(
			array('txt'=>'预存余额','col'=> ' available_predeposit '),
			array('txt'=>'赠送余额','col'=> ' fConsumeBalance '),
			array('txt'=>'消费积分','col'=> ' member_points '));
		Tpl::output('orderbys',$orderbys);
		$model_member = Model ( 'member' );
		/**
		 * 检索条件
		 */
		if ($_GET['orgids']) {
			$condition ['CreateOrgID'] = array (
				'in',
				 $_GET['orgids']
			);
		}

		if (isset($_GET['cardtype']) and $_GET['cardtype'] != '') {
			$condition ['cardtype'] = $_GET['cardtype'];
		}

		if (isset($_GET['cardgrade']) and $_GET['cardgrade'] != '') {
			$condition ['cardgrade'] = $_GET['cardgrade'];
		}

		if(!isset($_GET['orderby'])){
			$_GET['orderby'] = '预存余额';
		}



		if(!isset($_GET['order'])){
			$ordersql = 'desc';
		}else{
			$ordersql = $_GET['order'];
		}
		if($_GET['orderby']){
			foreach($orderbys as $orderby){
				if($orderby['txt']==$_GET['orderby']){
					$order = $orderby['col'] .' ' . $ordersql;
					break;
				}
			}
		}
		if ($_GET ['search_field_value'] != '') {
			switch ($_GET ['search_field_name']) {
				case 'member_name' :
					$condition ['member_name'] = array (
							'like',
							'%' . trim ( $_GET ['search_field_value'] ) . '%' 
					);
					break;
				case 'member_email' :
					$condition ['member_email'] = array (
							'like',
							'%' . trim ( $_GET ['search_field_value'] ) . '%' 
					);
					break;
				case 'member_truename' :
					$condition ['member_truename'] = array (
							'like',
							'%' . trim ( $_GET ['search_field_value'] ) . '%' 
					);
					break;
			}
		}
		if ($_GET ['member_id'] != '') {
			$condition ['member_id'] = array (
					'like',
					'%' . trim ( $_GET ['member_id'] ) . '%' 
			);
		}
		switch ($_GET ['search_state']) {
			case 'no_informallow' :
				$condition ['inform_allow'] = '2';
				break;
			case 'no_isbuy' :
				$condition ['is_buy'] = '0';
				break;
			case 'no_isallowtalk' :
				$condition ['is_allowtalk'] = '0';
				break;
			case 'no_memberstate' :
				$condition ['member_state'] = '0';
				break;
		}
		/**
		 * 排序
		 */
//		$order = trim ( $_GET ['search_sort'] );
		if (empty ( $order )) {
			$order = 'member_id desc';
		}
		$member_list = $model_member->getMemberList ( $condition, '*', 10, $order );
		/**
		 * 整理会员信息
		 */
		if (is_array ( $member_list )) {
			foreach ( $member_list as $k => $v ) {
				$member_list [$k] ['member_time'] = $v ['member_time'] ? date ( 'Y-m-d H:i:s', $v ['member_time'] ) : '';
				$member_list [$k] ['member_login_time'] = $v ['member_login_time'] ? date ( 'Y-m-d H:i:s', $v ['member_login_time'] ) : '';
			}
		}


		Tpl::output ( 'member_id', trim ( $_GET ['member_id'] ) );
		Tpl::output ( 'search_sort', trim ( $_GET ['search_sort'] ) );
		Tpl::output ( 'search_field_name', trim ( $_GET ['search_field_name'] ) );
		Tpl::output ( 'search_field_value', trim ( $_GET ['search_field_value'] ) );
		Tpl::output ( 'member_list', $member_list );
		Tpl::output ( 'page', $model_member->showpage () );
		Tpl::showpage ( 'member.index' );
	}
	
	/**
	 * 会员修改
	 */
	public function member_editOp() {
		$lang = Language::getLangContent ();
		$model_member = Model ( 'member' );
		/**
		 * 保存
		 */
		if (chksubmit ()) {
			/**
			 * 验证
			 */
			$obj_validate = new Validate ();
			$obj_validate->validateparam = array (
					array (
							"input" => $_POST ["member_email"],
							"require" => "true",
							'validator' => 'Email',
							"message" => $lang ['member_edit_valid_email'] 
					) 
			);
			$error = $obj_validate->validate ();
			if ($error != '') {
				showMessage ( $error );
			} else {
				$update_array = array ();
				$update_array ['member_id'] = intval ( $_POST ['member_id'] );
				if (! empty ( $_POST ['member_passwd'] )) {
					$update_array ['member_passwd'] = md5 ( $_POST ['member_passwd'] );
				}
				$update_array ['member_email'] = trim ( $_POST ['member_email'] );
				$update_array ['member_truename'] = trim ( $_POST ['member_truename'] );
				$update_array ['member_sex'] = trim ( $_POST ['member_sex'] );
				$update_array ['member_qq'] = trim ( $_POST ['member_qq'] );
				$update_array ['member_ww'] = trim ( $_POST ['member_ww'] );
				$update_array ['inform_allow'] = trim ( $_POST ['inform_allow'] );
				$update_array ['is_buy'] = trim ( $_POST ['isbuy'] );
				$update_array ['is_allowtalk'] = trim ( $_POST ['allowtalk'] );
				$update_array ['member_state'] = trim ( $_POST ['memberstate'] );
				if (! empty ( $_POST ['member_avatar'] )) {
					$update_array ['member_avatar'] = $_POST ['member_avatar'];
				}
				$result = $model_member->updateMember ( $update_array, intval ( $_POST ['member_id'] ) );
				if ($result) {
					$url = array (
							array (
									'url' => 'index.php?act=member&op=member',
									'msg' => $lang ['member_edit_back_to_list'] 
							),
							array (
									'url' => 'index.php?act=member&op=member_edit&member_id=' . intval ( $_POST ['member_id'] ),
									'msg' => $lang ['member_edit_again'] 
							) 
					);
					$this->log ( L ( 'nc_edit,member_index_name' ) . '[ID:' . $_POST ['member_id'] . ']', 1 );
					showMessage ( $lang ['member_edit_succ'], $url );
				} else {
					showMessage ( $lang ['member_edit_fail'] );
				}
			}
		}
		$condition ['member_id'] = intval ( $_GET ['member_id'] );
		$member_array = $model_member->infoMember ( $condition );
		
		Tpl::output ( 'member_array', $member_array );
		Tpl::showpage ( 'member.edit' );
	}
	
	/**
	 * 新增会员
	 */
	public function member_addOp() {
		$lang = Language::getLangContent ();
		$model_member = Model ( 'member' );
		/**
		 * 保存
		 */
		if (chksubmit ()) {
			/**
			 * 验证
			 */
			$obj_validate = new Validate ();
			$obj_validate->validateparam = array (
					array (
							"input" => $_POST ["member_email"],
							"require" => "true",
							'validator' => 'Email',
							"message" => $lang ['member_edit_valid_email'] 
					) 
			);
			$error = $obj_validate->validate ();
			if ($error != '') {
				showMessage ( $error );
			} else {
				$insert_array = array ();
				$insert_array ['member_name'] = trim ( $_POST ['member_name'] );
				$insert_array ['member_passwd'] = trim ( $_POST ['member_passwd'] );
				$insert_array ['member_email'] = trim ( $_POST ['member_email'] );
				$insert_array ['member_truename'] = trim ( $_POST ['member_truename'] );
				$insert_array ['member_sex'] = trim ( $_POST ['member_sex'] );
				$insert_array ['member_qq'] = trim ( $_POST ['member_qq'] );
				$insert_array ['member_ww'] = trim ( $_POST ['member_ww'] );
				// 默认允许举报商品
				$insert_array ['inform_allow'] = '1';
				if (! empty ( $_POST ['member_avatar'] )) {
					$insert_array ['member_avatar'] = trim ( $_POST ['member_avatar'] );
				}
				
				$result = $model_member->addMember ( $insert_array );
				if ($result) {
					$url = array (
							array (
									'url' => 'index.php?act=member&op=member',
									'msg' => $lang ['member_add_back_to_list'] 
							),
							array (
									'url' => 'index.php?act=member&op=member_add',
									'msg' => $lang ['member_add_again'] 
							) 
					);
					$this->log ( L ( 'nc_add,member_index_name' ) . '[	' . $_POST ['member_name'] . ']', 1 );
					showMessage ( $lang ['member_add_succ'], $url );
				} else {
					showMessage ( $lang ['member_add_fail'] );
				}
			}
		}
		Tpl::showpage ( 'member.add' );
	}
	
	/**
	 * ajax操作
	 */
	public function ajaxOp() {
		switch ($_GET ['branch']) {
			/**
			 * 验证会员是否重复
			 */
			case 'check_user_name' :
				$model_member = Model ( 'member' );
				$condition ['member_name'] = trim ( $_GET ['member_name'] );
				$condition ['no_member_id'] = intval ( $_GET ['member_id'] );
				$list = $model_member->infoMember ( $condition );
				if (empty ( $list )) {
					echo 'true';
					exit ();
				} else {
					echo 'false';
					exit ();
				}
				break;
			/**
			 * 验证邮件是否重复
			 */
			case 'check_email' :
				$model_member = Model ( 'member' );
				$condition ['member_email'] = trim ( $_GET ['member_email'] );
				$condition ['no_member_id'] = intval ( $_GET ['member_id'] );
				$list = $model_member->infoMember ( $condition );
				if (empty ( $list )) {
					echo 'true';
					exit ();
				} else {
					echo 'false';
					exit ();
				}
				break;
		}
	}
	
	
	public function consumesumOp() {
		$conn = require (BASE_DATA_PATH . '/../core/framework/db/mssqlpdo.php');
		if (! isset ( $_GET ['search_type'] )) {
			$_GET ['search_type'] = '0';
		}
		$sqlarray = array (
				'membername' => 'member.sName as "membername"',
				'memberid' => ' member.sMemberID as "memberid" ',
				'year' => ' year(a.dCO_Date) as "year" ',
				'month' => ' left(convert(varchar,dCO_Date,112),6) as  "month" ',
				'day' => ' convert(varchar,dCO_Date,112) as "day" ',
				'OrgID' => ' org.name as "OrgID" ' 
		);
		$sqlarray1 = array (
			'membername' => 'member.sName as "membername"',
			'memberid' => ' member.sMemberID as "memberid" ',
			'year' => ' year(a.RechargeDate) as "year" ',
			'month' => ' left(convert(varchar,RechargeDate,112),6) as  "month" ',
			'day' => ' convert(varchar,RechargeDate,112) as "day" ',
			'OrgID' => ' org.name as "OrgID" '
		);
		$sqlarray2 = array (
			'membername' => '  "membername"',
			'memberid' => ' "memberid" ',
			'year' => '   "year" ',
			'month' => '    "month" ',
			'day' => '   "day" ',
			'OrgID' => '   "OrgID" '
		);
		$config = array (
				'sumcol' => array (
						'OrgID' => array (
								name => 'OrgID',
								'text' => '机构' 
						),
						'member' => array (
								'text' => '会员',
								name => 'member',
								'cols' => array (
										0 => array (
												name => 'memberid',
												'text' => '会员号码' 
										),
										1 => array (
												name => 'membername',
												'text' => '会员名称' 
										) 
								) 
						),
						'year' => array (
								'text' => '年',
								name => 'year',
								uncheck => 'month,day' 
						),
						'month' => array (
								'text' => '月',
								name => 'month',
								uncheck => 'year,day' 
						),
						'day' => array (
								'text' => '日',
								name => 'day',
								uncheck => 'year,month' 
						) 
				) 
		);
		Tpl::output ( 'config', $config );
		
		// 处理汇总字段
		$sumtype = $_GET ['sumtype'];
		if ($sumtype == null) {
			$sumtype = array (
					0 => "OrgID" 
			);
			$_GET ['sumtype'] = $sumtype;
		}
		$checked = $_GET ['checked'];
		$page = new Page ();
		$page->setEachNum ( 10 );
		$page->setNowPage ( $_REQUEST ["curpage"] );
		$sql = 'from Center_CheckOut a  ,
             Organization org
          where isnull(smemberid , \'\') <> \'\'
           and a.orgid = org.id ';
		
		if ($_GET ['query_start_time']) {
			$sql = $sql . ' and a.dCO_Date >=\'' . $_GET ['query_start_time'] . '\'';
		}
		
		if ($_GET ['query_end_time']) {
			$sql = $sql . ' and a.dCO_Date < dateadd(day,1,\'' . $_GET ['query_end_time'] . '\')';
		}
		
		// 处理树的参数
		if ($_GET ['orgids']) {
			$sql = $sql . ' and a.OrgID in ( ' . implode ( ',', $_GET ['orgids'] ) . ')';
		}

		$msql = 'from center_MemberRecharge a  ,
             Organization org
          where [type]=2
           and a.orgid = org.id ';

		if ($_GET ['query_start_time']) {
			$msql = $msql . ' and a.RechargeDate >=\'' . $_GET ['query_start_time'] . '\'';
		}

		if ($_GET ['query_end_time']) {
			$msql = $msql . ' and a.RechargeDate < dateadd(day,1,\'' . $_GET ['query_end_time'] . '\')';
		}

		// 处理树的参数
		if ($_GET ['orgids']) {
			$msql = $msql . ' and a.OrgID in ( ' . implode ( ',', $_GET ['orgids'] ) . ')';
		}
		
		$search_type = $_GET ['search_type'];
		// echo $search_type;
		$colconfig = $config;
		// var_dump($config[intval($search_type)]);
		$displaycol = array ();
		$displaytext = array ();
		$sumcol = array ();
		$sumcol1 = array ();
		$sumcol2 = array();
		$totalcol = array ();
		$totalcol1 = array ();
		$groupbycol = array ();
		$groupbycol1 = array ();
		foreach ( $sumtype as $i => $v ) {
			// var_dump($colconfig['sumcol'][$v]);
			if (isset ( $colconfig ['sqlwher'] )) {
				$sql = $sql . $colconfig ['sqlwher'];
			}
			if (isset ( $colconfig ['sumcol'] [$v] )) {
				if (isset ( $colconfig ['sumcol'] [$v] ['cols'] )) {
					
					foreach ( $colconfig ['sumcol'] [$v] ['cols'] as $item ) {
						// echo $item['name'] . '<br>';
						array_push ( $sumcol, $sqlarray [$item ['name']] );
						array_push ( $sumcol1, $sqlarray1 [$item ['name']] );
						array_push ( $sumcol2, $sqlarray2 [$item ['name']] );
						array_push ( $displaycol, $item ['name'] );
						array_push ( $displaytext, $item ['text'] );
						$itemsplit = explode ( ' as ', $sqlarray [$item ['name']] );
						$itemsplit1 = explode ( ' as ', $sqlarray [$item ['name']] );
						array_push ( $totalcol, ' null as ' . $itemsplit [1] );
						array_push ( $totalcol1, ' null as ' . $itemsplit1 [1] );
						$str = strtolower ( str_replace ( ' ', '', trim ( $itemsplit [0] ) ) );
						if (substr ( $str, 0, 4 ) != 'sum(' && substr ( $str, 0, 6 ) != 'count(')
							array_push ( $groupbycol, $itemsplit [0] );
						$str1 = strtolower ( str_replace ( ' ', '', trim ( $itemsplit1 [0] ) ) );
						if (substr ( $str1, 0, 4 ) != 'sum(' && substr ( $str1, 0, 6 ) != 'count(')
							array_push ( $groupbycol1, $itemsplit1 [0] );
					}
				} else {
					$item = $colconfig ['sumcol'] [$v];
					array_push ( $sumcol, $sqlarray [$item ['name']] );
					array_push ( $sumcol1, $sqlarray1 [$item ['name']] );
					array_push ( $sumcol2, $sqlarray2 [$item ['name']] );
					array_push ( $displaycol, $item ['name'] );
					array_push ( $displaytext, $item ['text'] );
					$itemsplit = explode ( ' as ', $sqlarray [$item ['name']] );
					$itemsplit1 = explode ( ' as ', $sqlarray1 [$item ['name']] );
					array_push ( $totalcol, ' null as ' . $itemsplit [1] );
					array_push ( $totalcol1, ' null as ' . $itemsplit1 [1] );
					$str = strtolower ( str_replace ( ' ', '', trim ( $itemsplit [0] ) ) );
					if (substr ( $str, 0, 4 ) != 'sum(' && substr ( $str, 0, 6 ) != 'count(')
						array_push ( $groupbycol, $itemsplit [0] );
					$str1 = strtolower ( str_replace ( ' ', '', trim ( $itemsplit1 [0] ) ) );
					if (substr ( $str1, 0, 4 ) != 'sum(' && substr ( $str1, 0, 6 ) != 'count(')
						array_push ( $groupbycol1, $itemsplit1 [0] );
				}
			}
		}
		array_push ( $displaytext, '消费金额' );
		// var_dump($totalcol);
		$totalcol [0] = '\'总计：\' as ' . explode ( ' as ', $totalcol [0] )[1];
		// var_dump($totalcol);
		$totalcolstr = join ( ',', $totalcol );
		$totalcolstr1 = join ( ',', $totalcol1 );
		$sumcolstr = join ( ',', $sumcol );
		$sumcolstr1 = join ( ',', $sumcol1 );
		$sumcolstr2 = join ( ',', $sumcol2 );
		$groupbycolstr = join ( ',', $groupbycol );
		$groupbycolstr1 = join ( ',', $groupbycol1 );
		// echo $sumcolstr;
		$tsql = " select $sumcolstr2 ,sum(getmoney) getmoney
				 from (
					select $sumcolstr ,sum(fCO_GetMoney) getmoney
                        $sql group by $groupbycolstr
                  union all
                  select $sumcolstr1 ,sum(-RechargeMoney) getmoney
                        $msql group by $groupbycolstr1
                        ) zzz
                         group by $sumcolstr2 order by $sumcolstr2";

		 echo $tsql;
		// 处理合计
		$totalsql = " select $totalcolstr ,  sum(getmoney) getmoney
                        from (
						select $sumcolstr ,sum(fCO_FactMoney) getmoney
							$sql group by $groupbycolstr
					  union all
					  select $sumcolstr1 ,sum(-RechargeMoney) getmoney
							$msql group by $groupbycolstr1
                        ) zzz ";
//		echo $totalsql;
		if (isset ( $_GET ['export'] ) && $_GET ['export'] == 'true') {
			$this->exportxlsx ( array (
					0 => $tsql,
					1 => $totalsql 
			), $displaytext, '消费汇总' );
		}
		$stmt = $conn->query ( $tsql );
		$data_list = array ();
		while ( $row = $stmt->fetch ( PDO::FETCH_OBJ ) ) {
			array_push ( $data_list, $row );
		}
		
		// echo $totalsql;
		$totalstmt = $conn->query ( $totalsql );
		while ( $row = $totalstmt->fetch ( PDO::FETCH_OBJ ) ) {
			array_push ( $data_list, $row );
		}
		Tpl::output ( 'data_list', $data_list );
		// --0:期初入库 1:采购入库 2:购进退回 3:盘盈 5:领用 12:盘亏 14:领用退回 50:采购计划
		Tpl::output ( 'page', $page->show () );
		// 处理需要显示的列
		$col = array ();
		foreach ( $sumtype as $i => $v ) {
			if (isset ( $sumtypestr [$v] )) {
				foreach ( $sumtypestr [$v] as $key => $item ) {
					$col [$key] = $item;
				}
			}
		}
		// var_dump($col);
		Tpl::output ( 'displaycol', $displaycol );
		Tpl::output ( 'displaytext', $displaytext );
		Tpl::showpage ( 'member.consume.sum' );
	}
	
	public function rechargesumOp() {
		$conn = require (BASE_DATA_PATH . '/../core/framework/db/mssqlpdo.php');
		if (! isset ( $_GET ['search_type'] )) {
			$_GET ['search_type'] = '0';
		}
		$sqlarray = array (
				'ChargePerson' => 'person.sPerson_Name as "ChargePerson"',
				'type' => ' type.name as "type" ',
				'state' => ' state.name as "state" ',
				'year' => ' year(a.RechargeDate) as "year" ',
				'month' => ' left(convert(varchar,RechargeDate,112),6) as  "month" ',
				'day' => ' convert(varchar,RechargeDate,112) as "day" ',
				'OrgID' => ' org.name as "OrgID" ' 
		);
		// $config = array(0 => array('sumcol' => array('iBuy_Type' => array(name => 'iBuy_Type', 'text' => '单据类型', map => $this->types),
		// 'customname' => array(name => 'customname', 'text' => '供货企业'),
		// 'good' => array('text' => '商品',
		// 'cols' => array(0 => array(name => 'sDrug_TradeName', 'text' => '商品名称')
		// , 1 => array(name => 'sDrug_Spec', 'text' => '规格')
		// , 2 => array(name => 'sDrug_Unit', 'text' => '单位')
		// , 3 => array(name => 'sDrug_Brand', 'text' => '产地厂牌')
		// , 4 => array(name => 'drugcount', 'text' => '数量'))),
		// 'year' => array('text' => '年', name=>'year',uncheck=>'month,day' ),
		// 'month' => array('text' => '月', name=>'month',uncheck=>'year,day'),
		// 'day' => array('text' => '日', name=>'day',uncheck=>'year,month')
		// )));
		$config = array (
				'sumcol' => array (
						'OrgID' => array (
								name => 'OrgID',
								'text' => '机构' ,
								value =>0
						),
						'member' => array (
								'name' =>'member',
								'text' => '会员',
								value =>1,
								'cols' => array (
										0 => array (
												name => 'sMemberID',
												'text' => '会员卡号' 
										),
										1 => array (
												name => 'member_truename',
												'text' => '姓名' 
										),
										2 => array (
												name => 'member_sex',
												'text' => '性别' 
										),
										3 => array (
												name => 'Mobile',
												'text' => '联系电话'
										),
										4 => array (
												name => 'member_Birthday',
												'text' => '生日' 
										)
										
								) 
						),
						'Referrer' => array (
								name => 'Referrer',
								value =>2,
								'text' => '推荐人' 
						),
						'iYear' => array (
								'text' => '年',
								name => 'iYear',
								value =>3,
								uncheck => 'iMonth,dPayDate' 
						),
						'iMonth' => array (
								'text' => '月',
								name => 'iMonth',
								value =>4,
								uncheck => 'iYear,dPayDate' 
						),
						'dPayDate' => array (
								'text' => '日',
								name => 'dPayDate',
								value =>5,
								uncheck => 'iYear,iMonth' 
						) 
				) 
		);
		// $config = array('sumcol' => array('OrgID' => array(name => 'OrgID', 'text' => '机构'),
		// 'ChargePerson' => array(name => 'ChargePerson', 'text' => '收款人'),
		// 'type' => array(name => 'type', 'text' => '类型'),
		// 'state' => array(name => 'state', 'text' => '状态'),
		// 'year' => array('text' => '年', name=>'year',uncheck=>'month,day' ),
		// 'month' => array('text' => '月', name=>'month',uncheck=>'year,day'),
		// 'day' => array('text' => '日', name=>'day',uncheck=>'year,month'),
		// ));
		Tpl::output ( 'config', $config );
		
		// 处理汇总字段
		$sumtype = $_GET ['sumtype'];
		if ($sumtype == null) {
			$sumtype = array (
					0 => "OrgID" 
			);
			$_GET ['sumtype'] = $sumtype;
		}
		$checked = $_GET ['checked'];
		$page = new Page ();
		$page->setEachNum ( 10 );
		$page->setNowPage ( $_REQUEST ["curpage"] );
		$startnum = $page->getEachNum() * ($page->getNowPage() - 1);
		$endnum = $page->getEachNum() * ($page->getNowPage());
		
		if ($_GET ['query_start_time']) {
			$starttime =  $_GET ['query_start_time'] ;
		}
		
		if ($_GET ['query_end_time']) {
			$endtime = $_GET ['query_end_time'];
		}
		
		// 处理树的参数
		if ($_GET ['orgids']) {
			$orgids= implode ( ',', $_GET ['orgids'] );
		}
		
		$search_type = $_GET ['search_type'];
		// echo $search_type;
		$colconfig = $config;
		// var_dump($config[intval($search_type)]);
		$displaycol = array ();
		$displaytext = array ();
		$sumcol = array ();
		$totalcol = array ();
		$groupbycol = array ();
		
		$sumtypeparam = array(0=>'0',1=>'0',2=>'0',3=>'0',4=>'0',5=>'0');
		
		foreach ( $sumtype as $i => $v ) {
			// var_dump($colconfig['sumcol'][$v]);
			if (isset ( $colconfig ['sqlwher'] )) {
				$sql = $sql . $colconfig ['sqlwher'];
			}
			if (isset ( $colconfig ['sumcol'] [$v] )) {
				if (isset ( $colconfig ['sumcol'] [$v] ['cols'] )) {
					$sumtypeparam[$colconfig ['sumcol'] [$v]['value']] ='1';
					foreach ( $colconfig ['sumcol'] [$v] ['cols'] as $item ) {
						// echo $item['name'] . '<br>';
						
						array_push ( $sumcol, $sqlarray [$item ['name']] );
						array_push ( $displaycol, $item ['name'] );
						array_push ( $displaytext, $item ['text'] );
						$itemsplit = explode ( ' as ', $sqlarray [$item ['name']] );
						array_push ( $totalcol, ' null as ' . $itemsplit [1] );
						$str = strtolower ( str_replace ( ' ', '', trim ( $itemsplit [0] ) ) );
						if (substr ( $str, 0, 4 ) != 'sum(' && substr ( $str, 0, 6 ) != 'count(')
							array_push ( $groupbycol, $itemsplit [0] );
					}
				} else {
					$item = $colconfig ['sumcol'] [$v];
					$sumtypeparam[$item ['value']] ='1';
					array_push ( $sumcol, $sqlarray [$item ['name']] );
					array_push ( $displaycol, $item ['name'] );
					array_push ( $displaytext, $item ['text'] );
					$itemsplit = explode ( ' as ', $sqlarray [$item ['name']] );
					array_push ( $totalcol, ' null as ' . $itemsplit [1] );
					$str = strtolower ( str_replace ( ' ', '', trim ( $itemsplit [0] ) ) );
					if (substr ( $str, 0, 4 ) != 'sum(' && substr ( $str, 0, 6 ) != 'count(')
						array_push ( $groupbycol, $itemsplit [0] );
				}
			}
		}
		$param1 = implode('', $sumtypeparam);
		array_push ( $displaytext, '充值下账信息' );
		array_push ( $displaytext, '诊疗购买信息' );
		
		
		$tsql = "SET NOCOUNT ON; Exec pFMemberPayStat '$param1','$orgids','$starttime','$endtime','','$startnum','$endnum';SET NOCOUNT off; ";
		// echo $tsql;
		$stmt = $conn->prepare ( $tsql );
		$stmt->execute ();
		$data_list = array ();
		while ( $row = $stmt->fetchObject () ) {
			array_push ( $data_list, $row );
		}
		
		Tpl::output ( 'data_list', $data_list );
		Tpl::output('page', $page->show());
		// // var_dump($totalcol);
		// $totalcol[0] = '\'总计：\' as ' . explode(' as ', $totalcol[0])[1];
		// // var_dump($totalcol);
		// $totalcolstr = join(',', $totalcol);
		// $sumcolstr = join(',', $sumcol);
		// $groupbycolstr = join(',', $groupbycol);
		// // echo $sumcolstr;
		// $tsql = " select $sumcolstr , sum(RechargeMoney) rechargemMoney,sum(GiveMoney) givemoney , sum(RechargeMoney+GiveMoney) allmoney
		// $sql group by $groupbycolstr order by $groupbycolstr ";
		// //处理合计
		// $totalsql = " select $totalcolstr , count(1) cliniccount
		// $sql ";
		// if(isset($_GET['export']) && $_GET['export']=='true'){
		// $this->exportxlsx(array(0=>$tsql,1=>$totalsql),$displaytext,'充值下账汇总');
		// }
		// $stmt = $conn->query($tsql);
		// $data_list = array();
		// while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
		// array_push($data_list, $row);
		// }
		
		// // echo $totalsql;
		// $totalstmt = $conn->query($totalsql);
		// while ($row = $totalstmt->fetch(PDO::FETCH_OBJ)) {
		// array_push($data_list, $row);
		// }
		// Tpl::output('data_list', $data_list);
		// //--0:期初入库 1:采购入库 2:购进退回 3:盘盈 5:领用 12:盘亏 14:领用退回 50:采购计划
		// Tpl::output('page', $page->show());
		// //处理需要显示的列
		// $col = array();
		// foreach ($sumtype as $i => $v) {
		// if (isset($sumtypestr[$v])) {
		// foreach ($sumtypestr[$v] as $key => $item) {
		// $col[$key] = $item;
		// }
		// }
		// }
		// var_dump($col);
		Tpl::output ( 'displaycol', $displaycol );
		Tpl::output ( 'displaytext', $displaytext );
		Tpl::showpage ( 'member.recharge.sum' );
	}

	public function psresetOp()
	{
		try {
			$conn = require(BASE_DATA_PATH . '/../core/framework/db/mssqlpdo.php');
			$cardid = $_REQUEST['cardid'];
			$sql = " update shopnc_member set  member_passwd = '@@@@@@' where member_id = ? ";
			$stmt = $conn->prepare($sql);
			$stmt->execute(array($cardid));

			echo json_encode(array('success' => true, 'msg' => '重置成功!'));
		} catch (Exception $e) {
			echo json_encode(array('success' => false, 'msg' => '异常!'.$e->getMessage()));
		}
		exit;
	}

	public function membermoneydetailOp(){
		try {
			$conn = require(BASE_DATA_PATH . '/../core/framework/db/mssqlpdo.php');
			$cardid = $_REQUEST['cardid1'];
			$datestart = date('2000-1-1',time());
			$dateend = date('2030-12-31',time());
			$sql = " SET NOCOUNT ON; exec pFMemberAccount '$cardid', '$datestart','$dateend' ;SET NOCOUNT off;";
			$stmt = $conn->prepare($sql);

			$stmt->execute(array($cardid));
			$data_list = array ();
			while ( $row = $stmt->fetchObject () ) {
				array_push ( $data_list, $row );
			}
			echo json_encode(array('success' => true, 'msg' => '查询成功!' ,'data'=>$data_list ,'sql'=>$sql));
		} catch (Exception $e) {
			echo json_encode(array('success' => false, 'msg' => '异常!'.$e->getMessage()));
		}
		exit;
	}
}
