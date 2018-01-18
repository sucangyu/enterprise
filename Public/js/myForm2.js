/**
 * Created by Administrator on 2017/10/26 0026.
 */
before_request = 1; // 标识上一次ajax 请求有没回来, 没有回来不再进行下一次
function myForm2(form_id,submit_url){

    if(before_request == 0)
        return false;

    $.ajax({
        type : "POST",
        url  : submit_url,
        data : $('#'+form_id).serialize(),// 你的formid
        error: function(request) {
            alert("服务器繁忙, 请联系管理员!");
        },
        success: function(v) {
            before_request = 1; // 标识ajax 请求已经返回
            var v =  eval('('+v+')');
            // 验证成功提交表单
            if(v.hasOwnProperty('status'))
            {
                alert(v.msg);
                if(v.status == 1)
                {
                    if(v.hasOwnProperty('data')){
                        if(v.data.hasOwnProperty('url')){
                            location.href = v.data.url;
                        }else{
                            location.href = location.href;
                        }
                    }else{
                        location.href = location.href;
                    }
                    return true;
                }
                if(v.status == 0)
                {
                    alert(v.msg);
                    return false;
                }
                //return false;
            }
        }
    });
    before_request = 0; // 标识ajax 请求已经发出
}