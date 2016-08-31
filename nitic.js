/*
	Notice: both stocks and futures are treated under the stock model.
	No additional methods are implemented for futures, all futures operations goes under the stock model with a different algorithm
	The stock model is improved to include specific features for futures (eg. leverage), while these fields does not mean anything for stocks
 */

var logged_in = true; // As data is first read from cookie, assume already logged in
var bonus_filled = false; // Prevent periodic rewriting bonus table erasing contents filled in by user
var bonus_list = new Array();

function showLogin() {
//	document.querySelector('#paper-password /deep/ input').type='password';
	document.querySelector("#window_login").open();
	logged_in = false;
}

function updateUserInfo () {
	if (!tmpl.user || !tmpl.pswd) {
		showLogin();
		return;
	}
	if (!logged_in) {
		// While the user is inputting credential
		return;
	};
	if (tmpl.pswd.length != 40) {
		// 40 is the length of the sha1 checksum
		var shaObj = new jsSHA("SHA-1", "TEXT");
		shaObj.update(tmpl.pswd);
		tmpl.pswd = shaObj.getHash("HEX");
	}
	document.querySelector("#login_status").innerHTML="正在登录...";
	tmpl.ajaxLoading = true;
	$.ajax({
		url: "userinfo.php",
		method: "POST",
		data: {
			user: tmpl.user,
			pswd: tmpl.pswd,
			token: tmpl.token,
			skey: tmpl.skey
		},
		dataType: "json",
		error: function() {
			document.querySelector("#login_status").innerHTML="无法连接至服务器";
			showLogin();
			tmpl.ajaxLoading = false;
		},
		success: function(data) {
			if (data.status=="success") {
				logged_in = true;
				document.querySelector("#window_login").close();
				$.cookie("user",tmpl.user, {expires:30});
				$.cookie("pswd",tmpl.pswd, {expires:30});

				// Home page
				document.querySelector("#dashboard").innerHTML="欢迎回来,"+tmpl.user+"<br />\n";
				if (data.log) {
					document.querySelector("#dashboard").innerHTML+="<hr><div layout horizontal><h3>消息通知</h3><span flex>&nbsp;</span><span><paper-icon-button icon='close' onclick='dismissLogs()'></paper-icon-button></span></div><Br />\n";
					document.querySelector("#dashboard").innerHTML+="<code style='display:block'>"+data.log+"</code><Br />\n";
				}
				document.querySelector("#dashboard").innerHTML+="您的当前账户状态:<Br />\n";
				document.querySelector("#dashboard").innerHTML+="股票账户余额: <code>$ "+data.balance_stock+"</code><br />\n";
				document.querySelector("#dashboard").innerHTML+="期货账户余额: <code>$ "+data.balance_futures+"</code><br />\n";
                document.querySelector("#dashboard").innerHTML+="外汇账户余额: <code>$ "+data.balance_currency+"</code><br />\n";
				if (data.platform=="closed") {
					document.querySelector("#dashboard").innerHTML+="<br /><strong style='color:red'>当前交易平台已关闭</strong><br />\n";
				};

				tmpl.balance_stock = data.balance_stock;
				tmpl.balance_futures = data.balance_futures;
				tmpl.balance_currency = data.balance_currency;
				// Load stock info
				$.ajax({
					url: "stockinfo.php",
					method: "POST",
					data: {
						user: tmpl.user,
						pswd: tmpl.pswd
					},
					dataType: "json",
					error: function() {
						tmpl.toast = "无法连接至服务器";
						document.querySelector("#toast").show();
						tmpl.ajaxLoading = false;
					},
					success: function(data) {
						if (data.status=="success") {
							updateStock(data.data);
                            updatecurrency();
							updatemoneytype();
                            updateinfo();
							tmpl.ajaxLoading = false;
						} else {
							tmpl.alert = data.reason;
							document.querySelector("#window_alert").open();
							tmpl.ajaxLoading = false;
						}
					}
				});

			} else {
				tmpl.ajaxLoading = false;
				document.querySelector("#login_status").innerHTML=data.reason;
				showLogin();
			}
		}
	});

}

