/**
 * Created by Administrator on 2015/12/7.
 */
$(function(){
    var hei=$(window).height();
    var heit=parseInt(hei);
    $(".mod").css("minHeight",heit+"px");
});
$(function(){
    var mid=parseFloat($(".mid").html());
    var price=parseFloat($(".price").val());
    function setMid(){
        $(".mid").html(mid);
        $(".num").html(mid);
        $(".total").html(mid*price);
		$("#buyNum").val(mid);
		//alert($(".num").value);
    }
    $(".add").click(function(){
        mid++;
        setMid();
    });
    $(".min").click(function(){
        if(mid>1){
            mid--;
        }
        setMid();
    });
});
