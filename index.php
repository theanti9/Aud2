<!DOCTYPE html>
<html lang="en">
	<head>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js"></script>
		<script src="js/Aud2.js"></script>
	</head>
	<body>
		<div id="controls">
			<audio id="aud2Audio" controls autobuffer>
				<source src="">
				Audio tag not supported
			</audio>
			<button id="btnPrev">&lt;</button>
			<button id="btnPlPa">Play</button>
			<button id="btnNext">&gt;</button>
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