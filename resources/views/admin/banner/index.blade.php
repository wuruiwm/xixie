<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>轮播图列表</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="/static/admin/layui/css/layui.css" media="all">
    <link rel="stylesheet" href="/static/admin/font-awesome/css/font-awesome.min.css" media="all">
    <style>font{vertical-align:baseline!important;}</style>
</head>
<body class="layui-layout-body" style="overflow-y:visible;background: #fff;">
<form class="layui-form">
    <blockquote class="layui-elem-quote quoteBox">
        <div class="layui-inline" style="margin-left: 2rem;">
            <a class="layui-btn add">添加轮播图</a>
        </div>
    </blockquote>
</form>
<table class="layui-hide" id="table" lay-filter="table"></table>
<div id="edit" class="layui-form layui-form-pane" style="display: none;margin:1rem 3rem;">
    <div class="layui-form-item">
        <label class="layui-form-label">轮播图图片</label>
          <div class="layui-input-block">
        <button type="button" class="layui-btn" id="img">
          <i class="layui-icon">&#xe67c;</i>上传图片
        </button>
        <span style="color: red;">*推荐图片比例为380*750</span>
    </div>
  </div>
  <div class="layui-form-item img" style="display: none;">
    <label class="layui-form-label">图片预览</label>
    <div class="layui-input-block">
        <img src="" alt="" id="img_show" style="width: 355.55px;height: 200px;">
    </div>
  </div>
  <div class="layui-form-item">
    <label class="layui-form-label">排序</label>
    <div class="layui-input-block">
      <input type="text" placeholder="数字越大越靠前" class="layui-input" value="0" id="sort">
      <span style="color: red;">*排序的数字越大越靠前</span>
    </div>
  </div>
  <div class="layui-form-item">
    <div class="layui-input-block">
      <button class="layui-btn" lay-filter="formDemo" id="submit">立即提交</button>
    </div>
  </div>
</div>
<script type="text/html" id="img_path">
  <div>
    <a href="#">
      <img src="@{{d.img_path}}" style="width: 50px">
    </a>
  </div>
</script>
<script type="text/html" id="buttons">
  <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
  <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
</script>
<script src="/static/admin/jquery/jquery.min.js"></script>
<script src="/static/admin/layui/layui.js"></script>
<script type="text/javascript">
var id;
var img;
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
layui.use(['table','form','layer'], function(){
  var table = layui.table;
  var form = layui.form;
  var layer = layui.layer;
  table.render({
    elem: '#table' //表格id
    ,url:"{{route('admin.banner.list')}}"//list接口地址
    ,cellMinWidth: 80 //全局定义常规单元格的最小宽度
    ,height: 'full-120',
    page: true,
    limits: [14, 25, 45, 60],
    limit: 14
    ,cols: [[
    //align属性是文字在列表中的位置 可选参数left center right
    //sort属性是排序功能
    //title是这列的标题
    //field是取接口的字段值
    //width是宽度，不填则自动根据值的长度
      {field:'id', title: 'ID',align: 'center'},
      {field:'sort',title: '排序',align: 'center'},
      {field:'img_path',title: '图片',align: 'center',templet:'#img_path'},
      {field:'create_time', title: '创建时间',align: 'center'},
      {field:'update_time', title: '最后修改时间',align: 'center'},
      {fixed:'right',title: '操作', align:'center', toolbar: '#buttons'}
    ]],
    done: function () {
        hoverOpenImg();
    }
  });
  //监听
  table.on('tool(table)', function(obj){
      console.log(obj);
      //data就是一行的数据
      var data = obj.data;
      if(obj.event === 'del'){
          layer.confirm('真的删除吗', function(index){
              $.post("{{route('admin.banner.delete')}}",{id:data.id},function(res){
                if (res.status == 1) {
                    obj.del();//删除表格这行数据
                }
                layer.msg(res.msg);
              })
          });
      }else if(obj.event === 'edit'){
          id = data.id;
          img = data.img_path;
          $('#sort').val(data.sort);
          $('.img').show();
          $('#img_show').attr('src',data.img_path);
          layer.open({
            type: 1,
            title:'编辑轮播图',
            skin: 'layui-layer-rim', //加上边框
            area: ['50rem;', '30rem;'], //宽高
            content: $('#edit'),
          });
      }
    });
});
$('.add').click(function(){
    id = 0;
    img = '';
    $('#sort').val('0');
    $('.img').hide();
    $('#img_show').attr('src','');
    layer.open({
            type: 1,
            title:'添加轮播图',
            skin: 'layui-layer-rim', //加上边框
            area: ['50rem;', '30rem;'], //宽高
            content: $('#edit'),
          });
})
layui.use('upload', function(){
  var upload = layui.upload;
  //执行实例
  var uploadInst = upload.render({
    elem: '#img' //绑定元素
    ,url: "{{route('admin.upload.image')}}",
    data:{type:'carousel'} //上传接口
    ,done: function(res){
        console.log(res);
        if (res.status == 1) {
            $('.img').show();
            $('#img_show').attr('src',res.path);
            img = res.path;
        }
    }
    ,error: function(){
      //请求异常回调
    }
  });
});
$('#submit').click(function(){
    var data = {
        id:id,
        img_path:img,
        sort:$('#sort').val()
    };
    if(!id || id == '0'){
        var url = "{{route('admin.banner.create')}}";
    }else{
        var url = "{{route('admin.banner.edit')}}";
    }
    $.post(url,data,function(res){
        if (res.status == 1) {
            layer.closeAll();
            layui.use('table', function(){
                var table = layui.table;
                table.reload('table', { //表格的id
                    url:"{{route('admin.banner.list')}}",
                });
          })
        }
        layer.msg(res.msg);
    },'json');
})
function hoverOpenImg() {
        var img_show = null;
        $('td img').hover(function () {
            var kd = $(this).width();
            var kd1 = kd * 8;
            var kd2 = kd * 8 + 30;
            var img = "<img class='img_msg' src='" + $(this).attr('src') + "' style='width:" + kd1 + "px;' />";
            img_show = layer.tips(img, this, {
                tips: [2, 'rgba(41,41,41,.1)']
                , area: [kd2 + 'px']
            });
        }, function () {
            layer.close(img_show);
        });
}
</script>
</html>