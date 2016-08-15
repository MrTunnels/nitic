function tradectrl(action) {
	$.ajax({
		url: "admin/tradectrl.php",
		method: "POST",
		data: {
			user: tmpl.user,
			pswd: tmpl.pswd,
			action: action
		},
		dataType: "json",
		failure: function() {
			tmpl.alert = "Cannot connect to the server.";
			document.querySelector("#window_alert").open();
		},
		success: function(data) {
			if (data.status=="success") {
				tmpl.toast = "Done.";
				document.querySelector("#toast").show();
			} else {
				tmpl.alert = "Request Rejected: "+data.reason;
				document.querySelector("#window_alert").open();
			}
		}
	});
}

function commitStockPrice() {
	$.ajax({
		url: "admin/setstockprice.php",
		method: "POST",
		data: {
			user: tmpl.user,
			pswd: tmpl.pswd,
			name: tmpl.stock_name,
			price: tmpl.stock_price
		},
		dataType: "json",
		failure: function() {
			tmpl.alert="Cannot connect to the server";
			document.querySelector("#window_alert").show();
		},
		success: function(data) {
			if (data.status=="success") {
				document.querySelector("#window_stockdetail").close();
				tmpl.toast = "New price for ["+tmpl.stock_name+"] ($"+tmpl.stock_price+") successfully committed.";
				document.querySelector("#toast").show();
				updateUserInfo();
			} else {
				tmpl.alert="Rejected: "+data.reason;
				document.querySelector("#window_alert").show();
			}
		}
	});
}

function createCurrency() {
	$.ajax({
		url: "admin/createCurrency.php",
		method: "POST",
		data: {
			user: tmpl.user,
			pswd: tmpl.pswd,
			name: tmpl.newmoney_name,
			description: document.querySelector("#newmoney_description /deep/ textarea").value,
		},
		dataType: "json",
		failure: function() {
			tmpl.alert = "Cannot connect to the server.";
			document.querySelector("#window_alert").open();
		},
		success: function(data) {
			if (data.status=="success") {
				tmpl.toast = "MoneyType created.";
				document.querySelector("#toast").show();
				tmpl.newmoney_name = "";
				document.querySelector("#newmoney_description /deep/ textarea").value = "";
				updateUserInfo();
			} else {
				tmpl.alert = "Rejected: "+data.reason;
				document.querySelector("#window_alert").open();
			}
		}
	});
}
function createUser() {
	console.log(1);
	$.ajax({
		url: "admin/createuser.php",
		method: "POST",
		data: {
			reg_user: tmpl.create_user,
			reg_pswd: tmpl.create_pswd,
			reg_perm: tmpl.create_grp,
			reg_funds: tmpl.create_funds,
			user: tmpl.user,
			pswd: tmpl.pswd
		},
		dataType: "json",
		failure: function() {
			tmpl.alert = "Cannot connect to the server.";
			document.querySelector("#window_alert").open();
		},
		success: function(data) {
			if (data.status=="success") {
				tmpl.toast = "User created.";
				document.querySelector("#toast").show();
				tmpl.create_lock = false;
				tmpl.create_user = "";
				tmpl.create_pswd = "";
				tmpl.create_grp = "user";
				tmpl.create_funds = const_initial_funds;
			} else {
				tmpl.alert = "Rejected: "+data.reason;
				document.querySelector("#window_alert").open();
			}
		}
	});
}

function createStock() {
	$.ajax({
		url: "admin/createstock.php",
		method: "POST",
		data: {
			user: tmpl.user,
			pswd: tmpl.pswd,
			name: tmpl.newstk_name,
			type: tmpl.newstk_type,
			description: document.querySelector("#newstk_description /deep/ textarea").value,
			price: tmpl.newstk_price,
			leverage: tmpl.newstk_leverage
		},
		dataType: "json",
		failure: function() {
			tmpl.alert = "Cannot connect to the server.";
			document.querySelector("#window_alert").open();
		},
		success: function(data) {
			if (data.status=="success") {
				tmpl.toast = "Stock created.";
				document.querySelector("#toast").show();
				tmpl.newstk_name = "";
				document.querySelector("#newstk_description /deep/ textarea").value = "";
				tmpl.newstk_price = "";
				updateUserInfo();
			} else {
				tmpl.alert = "Rejected: "+data.reason;
				document.querySelector("#window_alert").open();
			}
		}
	})
}

