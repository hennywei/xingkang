<?php
/**
 * 统计管理
 *
 * @copyright  Copyright (c) 2014-2020 SZGR Inc. (http://www.szgr.com.cn)
 * @license    http://www.szgr.com.cn
 * @link       http://www.szgr.com.cn
 * @since      File available since Release v1.1
 */
defined('InShopNC') or exit('Access Invalid!');
require(BASE_DATA_PATH . '/../core/framework/db/mssql.php');

class communityControl extends SystemControl
{

    public function __construct()
    {
        parent::__construct();
        $conn = require(BASE_DATA_PATH . '/../core/framework/db/mssqlpdo.php');
        $treesql = 'select  b.id , b.name,b.districtnumber,b.parentid pId from map_org_wechat a, Organization b where a.orgid = b.id ';
        $treestmt = $conn->query($treesql);
        $this->treedata_list = array();
        while ($row = $treestmt->fetch(PDO::FETCH_OBJ)) {
            array_push($this->treedata_list, $row);
        }
        Tpl::output('treelist', $this->treedata_list);
        $this->getTreeData();
        $stmt = $conn->query(' select * from Center_codes  where type=\'iCO_Type\' order by code ');
        $this->types = array();
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            array_push($this->types, $row);
        }
//        $this->types = array(0 => '期初入库', 1 => '采购入库', 2 => '购进退回', 3 => '盘盈', 5 => '领用', 12 => '盘亏', 14 => '领用退回', 50 => '采购计划',);
        Tpl::output('types', $this->types);
        $this->goodtype = array(0 => '药品', 1 => '卫生用品', 2 => '诊疗项目', 3 => '特殊材料');
        Tpl::output('goodtype', $this->goodtype);

        $stmt = $conn->query(' select distinct orgid from map_org_wechat order by orgid ');
        $this->orgidarray = array();
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            array_push($this->orgidarray, $row[0]);
        }

    }

    /**
     * 新增会员
     */
    public function prescriptiondetailOp()
    {
        $conn = require(BASE_DATA_PATH . '/../core/framework/db/mssqlpdo.php');
        //处理数据
        $page = new Page();
        $page->setEachNum(10);
        $page->setNowPage($_REQUEST["curpage"]);
        $startnum = $page->getEachNum() * ($page->getNowPage() - 1);
        $endnum = $page->getEachNum() * ($page->getNowPage());
        $sql = 'from Center_ClinicLog a
            where 1=1 ';
//        if (!isset($_GET['search_type'])) {
//            $_GET['search_type'] = '1';
//        }
//        if (gettype($_GET['search_type']) == 'string' && intval($_GET['search_type']) >= 0) {
//            $sql = $sql . ' and  a.iBuy_Type = \'' . $_GET['search_type'] . '\'';
//        }

        if ($_GET['query_start_time']) {
            $sql = $sql . ' and a.ClinicDate >=\'' . $_GET['query_start_time'] . '\'';
        }

        if ($_GET['query_end_time']) {
            $sql = $sql . ' and a.ClinicDate < dateadd(day,1,\'' . $_GET['query_end_time'] . '\')';
        }

        if ($_GET['orgids']) {
            $sql = $sql . ' and a.OrgID in ( ' . implode(',', $_GET['orgids']) . ')';
        }

        //处理树的参数
        $checkednode = $_GET['checkednode'];
        if ($checkednode && isset($checkednode) && count($checkednode) > 0) {
            $sql = $sql . " and a.orgid  in ($checkednode) ";
        }

        $countsql = " select count(*)  $sql ";
        $stmt = $conn->query($countsql);
        $total = $stmt->fetch(PDO::FETCH_NUM);
        $page->setTotalNum($total[0]);
        $tsql = "SELECT * FROM  ( SELECT  * FROM (SELECT TOP $endnum row_number() over( order by  a.ClinicDate desc) rownum,
                        a.sSickName,
                        a.sSex,
                        a.sShowAge,
                        a.ClinicDate,
                        a.Diagnosis,
                        a.AllergyHistory,
                        a.Signs ,
                        a.Opinion,
                        a.Section,
                        a.Doctor,
                        a.sPhone,
                        a.sAddress,
                        a.sLinkman,
                        a.sIDCard,
                        a.sFileNo,
                        a.sHealthCardID
                        $sql order by  a.ClinicDate desc)zzzz where rownum>$startnum )zzzzz order by rownum";
