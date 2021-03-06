<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>          
    @if (empty($data['id']))
        添加优惠券
    @else
        编辑优惠券
    @endif
    </title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="/static/admin/layui/css/layui.css" media="all">
    <link rel="stylesheet" href="/static/admin/font-awesome/css/font-awesome.min.css" media="all">
</head>
<body class="layui-layout-body" style="overflow-y:visible;">
    <fieldset class="layui-elem-field layui-field-title" style="margin-top: 30px;">
        <legend>
        @if (empty($data['id']))
            添加优惠券
        @else
            编辑优惠券
        @endif
        </legend>
    </fieldset>
    <form class="layui-form layui-form-pane">
        <div class="layui-form-item">
            <label class="layui-form-label">面值</label>
            <div class="layui-input-block">
              <input type="text" name="face_value" placeholder="请输入优惠券面值" class="layui-input" required lay-verify="required" value="@if (!empty($data['face_value'])){{$data['face_value']}}@endif">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">有效期</label>
            <div class="layui-input-block">
              <input type="text" name="validity_time" placeholder="领取后几天有效" class="layui-input" required lay-verify="required" value="@if (!empty($data['validity_time'])){{$data['validity_time']}}@endif">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">满减</label>
            <div class="layui-input-block">
              <input type="text" name="full" placeholder="订单金额满多少可用" class="layui-input"  value="@if (!empty($data['full'])){{$data['full']}}@endif">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">总数量</label>
            <div class="layui-input-block">
              <input type="text" name="total" placeholder="请输入优惠券发放总数量" class="layui-input"  required lay-verify="required" value="@if (!empty($data['total'])){{$data['total']}}@endif">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">领取时间</label>
            <div class="layui-input-block">
              <input type="text" name="time" class="layui-input" placeholder="请选择优惠券领取时间范围" id="time" required lay-verify="required" value="@if (!empty($data['start_time']) && !empty($data['end_time'])){{date('Y-m-d H:i:s',$data['start_time'])}} - {{date('Y-m-d H:i:s',$data['end_time'])}}@endif">
            </div>
        </div>
        <div class="layui-form-item">
          <div class="layui-input-block">
            <input type="hidden" class="layui-input" name="id" value="@if (!empty($data['id'])){{$data['id']}}@endif">
            <button class="layui-btn" lay-submit lay-filter="submit">立即提交</button>
          </div>
        </div>
      </form>
</body>
<script src="/static/admin/jquery/jquery.min.js"></script>
<script src="/static/admin/layui/layui.js"></script>
<script type="text/javascript">
$.ajaxSetup({
    headers:{
        'X-CSRF-TOKEN': "{{csrf_token()}}"
    },
    dataType:'json',
    timeout:5000,
    beforeSend:function(){
        layer.load();
    },
    error:function(xhr){
        if(xhr.status == 419){
          layer.msg('CSRF验证过期,请刷新本页面后重试');
        }else if(xhr.status == 403){
          layer.msg('请检查您是否有权限');
        }else{
          layer.msg('访问出错');
        }
    },
    complete:function(){
        layer.closeAll('loading');
    }
});
    layui.use(['form','laydate','layer'], function(){
        var form = layui.form;
        var laydate = layui.laydate;
        var layer = layui.layer;
        laydate.render({
            elem: '#time',//指定元素
            type: 'datetime' ,
            range: true
        });
        //监听提交
        form.on('submit(submit)', function(data){
            $.post("",data.field,function(res){
                if(res.status == 1){
                    layer.closeAll("iframe");
                    parent.location.reload();
                }
                layer.msg(res.msg);
            },'json')
            return false;
        });
    });
</script>
</html>