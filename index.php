<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-type" content="text/html;charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=2.0, user-scalable=yes" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="mobile-web-app-capable" content="yes" />
	<title>NITIC</title>
	<!-- webcomponents -->
	<script type="text/javascript" src="bower_components/webcomponentsjs/webcomponents.min.js"></script>
		<!-- jquery (for ajax and cookie) -->
	<script type="text/javascript" src="bower_components/jquery/dist/jquery.min.js"></script>
	<script type="text/javascript" src="bower_components/jquery.cookie/jquery.cookie.js"></script>
	<!-- jsSHA (password cryptography) -->
	<script type="text/javascript" src="bower_components/jsSHA/src/sha1.js"></script>
	<link rel="import" href="import.html" />
	<!--stylesheet-->
	<link rel="stylesheet" type="text/css" href="nitic.css"/>
	<!--app-->
	<script type="text/javascript" src="nitic.js"></script>
</head>
<body fullbleed fit layout>
	<div id="h5_error">
		<h2>此页面需要Polymer HTML5支持</h2>
		<b>如果您的浏览器长时间卡在此页面,请考虑更换浏览器,推荐使用最新的Chrome访问</b>
		<br />
		<b>下载</b><br />
		<ul>
			<li><a href="chrome/ChromeStandaloneSetup.exe">Windows (32bit)</a></li>
			<li><a href="chrome/ChromeStandaloneSetup64.exe">Windows (64bit)</a></li>
			<li><a href="chrome/Chrome.dmg">MacOS (10.6+)</a></li>
			<li><a href="chrome/Chrome.apk">Android (4.0+)</a></li>
		</ul>
	</div>

	<template is="auto-binding">
		<core-drawer-panel>
			<core-header-panel drawer style="background-color:#fff3e0;" shadow>
				<core-toolbar class="medium-tall">
					<core-tooltip label="退出登录" class="fancy">
						<paper-button style="font-size:0.6em;background:none;color:white" onclick="logout()">{{user}}</paper-button>
					</core-tooltip>
					<div class="bottom">NITIC</div>
				</core-toolbar>
				<core-menu selected="{{page}}" style="margin:0px" valueattr="page-name">
					<paper-item page-name="home" class="navItem"><core-icon icon="dashboard"></core-icon>Home</paper-item>
					<paper-item page-name="stock" class="navItem"><core-icon icon="trending-up"></core-icon>股票</paper-item>
					<paper-item page-name="futures" class="navItem"><core-icon icon="shopping-basket"></core-icon>期货</paper-item>
                    <paper-item page-name="Currency" class="navItem"><core-icon icon="editor:attach-money"></core-icon>外汇</paper-item>
                     <paper-item page-name="Currency1" class="navItem"><core-icon icon="editor:attach-money"></core-icon>外汇1</paper-item>
                    <paper-item page-name="info" class="navItem"><core-icon icon="info"></core-icon>关于本机</paper-item>
				</core-menu>
			</core-header-panel>
			<core-header-panel main style="background-color:#fff3e0" shadow>
				<core-toolbar id="responsive-toolbar">
					<paper-icon-button icon="menu" id="menutoggle" core-drawer-toggle></paper-icon-button>
					<span id="page_title">{{pagetitles[page]}}</span>
					<span flex>&nbsp;</span>
					<paper-spinner active="{{ajaxLoading}}"></paper-spinner>
				</core-toolbar>
				<core-animated-pages selected="{{page}}" valueattr="page-name" transitions="cross-fade">

				<!-- Fill pages -->

				<!-- Begin template -->
				<section page-name="template">
					<div cross-fade class="main_card">
						<h1 class="card_title">{{pagetitles[page]}}</h1>
						<div><!-- Content --></div>
					</div>
				</section>
				<!-- End template -->

				<!-- Begin home -->
				<section page-name="home">
					<div cross-fade class="main_card">
						<h1 class="card_title">{{pagetitles[page]}}</h1>
						<div id="dashboard">
						</div>
						<br /><br /><br /><br /><br /><br />
						<div>
							<sub>   </sub>
						</div>
					</div>
				</section>
				<!-- End home -->

				<!-- Begin stock -->
				<section page-name="stock">
					<div cross-fade class="main_card">
						<h1 class="card_title">{{pagetitles[page]}}</h1>
						<h3>点击股票查看详情/交易</h3>
						<div id="stock_list" style="padding:0%;font-family:monospace">

						</div>
					</div>
				</section>
				<!-- End stock -->

				<!-- Begin futures -->
				<section page-name="futures">
					<div cross-fade class="main_card">
						<h1 class="card_title">{{pagetitles[page]}}</h1>
						<h3>点击期货查看详情/交易</h3>
						<div id="futures_list" style="padding:0%;font-family:monospace">
						</div>
					</div>
				</section>
				<!-- End futures -->
				
				<!-- Begin Currency -->
				<section page-name="Currency">
					<div cross-fade class="main_card">
						<h1 class="card_title">{{pagetitles[page]}}</h1>
						<h3>点击期货查看详情/交易</h3>
						<div id="Currency_list" style="padding:0%;font-family:monospace">
						</div>
					</div>
				</section>
				<!-- End Currency -->

                <!-- Begin Currency1 -->
                <section page-name="Currency1">
                    <div cross-fade class="main_card">
                        <h1 class="card_title">{{pagetitles[page]}}</h1>
						<h1 class="card_title">{{pagetitles[page]}}</h1>
                        <h3 >所有可用币种</h3>
						<div id="Money_list" style="padding:0%;font-family:monospace">
						</div>
						<h3> 汇率 </h3>
						<h3>点击查看详情</h3>
						<div id="Currency_list1" style="padding:0%;font-family:monospace">
						</div>
                        <br /><br /><br /><br /><br /><br />
						<paper-button raised onclick="tradeCurrency('')" id="btn_Curbuy">
                        <core-icon icon="file-download"></core-icon>
                                    计算器
                        </paper-button>
                        <paper-button raised onclick="tradeCurrency('')" id="btn_Curbuy">
                        <core-icon icon="file-download"></core-icon>
                                    购入
                        </paper-button>
                        <paper-button raised onclick="tradeCurrency()" id="btn_Cursell">
                        <core-icon icon="file-upload"></core-icon>
                                    卖出
                        </paper-button>
                        <div>
                            <sub>   </sub>
                        </div>
                    </div>
                </section>
                <!-- End Currency1 -->
 				
                <!-- Begin info -->
                <section page-name="info">
                    <div cross-fade class="main_card">
                        <h1 class="card_title">{{pagetitles[page]}}</h1>
                        <div id="info">
                        </div>
                        <br /><br /><br /><br /><br /><br />
                        <div>
                             <sub>    </sub>
                        </div>
                    </div>
                </section>
                <!-- End info -->

                <!-- Begin Temp -->
                <section page-name="temp">
                    <div cross-fade class="main_card">
                        
                        <br /><br /><br /><br /><br /><br />
						<div id="temp_list" style="padding:0%;font-family:monospace">
						</div>
                        <div>
                            <sub>Function Developed by OSC Studio</sub>
                        </div>
                    </div>
                </section>
                <!-- End Temp -->

				</core-animated-pages>

			</core-header-panel>

		</core-drawer-panel>

		<paper-toast text="{{toast}}" id="toast"></paper-toast>

		<core-overlay backdrop autoclosedisabled id="window_login" style="background:white;padding:3%;">
			<div>
				<h3>登录NITIC</h3>
				<paper-input floatingLabel value="{{user}}" label="用户名"></paper-input><br />
				<paper-input floatingLabel value="{{pswd}}" label="密码" id="paper-password"></paper-input>
				<br /><br />
				<div align="right">
					<paper-button raised onclick="logged_in=true;updateUserInfo()">登录</paper-button>
				</div>
				<span id="login_status">&nbsp;</span>
			</div>
		</core-overlay>

		<core-overlay backdrop autoclosedisabled id="window_stockdetail" style="background:white;padding:3%;max-width:600px">
			<div>
				<div layout horizontal>
					<h3 flex>{{stock_name}}</h3>
					<h3>{{priceinfo}}</h3>
				</div>
				<p id="stock_description">
				</p>
				<canvas id="stkchart" width="540" height="360" style="width:100%"></canvas>
				<div style="font-family:monospace">
					当前持有数:{{stock_holding}}
				</div>
				<div layout horizontal>
					<paper-button class="plain" onclick="document.querySelector('#window_stockdetail').close()">
						<core-icon icon="close"></core-icon>关闭
					</paper-button>
					<span flex>&nbsp;</span>
					<paper-button raised onclick="tradeStock('BUY')" id="btn_buy">
						<core-icon icon="file-download"></core-icon>
						购入
					</paper-button>
					<paper-button raised onclick="tradeStock('SELL')" id="btn_sell">
						<core-icon icon="file-upload"></core-icon>
						卖出
					</paper-button>
				</div>
			</div>
		</core-overlay>


		<core-overlay backdrop autoclosedisabled id="window_stocktrade" style="background:white;padding:8%;">
			<div>
				<div layout horizontal>
					<h3 flex>{{stockaction}}:{{stock_name}}</h3>
					<paper-spinner active="{{reqAjaxLoading}}"></paper-spinner>
				</div>
				<code style="display:block;padding:3%">
					<div style="display:none">
					  <span id="fut_leverage"><br /></span>
					</div>
					<span id="fut_original_price" style="display:none">原价: {{original_price}}<br /></span>
					价格: {{stock_price}}<Br />
					持有: {{stock_holding}} -> <span id="after_stkholding"></span><br />
					余额: {{balance}} -> <span id="after_balance">{{balance_after}}</span>
				</code>
				<!-- TODO Recalculate according to futures leverage rules -->
				<paper-input floatingLabel label="交易量" value="{{amt}}" id="input_stockamt"></paper-input>
			</div>
			<div layout horizontal>
				<paper-button onclick="document.querySelector('#window_stocktrade').close()" class="plain">
					<core-icon icon="close"></core-icon>取消
				</paper-button>
				<span flex>&nbsp;</span>
				<paper-button onclick="confirmStock()" raised id="btn_stktrade">
					<core-icon icon="done"></core-icon>确定
				</paper-button>
			</div>
		</core-overlay>
        
        <core-overlay backdrop autoclosedisabled id="window_curtrade" style="background:white;padding:8%;">
			<div>
				<div layout horizontal>
					<h3 flex>{{stockaction}}:{{stock_name}}</h3>
					<paper-spinner active="{{reqAjaxLoading}}"></paper-spinner>
				</div>
				<code style="display:block;padding:3%">
					<div style="display:none">
					  <span id="fut_leverage"><br /></span>
					</div>
					<span id="fut_original_price" style="display:none">原价: {{original_price}}<br /></span>
					汇率: {{cur_price}}<Br />
					持有: {{cur_holding}} -> <span id="after_curholding"></span><br />
					余额: {{cur_balance}} -> <span id="after_curbalance">{{balance_after}}</span>
				</code>
				<!-- TODO Recalculate according to futures leverage rules -->
				<paper-input floatingLabel label="交易量" value="{{cur_amt}}" id="input_curamt"></paper-input>
			</div>
			<div layout horizontal>
				<paper-button onclick="document.querySelector('#window_curtrade').close()" class="plain">
					<core-icon icon="close"></core-icon>取消
				</paper-button>
				<span flex>&nbsp;</span>
				<paper-button onclick="confirmStock()" raised id="btn_curtrade">
					<core-icon icon="done"></core-icon>确定
				</paper-button>
			</div>
		</core-overlay>

		<core-overlay backdrop autoclosedisabled id="window_alert" style="background:white;padding:3%;box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.26)">
			<div>
				<p>{{alert}}</p>
				<div align="right">
					<paper-button raised onclick="document.querySelector('#window_alert').close()">关闭</paper-button>
				</div>
			</div>
		</core-overlay>
	</template>

