<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" >
	<title>NITIC Scheduler Window</title>
	<style type="text/css">
	#logs{
		display: block;
		background: #eee;
		padding: 3%;
		margin: 5%;
		font-family: monospace;
		color: black;
	}
	</style>
	<script type="text/javascript" src="../bower_components/jquery/dist/jquery.min.js"></script>
	<script type="text/javascript">
	var user = "<?php echo $_REQUEST['user'];?>";
	var pswd = "<?php echo $_REQUEST['pswd'];?>";
	var interval = "<?php echo $_REQUEST['interval'];?>";
	var tick_val = 0;
	function log(msg) {
		document.querySelector("#logs").innerHTML+=msg+"<Br />\n";
	}
	function tick() {
		setTimeout(tick,1000);
		tick_val++;
		if (tick_val>=interval) {
			tick_val = 0;
			update();
		}
		document.querySelector("progress").value = tick_val;
	}
	function update () {
		$.ajax({
		url: "update.php",
		method: "POST",
		data: {
			user: user,
			pswd: pswd
		},
		dataType: "json",
		failure: function() {
			log("Ajax Request failed.");
		},
		success: function(data) {
			if (data.status!="success") {
				log("Update fail: "+data.reason);
			} else {
				log("Changes updated.");
			}
		}
	})


	}
	</script>
</head>
<body>
	<h2>NITIC Periodic Update Scheduler</h2>
	<h3>Do NOT close this window during trading session.</h3>
	<h3>Leave ONE AND ONLY ONE window running THROUGHOUT THE SESSION.</h3>
	<hr />
	<progress value="0" max="<?php echo $_REQUEST['interval'];?>" style="width:100%"></progress>
	<hr />
	<p id="logs">
		Update interval is <?php echo $_REQUEST['interval'];?> seconds.
	</p>
	<script type="text/javascript">
	tick();
	</script>
</body>
</html>