function getUserInfo() {
	tmpl.user=$.cookie("user");
	tmpl.pswd=$.cookie("pswd");
}

function logout() {
	logged_in = false;
	tmpl.user = "";
	tmpl.pswd = "";
	$.removeCookie("user");
	$.removeCookie("pswd");
	document.querySelector("#login_status").innerHTML="";
	document.querySelector("#window_login").open();
}

//Currency 
function updatecurrency(){
	document.querySelector("#Currency_list1").innerHTML="当前汇率<br />";
	$.ajax({
		type:"POST",
		url:"getCurrency.php",
		data:{
			user: tmpl.user,
			pswd: tmpl.pswd},
		dataType: "json",
		error: function() {
			document.querySelector("#Currency_list1").innerHTML="服务器发呆中。。。";
		},
		success: function(data) {
			if(data.status=="success")
			{
				//alert(data.text_1);
				document.querySelector("#Currency_list1").innerHTML=data.text_1;
			}
			else
			{
				document.querySelector("#Currency_list1").innerHTML=data.reason;
			}
		}
	});
}
//Available Money
function updatemoneytype(){
	var url="";	
    
	$.ajax({
		type:"POST",
		url:"getMoneyList.php",
		data:{
			user: tmpl.user,
			pswd: tmpl.pswd},
		dataType: "json",
		error: function() {
			document.querySelector("#Money_list").innerHTML="<img src='IMG_0062.JPG' width='400' height='300' />";
		},
		success: function(data) {
			if(data.status=="success")
			{
				//alert(data.text_1);
				document.querySelector("#Money_list").innerHTML=data.text_1;
			}
			else
			{
				document.querySelector("#Money_list").innerHTML=data.reason;
			}
		}
	});
}
// 4-she-5-ru
function decimal(num,v)  
{  
    var vv = Math.pow(10,v);  
    return Math.round(num*vv)/vv;  
}   
    
