<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-type" content="text/html;charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=2.0, user-scalable=yes" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="mobile-web-app-capable" content="yes" />
	<title>NITIC - Admin</title>
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
	<script type="text/javascript" src="nitic-admin.js"></script>
</head>
<body fullbleed fit layout>
	<template is="auto-binding">
		<core-drawer-panel>
			<core-header-panel drawer style="background-color:#fff3e0;" shadow>
				<core-toolbar class="medium-tall">
					<core-tooltip label="退出登录" class="fancy">
						<paper-button style="font-size:0.6em;background:none;color:white" onclick="logout()">{{user}}</paper-button>
					</core-tooltip>
					<div class="bottom">NITIC Administration</div>
				</core-toolbar>
				<core-menu selected="{{page}}" style="margin:0px" valueattr="page-name">
					<paper-item page-name="home" class="navItem"><core-icon icon="dashboard"></core-icon>Home</paper-item>
					<paper-item page-name="stock" class="navItem"><core-icon icon="trending-up"></core-icon>Stocks</paper-item>
					<paper-item page-name="futures" class="navItem"><core-icon icon="shopping-basket"></core-icon>Futures</paper-item>
					<paper-item page-name="currency" class="navItem"><core-icon icon="editor:attach-money"></core-icon>Currency</paper-item>
					<paper-item page-name="user" class="navItem"><core-icon icon="account-box"></core-icon>Users</paper-item>
					<paper-item page-name="periodicupdate" class="navItem"><core-icon icon="backup"></core-icon>Periodic Update</paper-item>
					<paper-item page-name="info" class="navItem"><core-icon icon="info"></core-icon>About</paper-item>
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
						<div id="dashboard" style="display:none">
						</div>
						<br />
						<h3>交易平台</h3>
						<hr />
						<p>
							说明:
							<ul>
								<li>STOP:停止交易(暂停/停止)</li>
								<li>START:恢复交易(用于暂停后继续)</li>
								<li>RESTART:重新开始交易(允许交易并清空各股票历史图像)</li>
							</ul>
						</p>
						<div layout horizontal>
							<paper-button raised onclick="tradectrl('start')">
								<core-icon icon="check"></core-icon>START
							</paper-button>
							<span>&nbsp;</span>
							<paper-button raised onclick="tradectrl('stop')">
								<core-icon icon="block"></core-icon>STOP
							</paper-button>
							<span>&nbsp;</span>
							<paper-button raised onclick="tradectrl('restart')">
								<core-icon icon="refresh"></core-icon>RESTART
							</paper-button>
						</div>
						<div style="display:none">
							<paper-input floatingLabel label="Refresh Interval" value="{{interval}}"></paper-input>
							<a href="{{'admin/periodicupdate.php?user='+user+'&pswd='+pswd+'&interval='+interval}}" target="_blank">Open Update Scheduler</a>
						</div>
						<br />
						<div>
							<h3>分红信息($/股)</h3><hr />
							<table id="bonustable"></table>
							<paper-button raised onclick="finalize()">Finalize</paper-button>
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
						<br />
						<paper-button onclick="document.querySelector('#window_createstock').open()" style="display:block; width:100%;">
							<core-icon icon="add"></core-icon>Create Stock/Future
						</paper-button>
					</div>
				</section>
				<!-- End stock -->

				<!-- Begin futures -->
				<section page-name="futures">
					<div cross-fade class="main_card">
						<h1 class="card_title">{{pagetitles[page]}}</h1>
						<h3>Click for details</h3>
						<div id="futures_list" style="padding:0%;font-family:monospace">
						</div>
						<br />
						<paper-button onclick="document.querySelector('#window_createstock').open()" style="display:block; width:100%;">
							<core-icon icon="add"></core-icon>Create Stock/Future
						</paper-button>
					</div>
				</section>
				<!-- End futures -->

				<!-- Begin Currency -->
				<section page-name="currency">
					<div cross-fade class="main_card">
						<h1 class="card_title">Currency</h1>
						<h3 >所有可用币种</h3>
						<div id="Money_list" style="padding:0%;font-family:monospace">
						</div>
						<paper-button onclick="document.querySelector('#window_createcurrency').open()" style="display:block; width:100%;">
							<core-icon icon="add"></core-icon>新建可用币种
						</paper-button>
						<h3> 汇率 </h3>
						<h3>点击查看详情</h3>
						<div id="Currency_list" style="padding:0%;font-family:monospace">
						</div>
						<paper-button onclick="updateCurrency()" style="display:block; width:100%;">
							<core-icon icon="add"></core-icon>更新汇率列表
						</paper-button>
						<br />
						
					</div>
				</section>
				<!-- End Currency -->
				
				<!-- Begin user -->
				<section page-name="user">
					<div cross-fade class="main_card">
						<h1 class="card_title">Create User</h1>
						<div>
							<paper-input floatingLabel label="Username" value="{{create_user}}" disabled="{{create_lock}}"></paper-input><br />
							<paper-input floatingLabel label="Password" value="{{create_pswd}}" id="pswd_create" onclick="document.querySelector('#pswd_create /deep/ input').type='password'"></paper-input><br />
							<paper-input floatingLabel label="Permission Group" value="{{create_grp}}" disabled="{{create_lock}}"></paper-input><br />
							<paper-input floatingLabel label="Initial Funds" value="{{create_funds}}" disabled="{{create_lock}}"></paper-input><br />
						</div>
						<br />
						<div layout horizontal>
							<paper-button class="plain" onclick="tmpl.create_lock=true">Lock Fields</paper-button>
							<span flex>&nbsp;</span>
							<paper-button raised onclick="createUser()">Create</paper-button>
						</div>
					</div>
				</section>
				<!-- End user -->
				
				<!-- Begin Temp -->
                <section page-name="temp">
                    <div cross-fade class="main_card">
                        <h1 class="card_title">{{pagetitles[page]}}</h1>
                        <h3>点击查看外汇详情/交易</h3>
                        <div id="temp_list">
                        </div>
                        <br /><br /><br /><br /><br /><br />
	
                        <div>
                            <sub>Function Developed by OSC Studio</sub>
                        </div>
                    </div>
                </section>
                <!-- End Temp -->

				<!-- Begin periodicupdate -->
				<section page-name="periodicupdate">
					<div cross-fade class="main_card">
						<h1 class="card_title">{{pagetitles[page]}}</h1>
						<p>
							比赛进行期间请保持全会场恰好有一个Updator运行
						</p>
						<div>
							<paper-progress value="{{update_progress}}" style="width:100%"></paper-progress>
							<hr />
							<paper-input value="{{update_interval}}" floatingLabel label="Interval (sec)"></paper-input><br />
							<paper-input-decorator floatingLabel label="Update data">
								<paper-autogrow-textarea>
									<textarea id="update_data"></textarea>
								</paper-autogrow-textarea>
							</paper-input-decorator>

							<paper-button raised onclick="updator.start()" id="updator_startbtn">Run</paper-button>
							<paper-button raised onclick="updator.commitData()">Recommit data</paper-button>
							<paper-button raised onclick="updator.update()">Force Update</paper-button>
							<hr />
							<table id="update_tasks"></table>
							<div id="update_log"></div>
						</div>
					</div>
				</section>
				<!-- End periodicupdate -->
				
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

				</core-animated-pages>

			</core-header-panel>

		</core-drawer-panel>

		<paper-toast text="{{toast}}" id="toast"></paper-toast>

		<core-overlay backdrop autoclosedisabled id="window_login" style="background:white;padding:3%;">
			<div>
				<h3>Log in to NITIC Admin</h3>
				<paper-input floatingLabel value="{{user}}" label="Username"></paper-input><br />
				<paper-input floatingLabel value="{{pswd}}" onclick="document.querySelector('#paper-password /deep/ input').type='password';" label="Password" id="paper-password"></paper-input>
				<br /><br />
				<div align="right">
					<paper-button raised onclick="logged_in=true;updateUserInfo()">Log In</paper-button>
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
				<canvas id="stkchart" width="540" height="360"></canvas>
				<div style="font-family:monospace">
					<paper-input floatingLabel label="New Price" value="{{stock_price}}"></paper-input>
				</div>
				<div layout horizontal>
					<paper-button class="plain" onclick="document.querySelector('#window_stockdetail').close()">
						<core-icon icon="close"></core-icon>Cancel
					</paper-button>
					<span flex>&nbsp;</span>
					<paper-button raised onclick="commitStockPrice()">
						<core-icon icon="check"></core-icon>
						Commit
					</paper-button>
				</div>
				<div style="display:none">
					<!-- Workaround for general nitic.js stock detail population: prevent buttons not found terminating the script -->
					<input type="hidden" id="btn_buy" />
					<input type="hidden" id="btn_sell" />
					<input type="hidden" id="fut_leverage" />
					<input type="hidden" id="fut_original_price" />
				</div>
			</div>
		</core-overlay>

		<core-overlay backdrop autoclosedisabled id="window_createstock" style="background:white;padding:3%;width:60%">
			<div>
				<paper-input floatingLabel label="ID" value="{{newstk_name}}"></paper-input><br />
				<paper-input floatingLabel label="Type (STK/FUT)" value="{{newstk_type}}"></paper-input><br />
				<paper-input floatingLabel label="Leverage (1 For Stock)" value="{{newstk_leverage}}"></paper-input><br />
				<paper-input-decorator floatingLabel label="Description (HTML supported)">
					<paper-autogrow-textarea id="newstk_description">
						<textarea></textarea>
					</paper-autogrow-textarea>
				</paper-input-decorator>
				<paper-input floatingLabel label="Price" value="{{newstk_price}}"></paper-input><br />
				<br />
				<div layout horizontal>
					<paper-button class="plain" onclick="document.querySelector('#window_createstock').close()">
						<core-icon icon="close"></core-icon> Close
					</paper-button>
					<span flex>&nbsp;</span>
					<paper-button raised onclick="createStock()">
						<core-icon icon="check"></core-icon> Create
					</paper-button>
				</div>
			</div>
		</core-overlay>
		
		<core-overlay backdrop autoclosedisabled id="window_createcurrency" style="background:white;padding:3%;width:60%">
			<div>
				<paper-input floatingLabel label="MoneyType" value="{{newmoney_name}}"></paper-input><br />
				<paper-input-decorator floatingLabel label="Description (HTML supported)">
					<paper-autogrow-textarea id="newmoney_description">
						<textarea></textarea>
					</paper-autogrow-textarea>
				</paper-input-decorator>
				<br />
				<div layout horizontal>
					<paper-button class="plain" onclick="document.querySelector('#window_createcurrency').close()">
						<core-icon icon="close"></core-icon> Close
					</paper-button>
					<span flex>&nbsp;</span>
					<paper-button raised onclick="createCurrency()">
						<core-icon icon="check"></core-icon> Create
					</paper-button>
				</div>
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


		<script type="text/javascript">
		document.querySelector("#h5_error").remove();
		// Event listeners
		$(".navItem").click(function() {
			document.querySelector("core-drawer-panel").closeDrawer();
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

		updateUserInfo();

		document.querySelector('#paper-password /deep/ input').type='password';
		</script>

	</template>

	<script type="text/javascript" src="const.js"></script>
	<script type="text/javascript">

	// Auto-binding variables initialization
	var tmpl = document.querySelector("template");
	tmpl.page="home";
	tmpl.interval = 60;
	tmpl.ajaxLoading = false;
	tmpl.pagetitles = [];
	tmpl.pagetitles['home']="Home";
	tmpl.pagetitles['stock']="Stock";
	tmpl.pagetitles['futures']="Futures";
	tmpl.pagetitles['user']="Users";
	tmpl.pagetitles['Currency']="Currency";
	tmpl.pagetitles['periodicupdate']="Periodic updator";
	tmpl.pagetitles['info']="About";
	tmpl.create_lock = false;
	tmpl.create_grp = "user";
	tmpl.create_funds = const_initial_funds;
	tmpl.update_interval = 300;
	tmpl.update_data = "";
	getUserInfo();
	</script>

	<div id="h5_error">
		<h2>此页面需要HTML5支持</h2>
		<b>如果您的浏览器长时间卡在此页面,请考虑更换浏览器</b>
		<br />
		已知支持:
		<ul>
			<li>Windows: Chrome, Firefox</li>
			<li>MacOS: Chrome, Firefox</li>
			<li>iOS: Safari, Chrome</li>
			<li>Android: Chrome</li>
		</ul>
		已知不支持:
		<ul>
			<li>Windows: IE(任何版本)</li>
			<li>MacOS: Safari</li>
			<li>iOS: (请使用默认浏览器即可)</li>
			<li>Android: (请务必安装Chrome)</li>
		</ul>
		<b>下载</b><br />
		<ul>
			<li><a href="chrome/ChromeStandaloneSetup.exe">Windows (32bit)</a></li>
			<li><a href="chrome/ChromeStandaloneSetup64.exe">Windows (64bit)</a></li>
			<li><a href="chrome/Chrome.dmg">MacOS (10.6+)</a></li>
			<li><a href="chrome/Chrome.apk">Android (4.0+)</a></li>
		</ul>
	</div>

</body>
</body>
</html>