function finalize() {
	var bonus_data = new Object();
	for (var i=0; i<bonus_list.length; i++) {
		bonus_data[bonus_list[i]] = parseFloat(document.querySelector("#bonus_"+bonus_list[i]).value);
	}

	$.ajax({
		url:"/admin/finalize.php",
		method: "POST",
		data: {
			user: tmpl.user,
			pswd: tmpl.pswd,
			bonus: JSON.stringify(bonus_data)
		},
		dataType: "json",
		failure: function() {
				tmpl.alert = "Cannot reach the server";
				document.querySelector("#window_alert").open();
		},
		success: function(data) {
			if (data.status=="success") {
				tmpl.toast = "Finalize succeeded.";
				document.querySelector("#toast").show();
			} else {
				tmpl.alert = "Request Rejected: "+data.reason;
				document.querySelector("#window_alert").open();
			}
		}
	})
}


var updator = {
	tick_val: 0,
	interval: 300,
	task_pt: 0,
	task_cnt: 0,
	tasks: null,
	start: function() {
		this.tick_val = 0;
		this.interval = parseInt(tmpl.update_interval);
		try {
			this.tasks = JSON.parse(document.querySelector("#update_data").value);
		} catch(e) {
			tmpl.alert = "Invalid JSON: Syntax error.\nCheck console for more info";
			document.querySelector("#window_alert").open();
			console.log(e);
			return;
		}
		// Fill table
		var titlerow = document.createElement("tr");
		for (var key in this.tasks) {
			var cell = document.createElement("th");
			cell.innerHTML = key;
			titlerow.appendChild(cell);
			if (this.tasks[key].length>this.task_cnt) {
				this.task_cnt = this.tasks[key].length;
			}
		}
		document.querySelector("#update_tasks").appendChild(titlerow);
		for (var i=0; i<this.task_cnt; i++) {
			var row = document.createElement("tr");
			row.id = "updatetsk_r"+i;
			for (var key in this.tasks) {
				var cell = document.createElement("td");
				cell.innerHTML = this.tasks[key][i];
				row.appendChild(cell);
			}
			document.querySelector("#update_tasks").appendChild(row);
		}
		document.querySelector("#updator_startbtn").disabled=true;
		this.tick();
	},
	tick: function() {
		if (this.task_pt < this.task_cnt) {
			setTimeout(function(){updator.tick()},1000);
		} else {
			//document.querySelector("#update_log").innerHTML += "Update sequence completed.<br />Updator will continue<br />";
			setTimeout(function(){updator.tick()},1000);
			//document.querySelector("#update_log").innerHTML += "Debug: Restarting sequence.<br />";
			//this.task_pt = 0;
			//this.tick();
		}
		this.tick_val++;
		if (this.tick_val>=this.interval) {
			this.tick_val = 0;
			if (this.task_pt < this.task_cnt) {
				document.querySelector("#updatetsk_r"+this.task_pt).style.background = "#ccc";
				this.task_pt++;
			}
			this.update();
		}
		tmpl.update_progress = this.tick_val/this.interval*100;

	},
	ajaxPendingCount: 0,
	commitData: function() {
		if (this.task_pt < this.task_cnt) {
			this.ajaxPendingCount = 0;
			document.querySelector("#updatetsk_r"+this.task_pt).style.background = "yellow";
			for (var key in this.tasks) {
				this.ajaxPendingCount++;
				$.ajax({
					url: "admin/setstockprice.php",
					method: "POST",
					data: {
						user: tmpl.user,
						pswd: tmpl.pswd,
						name: key,
						price: this.tasks[key][this.task_pt]
					},
					dataType: "json",
					failure: function() {
						document.querySelector("#update_log").innerHTML += "Commit failed: Cannot connect to server.<br />";
					},
					success: function(data) {
						if (data.status=="success") {
							document.querySelector("#window_stockdetail").close();
							updator.ajaxPendingCount--;
							if (updator.ajaxPendingCount <= 0) {
								document.querySelector("#updatetsk_r"+updator.task_pt).style.background = "#66ff66";
							}
						} else {
							document.querySelector("#update_log").innerHTML += "Commit Rejected: "+data.reason+"<br />";
						}
					}
				});
			}
		}
	},
	update: function() {
		tmpl.ajaxLoading = true;
		$.ajax({
			url: "admin/update.php",
			method: "POST",
			data: {
				user: tmpl.user,
				pswd: tmpl.pswd
			},
			dataType: "json",
			failure: function() {
				document.querySelector("#update_log").innerHTML += "Update Failed: Cannot connect to the server<br />";
				tmpl.ajaxLoading = false;
			},
			success: function(data) {
				tmpl.ajaxLoading = false;
				if (data.status!="success") {
					document.querySelector("#update_log").innerHTML += "Update rejected: "+data.reason+"<br />";
				} else {
					updateUserInfo();
					updator.commitData();
				}
			}

		});
	}
}