//info
function updateinfo(){
	document.querySelector("#info").innerHTML="<p>System Information:"+window.navigator.userAgent+"</p>"+"Contributor:"+"<br />"+"ilufang "+"<br />"+"Hu Qingyang"+"<br />"+"rickliu2000"+"<br />"+"Latest commit : <code>"+"20160831"+"</code> <br /> <br />"+"View Source"+"<br />"+"<a href='https://github.com/ilufang/nitic'>https://github.com/ilufang/nitic</a> "+"<br />"+" <a href='https://github.com/OSCStudio/nitic'>https://github.com/OSCStudio/nitic</a>";
}
// Stock
var stockinfo;
function updateStock(data) {
	stockinfo = data;
	var container_stk = document.querySelector("#stock_list");
	var container_fut = document.querySelector("#futures_list");
	var container_cur = document.querySelector("#Currency_list");
	container_stk.innerHTML = "";
	container_fut.innerHTML = "";
	container_cur.innerHTML = "";
	for (var i = 0; i < data.length; i++) {
		var container = container_stk;
		if (data[i].type=="FUT") 
		{
			container = container_fut;
		}
		else if (data[i].type=="CUR")
		{
            container = container_cur;
		}

		var price = data[i].price;
		var prevprice = data[i].data.trace[data[i].data.trace.length-2];
		var growth = price - prevprice;

		var stockbox = document.createElement("div");
		var pricebox = document.createElement("div");
		stockbox.setAttribute("layout","");
		stockbox.setAttribute("horizontal","");
		pricebox.setAttribute("layout","");
		pricebox.setAttribute("horizontal","");

		stockbox.style.padding="2%";
		stockbox.style.paddingBottom="0%";
		pricebox.style.padding="2%";
		pricebox.style.paddingTop="0%";

		stockbox.style.cursor = "pointer";
		pricebox.style.cursor = "pointer";
		stockbox.setAttribute("onclick","stockDetails("+i+")");
		pricebox.setAttribute("onclick","stockDetails("+i+")");

			stockbox.style.backgroundColor = "#eee";
			pricebox.style.backgroundColor = "#eee";
		if (i%2==0) {
			stockbox.style.backgroundColor = "#eee";
			pricebox.style.backgroundColor = "#eee";
		} else {
			stockbox.style.backgroundColor = "#fff";
			pricebox.style.backgroundColor = "#fff";
		}

		var title = document.createElement("h3");
		title.innerHTML = data[i].name;
		title.setAttribute("flex","");
		stockbox.appendChild(title);

		var growthratetag = document.createElement("h3");
		var growthtag = document.createElement("b");
		var pricetag = document.createElement("b");
		pricetag.setAttribute("flex","");
		if (growth>0) {
			growthratetag.style.color = "#29cc29";
			growthtag.style.color = "#29cc29";
		} else if (growth<0) {
			growthratetag.style.color = "#cc2929";
			growthtag.style.color = "#cc2929";
		}

		growthtag.innerHTML = Math.floor(growth*100)/100;
		growthratetag.innerHTML += Math.floor(10000*growth/prevprice)/100+"%";
		pricetag.innerHTML = "$"+price;
		if (stockinfo[i].amt!=0) {
			pricetag.innerHTML += " 持有:"+stockinfo[i].amt;
		}

		pricebox.appendChild(pricetag);
		pricebox.appendChild(growthtag);
		stockbox.appendChild(growthratetag);

		container.appendChild(stockbox);
		container.appendChild(pricebox);

		if (data[i].stats) {
			var stats = JSON.parse(data[i].stats);
			// Admin page: also display trade statistics
			// Formatting, copy&pasted
			var statsbox = document.createElement("div");
			statsbox.setAttribute("layout","");
			statsbox.setAttribute("vertical","");
			statsbox.style.padding="2%";
			statsbox.style.paddingBottom="0%";
			statsbox.style.cursor = "pointer";
			statsbox.style.fontFamily = "monospace";
			statsbox.setAttribute("onclick","stockDetails("+i+")");

				statsbox.style.backgroundColor = "#eee";
			if (i%2==0) {
				statsbox.style.backgroundColor = "#eee";
			} else {
				statsbox.style.backgroundColor = "#fff";
			}
			statsbox.innerHTML = "<div layout horizontal>\n"+
					"<span flex><strong>TOTAL:"+stats.total_count+"/"+stats.total_amt+"</strong></span>"+
					"<span>BUY:"+stats.total_count_buy+"/"+stats.total_amt_buy+" SELL:"+stats.total_count_sell+"/"+stats.total_amt_sell+"</span>"+
				"</div>\n"+
				"<div layout horizontal>"+
					"<span flex><strong>PREV: "+stats.local_count+"/"+stats.local_amt+"</strong></span>"+
					"<span>BUY:"+stats.local_count_buy+"/"+stats.local_amt_buy+" SELL:"+stats.local_count_sell+"/"+stats.local_amt_sell+"</span>"+
				"</div>";
			container.appendChild(statsbox);

			// When the user is admin, also fill in the bonus table
			if (!bonus_filled && data[i].type=="STK") {
				bonus_list.push(data[i].name);
				var bonusrow = document.createElement("tr");
				bonusrow.innerHTML+="<td><paper-input floatingLabel label='"+data[i].name+"' id='bonus_"+data[i].name+"''></paper-input></td>\n";
				document.querySelector("#bonustable").appendChild(bonusrow);
			}
		}
	};
	bonus_filled = true;
}