//        echo $sql;
        $stmt = $conn->query($tsql);
        $data_list = array();
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            array_push($data_list, $row);
        }

//        $totalsql = " select '总计：' as iBuy_TicketID,
//                        null as sBuy_A6,
//                        null as iBuy_ID,
//                        null as dBuy_Date,
//                        null as iDrug_RecType,
//                        null as iBuy_Type,
//                        null as OrgId,
//                        null as SaleOrgID,
//                        null as iDrug_ID,
//                        null as fBuy_FactNum,
//                        null as sBuy_DrugUnit,
//                        sum(fBuy_TaxMoney) as fBuy_TaxMoney,
//                        sum(fBuy_RetailMoney) as fBuy_RetailMoney,
//                        sum(fBuy_RetailMoney)-sum(fBuy_TaxMoney) as diffmoney
//                        $sql  ";
////        echo $sql;
//        $totalstmt = $conn->query($totalsql);
//        while ($row = $totalstmt->fetch(PDO::FETCH_OBJ)) {
//            array_push($data_list, $row);
//        }
        Tpl::output('data_list', $data_list);
        //--0:期初入库 1:采购入库 2:购进退回 3:盘盈 5:领用 12:盘亏 14:领用退回 50:采购计划
        Tpl::output('page', $page->show());
        Tpl::showpage('community.prescription.detail');
    }

    public function incomedetailOp()
    {
        $conn = require(BASE_DATA_PATH . '/../core/framework/db/mssqlpdo.php');
        //处理数据
        $page = new Page();
        $page->setEachNum(10);
        $page->setNowPage($_REQUEST["curpage"]);
        $startnum = $page->getEachNum() * ($page->getNowPage() - 1);
        $endnum = $page->getEachNum() * ($page->getNowPage());
        $sql = 'from Center_CheckOut a  , Center_codes ico, Center_codes gather,Center_codes state,Center_codes tag,
            Center_Person person , Organization org
          where a.iCO_Type = ico.code and ico.type=\'iCO_Type\'
           and  a.iCO_GatherType = gather.code and gather.type=\'iCO_GatherType\'
           and  a.iCO_State = state.code and state.type=\'iCO_State\'
           and  a.iCO_Tag = tag.code and tag.type=\'iCO_Tag\'
           and a.orgid = org.id
           and a.iCO_MakePerson = person.iPerson_ID ';
//        if (!isset($_GET['search_type'])) {
//            $_GET['search_type'] = '1';
//        }
//        if (gettype($_GET['search_type']) == 'string' && intval($_GET['search_type']) >= 0) {
//            $sql = $sql . ' and  a.iBuy_Type = \'' . $_GET['search_type'] . '\'';
//        }

        if ($_GET['query_start_time']) {
            $sql = $sql . ' and a.dCO_Date >=\'' . $_GET['query_start_time'] . '\'';
        }

        if ($_GET['query_end_time']) {
            $sql = $sql . ' and a.dCO_Date < dateadd(day,1,\'' . $_GET['query_end_time'] . '\')';
        }

        if ($_GET['orgids']) {
            $sql = $sql . ' and a.OrgID in ( ' . implode(',', $_GET['orgids']) . ')';
        }
        if (isset($_GET['types']) and $_GET['types'] != '') {
            $sql = $sql . ' and a.iCO_Type  =  ' . $_GET['types'] . '';
        }
        //处理树的参数
        $checkednode = $_GET['checkednode'];
        if ($checkednode && isset($checkednode) && count($checkednode) > 0) {
            $sql = $sql . " and a.orgid  in ($checkednode) ";
        }

        $moneycol = array('fCO_Foregift','fCO_Balance','fCO_FactMoney','fCO_IncomeMoney','fCO_GetMoney','fCO_PayMoney',
            'fCO_Card','fCO_Cash','fCO_StartMoney','fCO_Medicare','fCO_SelfCost','fCO_SelfPay','fCO_HospitalSubsidy',
            'fCO_BeforeSubsidy','fOweMoney','fCO_PosPay','fRecharge','fConsume','fRechargeBalance','fConsumeBalance',
            'fGive','fCanConsume');
        Tpl::output('moneycol', $moneycol);
        $countsql = " select count(*)  $sql ";

        $stmt = $conn->query($countsql);
        $total = $stmt->fetch(PDO::FETCH_NUM);
        $page->setTotalNum($total[0]);
        $tsql = "SELECT * FROM  ( SELECT  * FROM (SELECT TOP $endnum row_number() over( order by  a.dCO_Date desc) rownum,
                        ico.name as 'iCO_Type',
                        person.sPerson_Name 'iCO_MakePerson',
                        a.dCO_Date,
                        a.dCO_MakeDate,
                        a.fCO_Foregift,
                        a.fCO_Balance,
                        a.fCO_FactMoney,
                        a.fCO_IncomeMoney ,
                        a.fCO_GetMoney,
                        a.fCO_PayMoney,
                        gather.name as 'iCO_GatherType',
                        state.name as 'iCO_State',
                        a.sCO_CapitalMoney,
                        a.sCO_Remark,
                        tag.name as 'iCO_Tag',
                        a.fCO_Card,
                        a.fCO_Cash,
                        a.fCO_StartMoney,
                        a.fCO_Medicare,
                        a.fCO_SelfCost,
                        a.fCO_SelfPay,
                        a.fCO_HospitalSubsidy,
                        a.sCO_SubsidyReason,
                        a.fCO_BeforeSubsidy,
                        a.fOweMoney,
                        a.fCO_PosPay,
                        a.sMemberID,
                        a.sMemberAssistantID,
                        a.fRecharge,
                        a.fConsume,
                        a.fRechargeBalance,
                        a.fConsumeBalance,
                        a.fScale,
                        a.fScaleBalance,
                        a.fScaleToMoney,
                        a.fGive,
                        a.fCanConsume,
                        a.fCanScale,
                        a.fCanGive,
                        a.fAddScale,
                        org.name as 'OrgID'
                        $sql order by  a.dCO_Date desc)zzzz where rownum>$startnum )zzzzz order by rownum";
        $stmt = $conn->query($tsql);
        $data_list = array();
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            array_push($data_list, $row);
        }
        Tpl::output('data_list', $data_list);
        Tpl::output('page', $page->show());
        Tpl::showpage('community.income.detail');
    }


    public function ajaxOp()
    {
        //spotcheck_spot
        try {
            $conn = require(BASE_DATA_PATH . '/../core/framework/db/mssqlpdo.php');
            $id = $_REQUEST['id'];
            $spotid = $_REQUEST['spotid'];
            $spotdate = $_REQUEST['spotdate'];
            $result = $_REQUEST['spotresult'] == null ? "" : $_REQUEST['spotresult'];
            $reason = $_REQUEST['reason'] == null ? "" : $_REQUEST['reason'];
            $sql = " insert into spotcheck_spot (spotid,spotdate,result,reason,inputdate) values('$spotid','$spotdate','$result','$reason',getdate())";
            $conn->exec($sql);
            echo json_encode(array('success' => true, 'msg' => '保存成功!'));
        } catch (Exception $e) {
            echo json_encode(array('success' => false, 'msg' => '异常!' . $e->getMessage()));
        }
        exit;
    }

    private function getTreeData()
    {
        $conn = require(BASE_DATA_PATH . '/../core/framework/db/mssqlpdo.php');

        //查询机构树的类型
        $treesql = 'select  b.id , b.name,b.districtnumber,b.parentid pId from map_org_wechat a, Organization b where a.orgid = b.id ';
        $treestmt = $conn->query($treesql);
        $treedata_list = array();

        while ($row = $treestmt->fetch(PDO::FETCH_OBJ)) {
            array_push($treedata_list, $row);
        }
        $idmap = Array();
        //处理树选择节点
        $checkednode = $_GET['checkednode'];
        $checkednodearray = array();
        if (isset($checkednode)) {
            $checkednodearray = explode(',', $checkednode);
        }
        //处理父节点
        $root = array(id => -1, name => "全部", open => true, halfCheck => false);
        if ($checkednode && isset($checkednode) && count($checkednode) > 0) {
            $root['checked'] = true;
        }
        array_push($treedata_list, (object)$root);
        for ($i = 0; $i < count($treedata_list); $i++) {
            $item = $treedata_list[$i];
            $idmap[$item->id] = $item->id;
        }
        for ($i = 0; $i < count($treedata_list); $i++) {
            $item = $treedata_list[$i];
            if ($item->id >= 0) {
                $item->id = -(1000 + $item->id);
            }
            if (!isset($idmap[$item->pid])) {
                $item->pId = -1;
            }
        }

        foreach ($treedata_list as &$v) {
            if (in_array($v->id, $checkednodearray)) {
                $v->checked = true;
            }
        }

        Tpl::output('treedata', $treedata_list);
    }

    public function prescriptionsumOp()
    {
        $conn = require(BASE_DATA_PATH . '/../core/framework/db/mssqlpdo.php');
        if (!isset($_GET['search_type'])) {
            $_GET['search_type'] = '0';
        }
        $sqlarray = array('Section' => 'a.Section as "Section"',
            'Doctor' => ' a.Doctor as "Doctor" ',
            'year' => ' year(a.ClinicDate) as "year" ',
            'month' => ' month(a.ClinicDate) as  "month" ',
            'day' => ' day(a.ClinicDate) as "day" ',
            'OrgID' => ' org.name as "OrgID" '
        );
        $config = array('sumcol' => array('OrgID' => array(name => 'OrgID', 'text' => '机构', map => $this->types),
            'Section' => array(name => 'Section', 'text' => '科室'),
            'Doctor' => array(name => 'Doctor', 'text' => '医生'),
            'year' => array('text' => '年', name=>'year' ),
            'month' => array('text' => '月', name=>'month'),
            'day' => array('text' => '日', name=>'day'),
        ));
        Tpl::output('config', $config);

        //处理汇总字段
        $sumtype = $_GET['sumtype'];
        if ($sumtype == null) {
            $sumtype = array(0 => "OrgID");
            $_GET['sumtype'] = $sumtype;
        }
        $checked = $_GET['checked'];
        $page = new Page();
        $page->setEachNum(10);
        $page->setNowPage($_REQUEST["curpage"]);
        $sql = 'from Center_ClinicLog a  , Organization org  where  a.orgid = org.id ';

        if ($_GET['query_start_time']) {
            $sql = $sql . ' and a.ClinicDate >=\'' . $_GET['query_start_time'] . '\'';
        }

        if ($_GET['query_end_time']) {
            $sql = $sql . ' and a.ClinicDate < dateadd(day,1,\'' . $_GET['query_end_time'] . '\')';
        }

        //处理树的参数
        if ($_GET['orgids']) {
            $sql = $sql . ' and a.OrgID in ( ' . implode(',', $_GET['orgids']) . ')';
        }

        $search_type = $_GET['search_type'];
//        echo $search_type;
        $colconfig = $config;
//        var_dump($config[intval($search_type)]);
        $displaycol = array();
        $displaytext = array();
        $sumcol = array();
        $totalcol = array();
        $groupbycol = array();
        foreach ($sumtype as $i => $v) {
//            var_dump($colconfig['sumcol'][$v]);
            if(isset($colconfig['sqlwher'])){
                $sql = $sql . $colconfig['sqlwher'];
            }
            if (isset($colconfig['sumcol'][$v])) {
                if (isset($colconfig['sumcol'][$v]['cols'])) {
                    foreach ($colconfig['sumcol'][$v]['cols'] as $item) {
//                        echo $item['name'] . '<br>';
                        array_push($sumcol, $sqlarray[$item['name']]);
                        array_push($displaycol, $item['name']);
                        array_push($displaytext, $item['text']);
                        $itemsplit = explode(' as ', $sqlarray[$item['name']]);
                        array_push($totalcol, ' null as ' . $itemsplit[1]);
                        $str = strtolower(str_replace(' ', '', trim($itemsplit[0])));
                        if (substr($str, 0, 4) != 'sum(' && substr($str, 0, 6) != 'count(')
                            array_push($groupbycol, $itemsplit[0]);
                    }
                } else {
                    $item = $colconfig['sumcol'][$v];
                    array_push($sumcol, $sqlarray[$item['name']]);
                    array_push($displaycol, $item['name']);
                    array_push($displaytext, $item['text']);
                    $itemsplit = explode(' as ', $sqlarray[$item['name']]);
                    array_push($totalcol, ' null as ' . $itemsplit[1]);
                    $str = strtolower(str_replace(' ', '', trim($itemsplit[0])));
                    if (substr($str, 0, 4) != 'sum(' && substr($str, 0, 6) != 'count(')
                        array_push($groupbycol, $itemsplit[0]);
                }
            }
        }
//        var_dump($totalcol);
        $totalcol[0] = '\'总计：\' as ' . explode(' as ', $totalcol[0])[1];
//        var_dump($totalcol);
        $totalcolstr = join(',', $totalcol);
        $sumcolstr = join(',', $sumcol);
        $groupbycolstr = join(',', $groupbycol);
//        echo $sumcolstr;
        $tsql = " select $sumcolstr , count(1) cliniccount
                        $sql group by $groupbycolstr order by $groupbycolstr ";
//        echo $tsql;
        $stmt = $conn->query($tsql);
        $data_list = array();
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            array_push($data_list, $row);
        }
        //处理合计
        $totalsql = " select $totalcolstr , count(1) cliniccount
                        $sql ";
