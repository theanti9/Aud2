<!DOCTYPE html>
<html lang="en">
	<head>
		<script src="js/jquery-1.6.2.min.js"></script>
		<script src="jquery-ui-1.8.15.custom.min.js"></script>
		<script src="js/Aud2.js"></script>

		<link rel="stylesheet" type="text/css" href="css/base.css" />
		<link rel="stylesheet" type="text/css" href="css/cupertino/jquery-ui-1.8.15.custom.css" />

	</head>
	<body>
		<div id="controls">
			<audio id="aud2Audio" src="http://upload.wikimedia.org/wikipedia/commons/a/a9/Tromboon-sample.ogg" ontimeupdate="updateTime();" autobuffer>
				Audio tag not supported
			</audio>
			<button id="btnPrev">&lt;</button>
			<button id="btnPlPa">Play</button>
			<button id="btnNext">&gt;</button>
			<br /><div id="audSeek"></div><span id="audTime"></span>
		</div>
		<div id="library">
			<table>
				<thead>
					<tr>
						<th>#</th><th>Title</th><th>Artist</th><th>Album</th>
					</tr>
				</thead>
				<tbody id="tblLibBody">
				</tbody>
			</table>
		</div>
	</body>
</html>