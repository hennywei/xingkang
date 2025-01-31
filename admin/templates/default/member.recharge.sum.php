<?php defined('InShopNC') or exit('Access Invalid!'); ?>
<style>
    .datatable {
        overflow:auto;
        width: 100%;
        right: 0;
    }
    .datatable th, .datatable td {
        border: solid 1px #DEEFFB;
    }

    .typeselect {
        display: none;
    }

    .typeselect + label {
        width: 90%;
        height: 30px;
        line-height: 30px;
        margin: 1px auto;
        border: 1px solid #DEEFFB;
        border-radius: 5px;
        display: block;
        text-align: center;
    }

    .typeselect:checked + label {
        background-color: #DEEFFB;
    }

    .leftdiv {
        position: absolute;
        left: 0;
        width: 10%;
        top: 0;
        bottom: 0;
        border-right: 1px solid #fff;
        padding-top: 7px;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>充值下账汇总</h3>
        </div>
    </div>
    <div class="fixed-empty"></div>

    <form method="get" name="formSearch" id="formSearch">
        <input type="hidden" value="member" name="act">
        <input type="hidden" value="rechargesum" name="op">
        <input type="hidden" id ='export' name="export" value="false">
        <input type="hidden" name="search_type" id="search_type" value="<?php echo $_GET['search_type']?>"/>
        <input type="hidden" name="checked" id="checked" value="<?php echo $_GET['checked']?>"/>
        <table class="tb-type1 noborder search">
            <tbody>
            <tr>
                <th><label>选择机构</label></th>
                <td colspan="1"><select name="orgids[]" id="orgids" class="orgSelect" multiple>
                        <?php
                        $orgids = $_GET['orgids'];
                        if (!isset($orgids)) {
                            $orgids = array();
                        }
                        foreach ($output['treelist'] as $k => $v) {
                            ?>
                            <option value="<?php echo $v->id; ?>"
                                    <?php if (in_array($v->id, $orgids)){ ?>selected<?php } ?>><?php echo $v->name; ?></option>
                        <?php } ?>
                    </select></td>
                </td>
                <th><label for="query_start_time">日期范围</label></th>
                <td><input class="txt date" type="text" value="<?php echo $_GET['query_start_time']; ?>"
                           id="query_start_time" name="query_start_time">
                    <input class="txt date" type="text" value="<?php echo $_GET['query_end_time']; ?>" id="query_end_time"
                           name="query_end_time"/></td>
                <th><label>汇总类型</label></th>
                <td colspan="1" id="sumtypetr">
                    <?php foreach ($output['config']['sumcol'] as $k => $v) { ?>
                        <input type='checkbox' name='sumtype[]'  id='sumtype_<?php echo $v['name']; ?>' <?php if(in_array( $v['name'],$_GET['sumtype'])) echo 'checked'; ?>
                               value='<?php echo $v['name']; ?>' onclick="sumuncheck('sumtype_','<?php echo $v['uncheck']; ?>')">

                        <label for='sumtype_<?php echo $v['name']; ?>'><?php echo $v['text']; ?></label>
                    <?php } ?>
                </td>
                <td><a href="javascript:void(0);" id="ncsubmit" class="btn-search "
                       title="<?php echo $lang['nc_query']; ?>">&nbsp;</a>
<!--                    <a href="javascript:void(0);" id="ncexport" class="btn-export " title="导出"></a>-->
                </td>
<!--                <td><a href="javascript:void(0);" id="ncexport" class="btn-export "-->
<!--                       title="导出"></a>-->
<!--                </td>-->
            </tr>
            </tbody>
        </table>
    </form>
    <table class="table tb-type2 " id="prompt">
        <tbody>
        <tr class="space odd">
            <th colspan="12">
                <div class="title">
                    <h5><?php echo $lang['nc_prompts']; ?></h5>
                    <span class="arrow"></span></div>
            </th>
        </tr>
        </tbody>
    </table>
    <form method="post" id="form_member" style='position: relative;'>
        <input type="hidden" name="form_submit" value="ok"/>
        <table class="table tb-type2 nobdb datatable">
            <thead>
            <tr class="thead">
                <th class="align-center" rowspan="2">序号</th>
                <?php foreach ($output['displaytext'] as $k => $v) {
                    ?>
                    <th class="align-center"  rowspan="2"  ><?php echo $v?></th>
                <?php  }?>
                <th class="align-center" rowspan="2">普卡消费金额</th>
                <th class="align-center" colspan="5">充值下账信息</th>
                <th class="align-center" colspan="5">诊疗购买信息</th>
            </tr>
            <tr>
                <th class="align-center">期初预存</th>
                <th class="align-center">日常充值</th>
                <th class="align-center">日常下账</th>
                <th class="align-center">赠送金额</th>
                <th class="align-center">赠送下账</th>
                <th class="align-center">预存下账</th>
                <th class="align-center">赠送下账</th>
                <th class="align-center">积分下账</th>
                <th class="align-center">扣减积分</th>
                <th class="align-center">赠送积分</th>
            </tr>
            <tbody>
            <?php if (!empty($output['data_list']) && is_array($output['data_list'])) { ?>
                <?php foreach ($output['data_list'] as $k => $v) { ?>
                    <tr class="hover member">
                        <td class=" align-center">
                            <?php echo $k+1?>
                        </td>
                        <?php foreach ($output['displaycol'] as $key => $item) {
                            ?>
                            <th class="align-left"><?php if(substr($item,-5) == 'count')  echo number_format($v->$item,0); else if(substr($item,-3) == 'day')  echo substr($v->$item,0,10); else echo $v->$item;?></th>
                        <?php  }?>
                        <td class=" align-right">
                            <?php echo number_format($v->pkmoney,2); ?>
                        </td>
                        <td class=" align-right">
                            <?php echo number_format($v->fRechargeInit,2); ?>
                        </td>
                        <td class=" align-right">
                            <?php echo number_format($v->fRechargeAdd,2); ?>
                        </td>
                        <td class=" align-right">
                            <?php echo number_format($v->fRechargeBuy,2); ?>
                        </td>
                        <td class=" align-right">
                            <?php echo number_format($v->GiveMoney,2); ?>
                        </td>
                        <td class=" align-right">
                            <?php echo number_format($v->GiveSaleMoney,2); ?>
                        </td>
                        <td class=" align-right">
                            <?php echo number_format($v->fRecharge,2); ?>
                        </td>
                        <td class=" align-right">
                            <?php echo number_format($v->fConsume,2); ?>
                        </td>
                        <td class=" align-right">
                            <?php echo number_format($v->fScaleToMoney,2); ?>
                        </td>
                        <td class=" align-right">
                            <?php echo number_format($v->fScale,2); ?>
                        </td>
                        <td class=" align-right">
                            <?php echo number_format($v->fAddScale,2); ?>
                        </td>

                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr class="no_data">
                    <td colspan="11"><?php echo $lang['nc_no_record'] ?></td>
                </tr>
            <?php } ?>
            </tbody>
            <tfoot class="tfoot">
            </tfoot>
        </table>
    </form>
</div>

<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL; ?>/js/jquery-ui/jquery.ui.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL; ?>/js/jquery-ui/i18n/zh-CN.js"
        charset="utf-8"></script>
<link rel="stylesheet" type="text/css"
      href="<?php echo RESOURCE_SITE_URL; ?>/js/jquery-ui/themes/smoothness/jquery.ui.css"/>
<link href="<?php echo RESOURCE_SITE_URL; ?>/js/ztree/css/zTreeStyle/zTreeStyle.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo RESOURCE_SITE_URL; ?>/js/multiselect/jquery.multiselect.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL; ?>/js/ztree/js/jquery.ztree.all-3.5.min.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL; ?>/js/multiselect/jquery.multiselect.min.js"></script>
<script type="text/javascript">
    var config = <?php echo json_encode($output[config]);?>;

    var checked = getchecked('<?php echo $_GET['checked'];?>');
    $(function () {
        //生成机构下拉
        function orgtext(n1, n2, list) {
            var texts = [];
            for (var idx in list) {
                texts.push($(list[idx]).attr("title"));
            }
            return texts.join('<br>');
        }

        $("#orgids").multiselect(
            {
                checkAllText: '选择全部',
                uncheckAllText: '清除选择',
                noneSelectedText: '未选择',
                selectedText: orgtext
            }
        );

        //生成日期
        $('input.date').datepicker({dateFormat: 'yy-mm-dd'});
        $('#ncsubmit').click(function () {
            $("#export").val('false');
            var sumtypes =$(":checkbox[name='sumtype[]'][checked]");
            if(sumtypes.length<=0){
                $("#sumtype_good").attr("checked", true);
                sumtypes =$(":checkbox[name='sumtype[]'][checked]");
            }
            var search_type_select = $('input[name="search_type_select"]:checked').val();
            $("#search_type").val($('input[name="search_type_select"]:checked').val());
            checked[search_type_select] = [];
            for(var i =0 ;i < sumtypes.length;i++){
                checked[search_type_select].push( $(sumtypes[i]).val());
            }
            $("#checked").val(makechecked(checked));
            $('#formSearch').submit();
        });
        $("#formSearch input").keypress(function(event){
            if(event.keyCode==13){
                $('#ncsubmit').click();
            }
        });
        $('#ncexport').click(function () {
            $("#export").val('true');
            var sumtypes =$(":checkbox[name='sumtype[]'][checked]");
            if(sumtypes.length<=0){
                $("#sumtype_good").attr("checked", true);
                sumtypes =$(":checkbox[name='sumtype[]'][checked]");
            }
            var search_type_select = $('input[name="search_type_select"]:checked').val();
            $("#search_type").val($('input[name="search_type_select"]:checked').val());
            checked[search_type_select] = [];
            for(var i =0 ;i < sumtypes.length;i++){
                checked[search_type_select].push( $(sumtypes[i]).val());
            }
            $("#checked").val(makechecked(checked));
            $('#formSearch').submit();
        });
    });
    function makechecked(arr){
        var retarr = [];
        for (var row in checked){
            if(checked[row])
                retarr.push(row+':'+checked[row].join(','));
        }
        return retarr.join(";");
    }
    function getchecked(str){
        var ret = {};
        var data = str.split(";");
        for(var idx in data){
            var strs = data[idx].split(":");
            if(strs.length>1){
                var values = strs[1].split(",");
                ret[strs[0]] = values;
            }
        }
        return ret;
    }
    function sumuncheck(pre,ids){
        if(ids){
            var idarray = ids.split(",");
            for(var i = 0 ;i <idarray.length;i++){
                $("#"+pre+idarray[i]).prop("checked",false);
            }
        }
    }

</script>
<style>
    #spotresult_pass:checked + label {
        color: #008000;
    }

    #spotresult_false:checked + label {
        color: red;
    }

    #spotresult_unknown:checked + label {
        color: sienna;
    }

    #ui-datepicker-div {
        display: none;
    }
</style>