//        echo $totalsql;
        $totalstmt = $conn->query($totalsql);
        while ($row = $totalstmt->fetch(PDO::FETCH_OBJ)) {
            array_push($data_list, $row);
        }
        Tpl::output('data_list', $data_list);
        //--0:期初入库 1:采购入库 2:购进退回 3:盘盈 5:领用 12:盘亏 14:领用退回 50:采购计划
        Tpl::output('page', $page->show());
        //处理需要显示的列
        $col = array();
        foreach ($sumtype as $i => $v) {
            if (isset($sumtypestr[$v])) {
                foreach ($sumtypestr[$v] as $key => $item) {
                    $col[$key] = $item;
                }
            }
        }
//        var_dump($col);
        Tpl::output('displaycol', $displaycol);
        Tpl::output('displaytext', $displaytext);
        Tpl::showpage('community.prescription.sum');
    }

    public function incomesumOp()
    {
        $conn = require(BASE_DATA_PATH . '/../core/framework/db/mssqlpdo.php');
        if (!isset($_GET['search_type'])) {
            $_GET['search_type'] = '0';
        }
        $sqlarray = array('iCO_Type' => 'ico.name as "iCO_Type"',
            'iCO_MakePerson' => ' person.sPerson_Name as "iCO_MakePerson" ',
            'iCO_GatherType' => ' gather.name as "iCO_GatherType" ',
            'year' => ' year(a.dCO_Date) as "year" ',
            'month' => ' month(a.dCO_Date) as  "month" ',
            'day' => ' day(a.dCO_Date) as "day" ',
            'OrgID' => ' org.name as "OrgID" '
        );
        $config = array('sumcol' => array('iCO_Type' => array(name => 'iCO_Type', 'text' => '类型', map => $this->types),
            'iCO_MakePerson' => array(name => 'iCO_MakePerson', 'text' => '收费员'),
            'iCO_GatherType' => array(name => 'iCO_GatherType', 'text' => '医保类型'),
            'OrgID' => array(name => 'OrgID', 'text' => '结算机构'),
            'year' => array('text' => '年', name=>'year' ),
            'month' => array('text' => '月', name=>'month'),
            'day' => array('text' => '日', name=>'day'),
        ));
        Tpl::output('config', $config);

        //处理汇总字段
        $sumtype = $_GET['sumtype'];
        if ($sumtype == null) {
            $sumtype = array(0 => "iCO_Type");
            $_GET['sumtype'] = $sumtype;
        }
        $checked = $_GET['checked'];
        $page = new Page();
        $page->setEachNum(10);
        $page->setNowPage($_REQUEST["curpage"]);
        $sql = 'from Center_CheckOut a  , Center_codes ico, Center_codes gather,Center_codes state,Center_codes tag,
            Center_Person person , Organization org
          where a.iCO_Type = ico.code and ico.type=\'iCO_Type\'
           and  a.iCO_GatherType = gather.code and gather.type=\'iCO_GatherType\'
           and  a.iCO_State = state.code and state.type=\'iCO_State\'
           and  a.iCO_Tag = tag.code and tag.type=\'iCO_Tag\'
           and a.orgid = org.id
           and a.iCO_MakePerson = person.iPerson_ID ';

        if ($_GET['query_start_time']) {
            $sql = $sql . ' and a.dCO_Date >=\'' . $_GET['query_start_time'] . '\'';
        }

        if ($_GET['query_end_time']) {
            $sql = $sql . ' and a.dCO_Date < dateadd(day,1,\'' . $_GET['query_end_time'] . '\')';
        }

        //处理树的参数
        if ($_GET['orgids']) {
            $sql = $sql . ' and a.OrgID in ( ' . implode(',', $_GET['orgids']) . ')';
        }

        $search_type = $_GET['search_type'];
//        echo $search_type;
        $colconfig = $config;
//        var_dump($config[intval($search_type)]);
        $displaycol = array();
        $displaytext = array();
        $sumcol = array();
        $totalcol = array();
        $groupbycol = array();
        foreach ($sumtype as $i => $v) {
//            var_dump($colconfig['sumcol'][$v]);
            if(isset($colconfig['sqlwher'])){
                $sql = $sql . $colconfig['sqlwher'];
            }
            if (isset($colconfig['sumcol'][$v])) {
                if (isset($colconfig['sumcol'][$v]['cols'])) {
                    foreach ($colconfig['sumcol'][$v]['cols'] as $item) {
//                        echo $item['name'] . '<br>';
                        array_push($sumcol, $sqlarray[$item['name']]);
                        array_push($displaycol, $item['name']);
                        array_push($displaytext, $item['text']);
                        $itemsplit = explode(' as ', $sqlarray[$item['name']]);
                        array_push($totalcol, ' null as ' . $itemsplit[1]);
                        $str = strtolower(str_replace(' ', '', trim($itemsplit[0])));
                        if (substr($str, 0, 4) != 'sum(' && substr($str, 0, 6) != 'count(')
                            array_push($groupbycol, $itemsplit[0]);
                    }
                } else {
                    $item = $colconfig['sumcol'][$v];
                    array_push($sumcol, $sqlarray[$item['name']]);
                    array_push($displaycol, $item['name']);
                    array_push($displaytext, $item['text']);
                    $itemsplit = explode(' as ', $sqlarray[$item['name']]);
                    array_push($totalcol, ' null as ' . $itemsplit[1]);
                    $str = strtolower(str_replace(' ', '', trim($itemsplit[0])));
                    if (substr($str, 0, 4) != 'sum(' && substr($str, 0, 6) != 'count(')
                        array_push($groupbycol, $itemsplit[0]);
                }
            }
        }
//        var_dump($totalcol);
        $totalcol[0] = '\'总计：\' as ' . explode(' as ', $totalcol[0])[1];
//        var_dump($totalcol);
        $totalcolstr = join(',', $totalcol);
        $sumcolstr = join(',', $sumcol);
        $groupbycolstr = join(',', $groupbycol);
//        echo $sumcolstr;
        $tsql = " select $sumcolstr , sum(fCO_PayMoney) paymoney, sum(fCO_GetMoney) getmoney
                        $sql group by $groupbycolstr order by $groupbycolstr ";
//        echo $tsql;
        $stmt = $conn->query($tsql);
        $data_list = array();
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            array_push($data_list, $row);
        }
        //处理合计
        $totalsql = " select $totalcolstr , sum(fCO_PayMoney) paymoney, sum(fCO_GetMoney) getmoney
                        $sql ";
//        echo $totalsql;
        $totalstmt = $conn->query($totalsql);
        while ($row = $totalstmt->fetch(PDO::FETCH_OBJ)) {
            array_push($data_list, $row);
        }
        Tpl::output('data_list', $data_list);
        //--0:期初入库 1:采购入库 2:购进退回 3:盘盈 5:领用 12:盘亏 14:领用退回 50:采购计划
        Tpl::output('page', $page->show());
        //处理需要显示的列
        $col = array();
        foreach ($sumtype as $i => $v) {
            if (isset($sumtypestr[$v])) {
                foreach ($sumtypestr[$v] as $key => $item) {
                    $col[$key] = $item;
                }
            }
        }
//        var_dump($col);
        Tpl::output('displaycol', $displaycol);
        Tpl::output('displaytext', $displaytext);
        Tpl::showpage('community.income.sum');
    }

}