var activestock;
function stockDetails(idx) {
	activestock = idx;
	var stock = stockinfo[idx];
	//console.log("Populating idx "+idx);
	//console.log(stock);
	document.querySelector("#btn_buy").style.display="inline-block";
	document.querySelector("#btn_sell").style.display="inline-block";
	if (stock.type == 'FUT') {
		tmpl.balance = tmpl.balance_futures;
		if (stock.amt > 0) {
			document.querySelector("#btn_buy").style.display="none";
		}
		if (stock.amt < 0) {
			document.querySelector("#btn_sell").style.display="none";
		}
		if (stock.price0 && stock.amt!=0) {
			tmpl.original_price = stock.price0;
			document.querySelector("#fut_original_price").style.display="inline";
		}
		document.querySelector("#fut_leverage").style.display="inline";
	} else if (stock.type == 'CUR'){
		tmpl.balance = tmpl.balance_currency;
		document.querySelector("#fut_leverage").style.display="none";
		document.querySelector("#fut_original_price").style.display="none";
	} else {
		tmpl.balance = tmpl.balance_stock;
		document.querySelector("#fut_leverage").style.display="none";
		document.querySelector("#fut_original_price").style.display="none";
	}

	tmpl.stock_name = stock.name;
	document.querySelector("#stock_description").innerHTML = stock.description;
	tmpl.stock_holding = stock.amt;
	tmpl.stock_price = stock.price;
	tmpl.leverage = stock.leverage;
	tmpl.stock_type = stock.type;
	document.querySelector("#window_stockdetail").open();

	// draw chart
	var ctx = document.querySelector("#stkchart").getContext("2d");
	var cx = document.querySelector("#stkchart").width;
	var cy = document.querySelector("#stkchart").height;

	ctx.clearRect(0,0,cx,cy);
	ctx.fillStyle = "#eeeeee";
	ctx.fillRect(0,0,cx,cy);

	ctx.beginPath();

	var min = parseFloat(stock.data.trace[0]);
	var max = parseFloat(stock.data.trace[0]);
	for (var i = 0; i < stock.data.trace.length; i++) {
		stock.data.trace[i] = parseFloat(stock.data.trace[i]);
		if (stock.data.trace[i] < min) {
			min = stock.data.trace[i];
		}
		if (stock.data.trace[i] > max) {
			max = stock.data.trace[i];
		}
	}
	x = 0;
	y = cy-(((stock.data.trace[0]-min)/(max-min)/2)+1/4)*cy;

	for (var i = 1; i < stock.data.trace.length; i++) {
		ctx.moveTo(x,y);
		x = i/stock.data.trace.length*cx;
//		y = cy-(((stock.data.trace[i]-min)/(max-min)/2)+1/4)*cy;
		y = cy*(1-((stock.data.trace[i]-min)/(max-min))*0.8-0.1);
		ctx.lineTo(x,y);
		ctx.stroke();
	}
}

function tradeStock(action) {
	document.querySelector("#window_stockdetail").close();
	tmpl.amt = 0;
	tmpl.stockaction = action;
	tmpl.balance_after = tmpl.balance;
	document.querySelector("#after_stkholding").innerHTML = tmpl.stock_holding;

	document.querySelector("#btn_stktrade").disabled = true;
	document.querySelector("#after_stkholding").style.color="black";
	document.querySelector("#after_balance").style.color="black";
	document.querySelector("#window_stocktrade").open();
}

function confirmStock() {
	tmpl.reqAjaxLoading = true;
	$.ajax({
		url: "tradestock.php",
		method: "POST",
		data: {
			user: tmpl.user,
			pswd: tmpl.pswd,
			name: tmpl.stock_name,
			price: tmpl.stock_price,
			amt: tmpl.amt,
			action: tmpl.stockaction
		},
		dataType: "json",
		error: function() {
			tmpl.ajaxLoading = false;
			tmpl.alert = "无法连接至服务器.";
			document.querySelector("#window_alert").open();
		},
		success: function(data) {
			tmpl.reqAjaxLoading = false;
			if (data.status=="success") {
				document.querySelector("#window_stocktrade").close();
				tmpl.toast = "交易成功";
				document.querySelector("#toast").show();
				updateUserInfo();
			} else {
				tmpl.alert = "交易失败: "+data.reason;
				document.querySelector("#window_alert").open();
			}
		}
	});
}

function dismissLogs() {
	$.ajax({
		url: "dismissLogs.php",
		method: "POST",
		data: {
			user: tmpl.user,
			pswd: tmpl.pswd
		},
		dataType: "json",
		error: function() {
			tmpl.alert = "无法连接至服务器.";
			document.querySelector("#window_alert").open();
		},
		success: function(data) {
			if (data.status=="success") {
				updateUserInfo();
			} else {
				tmpl.alert = "请求失败: "+data.reason;
				document.querySelector("#window_alert").open();
			}
		}
	});
}
