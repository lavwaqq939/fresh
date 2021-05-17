layui.use(['element', 'table', 'layer', 'form', 'laydate'], function () {
    var element = layui.element;
    var table = layui.table;
    var layer = layui.layer;
    var form = layui.form;
    var laydate = layui.laydate;

    //修改 table 字段
    table.on('edit(edit-table)', function (obj) { //注：edit是固定事件名，test是table原始容器的属性 lay-filter="对应的值"
        data = obj.data;
        value = obj.value;
        field = obj.field;
        url = $('.edit-table').data('url');
        key = $('.edit-table').data('key');
        $.post(url, {id: data[key], field: field, value: value}, function () {
            data[field] = value;
        })
    });

    //监听工具条
    table.on('tool(edit-table)', function (obj) {
        var data = obj.data;
        var url = $(this).data('url');
        var key = $('.edit-table').data('key');
        if (obj.event === 'del') {
            layer.confirm('真的要删除么', function (index) {
                $.post(url, {id: data[key]}, function (res) {
                    layer.close(index);
                    var msg = res.msg ? res.msg : "网络异常";
                    if (res.code == 200) {
                        obj.del();
                    }
                    layer.msg(msg);
                })
            });
        }
        if (obj.event === 'refuse') {
            layer.confirm('真的拒绝吗?', function (index) {
                $.post(url, {id: data[key]}, function (res) {
                    layer.close(index);
                    var msg = res.msg ? res.msg : "网络异常";
                    if (res.code == 200) {
                        window.location.reload();
                    }
                    layer.msg(msg);
                })
            });
        }
        if (obj.event === 'agree') {
            layer.confirm('真的确定吗?', function (index) {
                $.post(url, {id: data[key]}, function (res) {
                    layer.close(index);
                    var msg = res.msg ? res.msg : "网络异常";
                    if (res.code == 200) {
                        window.location.reload();
                    }
                    layer.msg(msg);
                })
            });
        }
    });
    //弹出层
    $(document).on('click', '.model-a', function (e) {
        e.preventDefault();
        var url = $(this).attr('href');
        $.get(url, {}, function (str) {
            var model_index = layer.open({
                type: 1,
                content: str //注意，如果str是object，那么需要字符拼接。
                , skin: 'min-width-400'
            });
            $(document).on('click','model-close',function () {
                layer.close(model_index)
            })
        });

    });

    //监听提交
    form.on('submit(ajax)', function (data) {
        var loading = layer.load(1, {
            shade: [0.1, '#fff'] //0.1透明度的白色背景
        });
        var url = data.form.action;
        var method = data.form.method;
        $.ajax({
            type: method,
            url: url,
            data: data.field,
            dataType: 'json',
            success: function (res) {
                layer.close(loading);
                msg = res.msg ? res.msg : '网络错误';
                layer.msg(msg);
                jumpUrl = res.jumpUrl;
                if (res.code == 200) {
                    if (jumpUrl) {
                        window.location.href = jumpUrl;
                    } else {
                        window.location.reload();
                    }
                }
            },
            error: function (res) {
                layer.close(loading);
                layer.msg("网络错误");
            }
        });
        return false;
    });

    laydate.render({
        elem: '.laydate-date' //指定元素
        , range: false
        // , showBottom: false
        , theme: 'grid'
    });
    laydate.render({
        elem: '.laydate-time' //指定元素
        , type: 'time'
        , range: false
        , showBottom: false
        , theme: 'grid'

    });
    laydate.render({
        elem: '.laydate-time-start' //指定元素
        , type: 'time'
        , range: false
        // , showBottom: false
        , theme: 'grid'

    });
    laydate.render({
        elem: '.laydate-time-end' //指定元素
        , type: 'time'
        , range: false
        // , showBottom: false
        , theme: 'grid'
    });
});