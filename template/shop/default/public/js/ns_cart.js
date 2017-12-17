(function($) {
	$.topbar = {
		init: function() {
			if ($(".NS-USER-NAME").size() > 0) {
				$.get('/site/user', {}, function(result) {
					if (result.code == 0 && result.data != null) {
						$.sidebar.renderLogin(result.data);
						$.sidebar.initLogin = true;

						var data = result.data;

						// 判断是否存在站点
						if (data.site_id != undefined) {

							if (data.site_id == 0) {
								// 弹出选择站点的界面
								$.get('/subsite/selector', {}, function(result) {
									if (result.code == 0 && result.data != null) {
										var element = $($.parseHTML(result.data, true));
										$("body").append(element);
									}
								}, "json");
							}

							if (data.site_change != undefined || data.site_change != null) {
								if ($(".NS-SUBSITE").size() > 0) {
									$(".NS-SUBSITE").html(data.site_change);
								}
							}

						}
					}
				}, "json");
			}
		}
	};

	// 侧边栏
	$.sidebar = {
		// 初始化登录信息
		initLogin: false,
		// 初始化
		init: function() {
			// 侧边栏浏览记录
			$(".sidebar-historybox-trigger").click(function() {
				var target = this;
				if ($(target).data("load")) {
					return;
				}
				$.get("/history/box-goods-list", {}, function(result) {
					if (result.code == 0) {
						$(".sidebar-historybox").find(".sidebar-historybox-goods-list").html(result.data);
					}
					$(target).data("load", true);
				}, "json");
			});

			// 初始化侧边栏登录信息
			$(".sidebar-user-trigger").mouseover(function() {

				if ($.sidebar.initLogin) {
					return;
				}

				$.get('/site/user', {}, function(result) {
					if (result.code == 0 && result.data != null) {
						$.sidebar.renderLogin(result.data);
					}
				});

				$.sidebar.initLogin = true;
			});
		},
		renderLogin: function(user) {

			if (user && user.cart) {
				var count = user.cart.goods_count;
				if (count > 99) {
					count = "99+";
				}
				// 购物车中商品数量
				$(".NS-CART-COUNT").html(count);
			}

			// 用户信息
			if (user && user.user_name) {

				var target = $(".NS-USER-ALREADY-LOGIN");

				$(target).find(".NS-USER-NAME").html(user.user_name);

				$(target).find(".NS-USER-PIC").attr("src", user.headimg);

				if (user.user_rank) {

					$(target).find(".NS-USER-RANK-IMG").attr("src", user.user_rank.rank_img);

					$(target).find(".NS-USER-RANK-NAME").html(user.user_rank.rank_name);

				}

				$(target).find(".NS-USER-LAST-LOGIN").html(user.last_time_format);

				$(".NS-USER-NOT-LOGIN").hide();
				$(".NS-USER-ALREADY-LOGIN").show();
			} else {
				$(".NS-USER-NOT-LOGIN").show();
				$(".NS-USER-ALREADY-LOGIN").hide();
			}
		},
		// 飞入购物车效果
		fly: function(image_url, event, target) {
			if (image_url && event && $(target).size() > 0) {
				// 结束的地方的元素
				var offset = $(target).offset();
				var flyer = $('<img class="fly-img" src="' + image_url + '">');
				if ($.isFunction(flyer.fly)) {
					flyer.fly({
						start: {
							left: event.pageX - 20,
							top: event.pageY - $(window).scrollTop()
						},
						end: {
							left: offset.left + 20,
							top: offset.top - $(window).scrollTop() + 50,
							width: 0,
							height: 0
						},
						onEnd: function() {
							this.destory();
						}
					});
				}
			}
		}
	};

	// 购物车盒子
	$.cartbox = {
		// 上次访问的时间戳
		lasttime: 0,
		// 当前购物车盒子中商品的数量
		count: 0,
		// 初始化
		init: function() {

			$(".cartbox").mouseenter(function() {
				var time = new Date().getTime();
				if ($.cartbox.lasttime == 0 || time - $.cartbox.lasttime > 5 * 1000) {
					$.cartbox.load();
				}
				$(this).find(".cartbox-goods-list").show();
			}).mouseleave(function() {
				$(this).find(".cartbox-goods-list").hide();
			});

			$(".sidebar-cartbox-trigger").click(function() {
				var time = new Date().getTime();
				if ($.cartbox.lasttime == 0 || time - $.cartbox.lasttime > 5 * 1000) {
					$.cartbox.load();
				}
			});
		},
		// 设置新增了几件商品
		add: function(number) {
			// 计数累计
			this.count = parseInt(this.count) + parseInt(number);
			// 移入刷新
			this.lasttime = 0;
			// 渲染数量
			this.renderCount();
		},
		// 渲染数量
		renderCount: function(count) {
			if (!count) {
				count = this.count;
			}
			if (count > 99) {
				count = "99+";
			}
			$(".cartbox").find(".NS-CART-COUNT").html(count);

			$(".sidebar-cartbox-trigger").find(".NS-CART-COUNT").html(count);
		}
	};

	// 购物车
	$.cart = {
		loading: false,
		request: null,
		// 立即购买
		quickBuy: function(id, number, options) {
			$.loading.start();

			var data = {
				sku_id: id,
				number: number
			};

			// 拼团
			if (options && options.group_sn) {
				data.group_sn = options.group_sn;
			}

			$.post('/cart/quick-buy.html', data, function(result) {
				if (result.code == 0) {
					$.go(result.data);
				} else {
					$.msg(result.message, {
						time: 5000
					});
				}
			}, "json").always(function() {
				$.loading.stop()
			});
		},
		// 添加购物车
		// @param sku_id 商品SKU编号
		// @param number 数量
		// @param options 其他参数 {is_sku-是否为SKU, image_url-图片路径, event-点击事件,
		// shop_id-店铺编号
		// callback-回调函数}
		add: function(id, number, options) {

			var defaults = {
				// 是否为SKU商品
				is_sku: true,
				// 图片路径
				image_url: undefined,
				// 点击事件
				event: undefined,
				// 回调函数
				callback: undefined
			};

			options = $.extend(true, defaults, options);
			var data = {
				sku_id: id,
				number: number
			};

			if (options.shop_id != undefined && options.shop_id != 0) {
				data.shop_id = options.shop_id;
			}
			if (options.is_sku) {
				
				if(validationInfo(id,options.tag)){//shopping_cart.js中的函数

					//立即购买
					if(options.tag == "buy_now"){

						if($("#hidden_uid").val() == undefined || $("#hidden_uid").val() == ""){
							$("#verify_img").attr("src",$("#hidden_captcha_src").val()+"&send='"+Math.random());
							$('#mask-layer-login').attr("data-tag",options.tag).show();
							$('#layui-layer').show();
						}else{
							//防止用户恶意操作
							if($(".add-cart").hasClass("js-disabled")) return;
							if($(".js-buy-now").hasClass("js-disabled")) return;
							
							$(".js-buy-now").addClass("js-disabled");
							$(".add-cart").addClass("js-disabled");
							
							var sku_id = $("#hidden_skuid").val();
							
							//没有SKU商品，获取第一个
							if(sku_id == null || sku_id == "") sku_id = $("#goods_sku0").attr("skuid");
							
							getGoodsPurchaseRestrictionForCurrentUser($("#hidden_goodsid").val(),$("#num").val(),function(purchase){
								
								if(purchase.code>0){
									$.ajax({
										url : __URL(SHOPMAIN + "/member/ordercreatesession"),
										type : "post",
										data : { "tag" : "buy_now", "sku_id" : sku_id , "num" : $("#num").val(), "goods_type" : $("#hidden_goods_type").val() },
										success : function(res){
											if(res > 0){
												location.href= __URL(SHOPMAIN + "/member/paymentorder");
											}else{
												$.msg("购买失败");
												$(".js-buy-now").removeClass("js-disabled");
												$(".add-cart").removeClass("js-disabled");
											}
										}
									});
								}else{
									$.msg(purchase.message);
									$(".js-buy-now").removeClass("js-disabled");
									$(".add-cart").removeClass("js-disabled");
									$('#layui-layer').hide();
									$('#mask-layer-login').hide();
									getTopLoginInfo();
								}
								
							});
						}
					}else{
						var cart_detail = new Object();
						cart_detail.goods_id = $("#hidden_goodsid").val();
						cart_detail.count = $("#num").val();
						cart_detail.goods_name = $(".js-goods-name").text();
						cart_detail.sku_id = $("#hidden_skuid").val();
						//没有SKU商品，获取第一个
						if(cart_detail.sku_id == null || cart_detail.sku_id == "") cart_detail.sku_id=$("#goods_sku0").attr("skuid");
						cart_detail.sku_name = $("#hidden_skuname").val();
						cart_detail.price = $("#hidden_sku_price").val();
						cart_detail.picture_id = $("#hidden_default_img_id").val();
						cart_detail.cost_price = $("#hidden_sku_price").val();//成本价
						$.ajax({
							url : __URL(SHOPMAIN+"/goods/addcart"),
							type : "post",
							data : { "cart_detail" : JSON.stringify(cart_detail) },
							success : function(res){
								if(res.code > 0){
									// 加入购物车，飞入购物车动画特效
									$.sidebar.fly(options.image_url, options.event, $(".sidebar-cartbox-trigger"));
									$(".add-cart").removeClass("js-disabled");
									$(".js-buy-now").removeClass("js-disabled");
								}
								refreshShopCart();//里边会加载购物车中的数量
								$.msg(res.message);
							}
						});
					}
				}
			}

		}
	};

})(jQuery);