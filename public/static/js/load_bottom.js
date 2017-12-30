/**
 *  功能描述：加载底部的版权信息
 */
$(function(){
	$.ajax({
		url : __URL(APPMAIN + "/task/copyrightisload"),
		type : "post",
		dataType : "json",
		success : function(data) {
			$is_load=data["is_load"];
			$bottom_info=data["bottom_info"];
			var copyright_meta = "";
			if($is_load>0){
				$("#copyright_logo").attr("src", STATIC + data["default_logo"]);
				$("#copyright_companyname").attr("href", "http://www.niushop.com.cn");
				$("#copyright_companyname").html("山西牛酷信息科技有限公司&nbsp;提供技术支持");
				$("#copyright_desc").html("Copyright © 2015-2025 NIUSHOP开源商城&nbsp;版权所有 保留一切权利");
			}else{
				$("#copyright_logo").attr("src", __IMG($bottom_info["copyright_logo"]));
				$("#copyright_logo_wap").attr("src", __IMG($bottom_info["copyright_logo"]));
				$("#copyright_companyname").attr("href", $bottom_info["copyright_link"]);
				$("#copyright_companyname").html($bottom_info["copyright_companyname"]);
				$("#copyright_desc").html($bottom_info["copyright_desc"]);
				$("#login_copyright").hide();
				$("#rigister_copyright").hide();
			}
			//备案信息
			if($bottom_info["copyright_meta"] != "" && $bottom_info["copyright_meta"] != null){
				copyright_meta = "<a href='http://www.miitbeian.gov.cn' target='_blank' style='text-decoration: none;'>备案号："+$bottom_info["copyright_meta"]+'</a>';
			}
			
			//网站公安备案信息
			if($bottom_info["web_gov_record_url"] != undefined){
				if($bottom_info["web_gov_record_url"].length > 0){

					$("#web_gov_record").find("a").attr("href",$bottom_info["web_gov_record_url"]);
				}
			}
			if($bottom_info["web_gov_record"] != undefined){
				
				if($bottom_info["web_gov_record"].length > 0){
					$("#web_gov_record").find("span").text($bottom_info["web_gov_record"]);
					$("#web_gov_record").show();
				}
			}

			$("#copyright_meta").html(copyright_meta);
		}
	});
});