<script type="text/javascript">
	function periodicUpdate() {
		setTimeout(periodicUpdate, 5000); // Refresh info per 5 seconds
		updateUserInfo();
	}

	var lansvr = [];

	function getTokenNextServer() {
		var currentSvr = lansvr.shift();
		if (!currentSvr) {
			alert("没有可用的验证服务器");
			return;
		}
		$.ajax({
			url: currentSvr+"/lanauth.php",
			method: "POST",
			timeout: 2000,
			data: {
				skey: tmpl.skey
			},
			dataType: "json",
			error: function() {
				getTokenNextServer();
			},
			success: function(data) {
				if (data.token) {
					tmpl.token = data.token;
					periodicUpdate();
				} else {
					getTokenNextServer();
				}
			}
		});
	}

	function initLogin() {
		var skey = $.cookie('skey');
		if (!skey) {
			var charset = "1234567890abcdef";
			skey = "";
			for(var i=0; i<40; i++) {
				skey += charset.charAt(Math.floor(Math.random()*charset.length));
			}
			$.cookie("skey", skey, {expires:1});
		}
		tmpl.skey = skey;
		$.ajax({
			url: "tokensvr.php",
			method: "GET",
			dataType: "json",
			failure: function() {
				alert("无法连接到服务器, 请刷新页面.");
			},
			success: function(data) {
				if (data.length>0) {
					lansvr = data;
					getTokenNextServer();
				} else {
					alert("请求错误: 无法找到一个可用的验证服务器");
				}
			}
		})
	}

	function tmplinit() {
		document.querySelector("#h5_error").remove();

		// Event listeners
		$(".navItem").click(function() {
			document.querySelector("core-drawer-panel").closeDrawer();
		});

		document.querySelector("#input_stockamt /deep/ input").addEventListener("input",function(){
			// Force convert to number
			var abs_amt = parseInt(tmpl.amt);

			if (isNaN(abs_amt)) {
				// Abort on illegal input
				document.querySelector("#btn_stktrade").disabled = true;
				return;
			}

			// Calculate fields
			if (tmpl.stockaction=="SELL") {
				abs_amt *= -1;
			}

			document.querySelector("#after_stkholding").innerHTML = tmpl.stock_holding+abs_amt;

			tmpl.balance*=1;
			if (tmpl.stock_type=="FUT") {
				// Futures
				if (tmpl.stock_holding==0) {
					// New session
					tmpl.balance_after = tmpl.balance-tmpl.amt*tmpl.stock_price/tmpl.leverage;
				} else {
					// Unfinished session
					tmpl.balance_after = tmpl.balance+Math.abs(abs_amt)*tmpl.original_price/tmpl.leverage-abs_amt*(tmpl.stock_price-tmpl.original_price);
				}
			} else {
				// Stock
				tmpl.balance_after = tmpl.balance - abs_amt*tmpl.stock_price;
			}

			// TODO: Calculate account balance after trading
			// Same algorithm should be applied to the server-side as well
			// Scheduler should reset statistics correctly (on start, end, tick)

			var valid = true;
			if (!(tmpl.amt>0)) {
				valid=false;
			};

			if ((tmpl.stock_holding+abs_amt)*tmpl.stock_holding<0 || (tmpl.stock_type=="STK" && tmpl.stock_holding+abs_amt<0)) {
				// When the holding amount changes its sign,
				// It means that the futures amount goes beyond 0
				valid = false;
				document.querySelector("#after_stkholding").style.color="red";
			} else {
				document.querySelector("#after_stkholding").style.color="black";
			}
			if (tmpl.balance_after<0) {
				valid = false;
				document.querySelector("#after_balance").style.color="red";
			} else {
				document.querySelector("#after_balance").style.color="black";
			}
			if (valid) {
				document.querySelector("#btn_stktrade").disabled = false;
			} else {
				document.querySelector("#btn_stktrade").disabled = true;
			}
		});

		document.querySelector("core-drawer-panel").addEventListener("core-responsive-change",function(e) {
			if (e.detail.narrow) {
				document.querySelector("core-header-panel[main]").mode="standard";
				$("#responsive-toolbar").removeClass("medium-tall");
			} else {
				document.querySelector("core-header-panel[main]").mode="cover";
				$("#responsive-toolbar").addClass("medium-tall");
			}
		});

		if (window.innerWidth<=640) {
			document.querySelector("core-header-panel[main]").mode="standard";
			$("#responsive-toolbar").removeClass("medium-tall");
		} else {
			document.querySelector("core-header-panel[main]").mode="cover";
			$("#responsive-toolbar").addClass("medium-tall");
		}

		document.querySelector('#paper-password /deep/ input').type='password';

		// Fetch for a valid token before login

		initLogin();
	}
</script>


	<script type="text/javascript">
	// Auto-binding variables initialization
	var tmpl = document.querySelector("template");
	tmpl.page="home";
	tmpl.ajaxLoading = false;
	tmpl.reqAjaxLoading = false;
	tmpl.pagetitles = [];
	tmpl.pagetitles['home']="Home";
	tmpl.pagetitles['stock']="股票";
	tmpl.pagetitles['futures']="期货";
    tmpl.pagetitles['Currency1']="外汇1";
    tmpl.pagetitles['Currency']="外汇";
    tmpl.pagetitles['info']="关于";
	getUserInfo();

	tmpl.addEventListener('template-bound', tmplinit);

	</script>


	<div style="display:none">
		<table id="bonustable"></table>
	</div>
</body>
</h
