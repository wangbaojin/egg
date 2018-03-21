<?php if (!defined('THINK_PATH')) exit();?>
<div class="container-fluid">
    <div class="jumbotron">
        <small>添加新闻</small>
    </div>

    <form method="post" id="create_form">
        <hr>
        <div class="form-group">
            <label>标题:</label>
            <input type="text" name="title" id="title" class="form-control"  placeholder="标题">
        </div>
        <div class="form-group">
            <label>原文链接:</label>
            <input type="text" name="article_url" id="article_url" class="form-control"   placeholder="原文链接">
        </div>
        <div class="form-group">
            <label>摘要:</label>
            <textarea class="form-control" name="abstract" id="abstract"></textarea>
        </div>
        <div class="form-group">
            <label>发布时间:</label>
            <input type="datetime" name="newstime" id="newstime" onclick="layui.laydate({elem: this, istime: true, format: 'YYYY-MM-DD 00:00:00'})" class="layui-input form-control" value="<?php echo ($info["newstime"]); ?>" placeholder="发布时间">
        </div>
        <div class="form-group">
            <label>封面:</label>
            <input type="file" name="news_cover" id="news_cover" value="<?php echo ($info["newstime"]); ?>" placeholder="封面">
        </div>
        <div class="layui-upload">
            <input type="file" name="news_cover" id="test1" value="<?php echo ($info["newstime"]); ?>" placeholder="封面">
            <div class="layui-upload-list">
                <img class="layui-upload-img" id="demo1" width="100px">
                <p id="demoText"></p>
            </div>
        </div>
        <div class="form-group">
            <label>文章来源:</label>
                <?php if(is_array($come_from)): $i = 0; $__LIST__ = $come_from;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$c_vo): $mod = ($i % 2 );++$i; echo ($c_vo); ?><input type="radio" checked name="come_from" value="<?php echo ($key); ?>"><?php endforeach; endif; else: echo "" ;endif; ?>
                <br>
        </div>
        <div class="form-group">
            <label>是否置顶:<input type="checkbox" name="top_num" id="top_num" class="" ><br><small>[选择后该文章将会置顶]</small><br></label>
        </div>
        <button type="button" onclick="add()" id="tj" class="btn btn-default">提交</button><br><br>
    </form>
</div>
<br>

<script src="/Public/static/layui_2_2_5/layui.js"></script>
<script type="text/javascript">
    layui.use('layer', function(){ //独立版的layer无需执行这一句
        //var $ = layui.jquery,
        layer = layui.layer; //独立版的layer无需执行这一句
    });
    layui.use('laydate', function(){
        var laydate = layui.laydate;
        var start = {
            //min: laydate.now(),
            istoday: true,
            choose: function(datas){
                end.min = datas; //开始日选好后，重置结束日的最小日期
                end.start = datas //将结束日的初始值设定为开始日
            }
        };
    });

    layui.use('upload', function(){
        var $ = layui.jquery
        var upload = layui.upload;

        //执行实例
        var uploadInst = upload.render({
            elem: '#test1',
            url : '',
            before: function (obj) {
                //alert(2)
                //预读本地文件示例，不支持ie8
                obj.preview(function (index, file, result) {
                    $('#demo1').attr('src', result); //图片链接（base64）
                });
            },error: function() {
                alert(1)
            },done: function(res){
                alert(2)
            }
        });
    });

    function add(){

        url = "<?php echo U('AdminNews/add');?>"

        var formData = new FormData($( "#create_form" )[0]);

        title       = $( "#title" ).val();
        article_url = $( "#article_url" ).val();
        newstime = $( "#newstime" ).val();
        top_num  = $( "#top_num" ).val();
        abstract = $( "#abstract" ).val();

        if(!title || !article_url || !newstime || !top_num || !abstract) {
            layer.msg('程序员小哥哥提示你:信息都不填全就提交?是我拿不动刀了还是你飘了?',{
                icon:2,
                time:2000
            });return false;
        }

        $('#tj').attr('disabled',true)
        $('#tj').html('正在提交。。。')

        $.ajax({
            type: 'POST',
            url: url,
            data:formData,
            processData: false,
            contentType: false,
            dataType: 'text',
            success:function(msg){
                if(msg=='success') {
                    layer.msg('添加成功!',{
                        icon:1,
                        time:2000
                    });
                    $('#tj').html('提交')
                    $('#tj').attr('disabled',false)
                    setTimeout("parent.location.reload()", 2000);
                }
                else
                {
                    layer.msg(msg,{
                        icon:2,
                        time:2000
                    });
                    $('#tj').html('提交')
                    $('#tj').attr('disabled',false)
                }
            },
            error:function(){
                layer.msg('程序员小哥哥提示你:未知错误!请稍后重试!',{
                    icon:2,
                    time:2000
                });
                $('#tj').html('提交')
                $('#tj').attr('disabled',false)
            }
        });
    }
</script>
</body>
</html>