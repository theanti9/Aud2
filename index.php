<?php 
session_start();
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<link rel="stylesheet" type="text/css" href="css/base.css" />
		<link rel="stylesheet" type="text/css" href="css/cupertino/jquery-ui-1.8.15.custom.css" />
		<link rel="stylesheet" type="text/css" href="css/jquery.fileupload-ui.css" />
	</head>
	<body>
		<div class="audPage" id="audPageRegister">
			<div>
				<form id="audRegister" action="/views/register.php" method="POST">
					<h2>Register</h2><br />
					<label>Username:</label><input id="regUser" type="text" name="username" />
					<label>Password:</label><input id="regPass" type="password" name="password" /><br />
					<label>Confirm:</label><input id="regConf" type="password" name="confirm" /><br />
					<input id="regSub" type="submit" name="submit" value="Register" /><br />
				</form>
				<button id="audLoginButton">Or Login</button>
			</div>
		</div>
		<div class="audPage" id="audPageLogin">
			<div>
				<form id="audLogin" action="/views/login.php" method="POST">
					<h2>Login</h2><br />
					<label>Username:</label><input id="logUser" type="text" name="username" />
					<label>Password:</label><input id="logPass" type="password" name="password" />
					<input id="logSub" type="submit" name="submit" value="Login" /><br />
				</form>
				<button id="audRegButton">Or Register</button>
			</div>
		</div>
		<div class="audPage" id="audPageLoading">
			<div><h1>Aud2 is loading...</h1></p></div>
		</div>
		<div class="audPage" id="audPageLoaded">
			<div id="audPlayer">
				<div id="audAudio">
					<!-- HTML5 Audio Element -->
				</div>
				<div id="audNowMeta">
					<span id="audCurArtist" class="audCont">Artist</span> - <span id="audCurAlbum" class="audCont">Album</span> - <span id="audCurSong" class="audCont">Song</span>
				</div>
				<div id="audControls">
					<button id="audPrev">&lt;</button> <button id="audPlPa">Play</button> <button id="audNext">&gt;</button>
					<br /><span class="audCont audTime" id="audTimePassed">00:00</span> <div class="audCont" id="audSeekCont"><div id="audSeek"></div></div> <span class="audCont audTime">-<span class="audCont" id="audTimeLeft">00:00</span></span><div class="audCont" id="audVolCont"><button id="audVolButton"></button><div id="audVol"></div></div>
				</div>
			</div>
			<button id="audUpload">Upload Music</button>
			<div id="audMusicUpload" title="Upload Music">
				<div id="fileupload">
					<form action="/views/upload.php" method="POST" id="audMusicUploadForm" enctype="multipart/form-data">
						<div class="fileupload-buttonbar">
							<label class="fileinput-button">
								<span>Add songs...</span>
								<input type="file" name="files[]" multiple>
							</label>
							<button type="submit" class="start">Start upload</button>
							<button type="reset" class="cancel">Cancel upload</button>
						</div>
					</form>
					<div class="fileupload-content">
						<table class="files"></table>
						<div class="fileupload-progressbar"></div>
					</div>
				</div>
			</div>
			<div id="audLibrary">
				<h3>Aud Library</h3>
				<table>
					<thead id="audLibHead">
						<tr>
							<th>#</th><th>Title</th><th>Artist</th><th>Album</th>
						</tr>
					</thead>
					<tbody id="audLibBody">
					</tbody>
				</table>
			</div>
		</div>
	</body>
	<footer>
		<script src="js/jquery-1.6.2.min.js"></script>
		<script src="js/jquery-ui-1.8.15.custom.min.js"></script>
		<script src="js/jquery.tmpl.min.js"></script>
		<script id="template-upload" type="text/x-jquery-tmpl">
			<tr class="template-upload{{if error}} ui-state-error{{/if}}">
				<td class="preview"></td>
				<td class="name">${name}</td>
				<td class="size">${sizef}</td>
				{{if error}}
					<td class="error" colspan="2">Error:
						{{if error === 'maxFileSize'}}File is too big
						{{else error === 'minFileSize'}}File is too small
						{{else error === 'acceptFileTypes'}}Filetype not allowed
						{{else error === 'maxNumberOfFiles'}}Max number of files exceeded
						{{else}}${error}
						{{/if}}
					</td>
				{{else}}
					<td class="progress"><div></div></td>
					<td class="start"><button>Start</button></td>
				{{/if}}
				<td class="cancel"><button>Cancel</button></td>
			</tr>
		</script>
		<script id="template-download" type="text/x-jquery-tmpl">
			<tr class="template-download{{if error}} ui-state-error{{/if}}">
				{{if error}}
					<td></td>
					<td class="name">${name}</td>
					<td class="size">${sizef}</td>
					<td class="error" colspan="2">Error:
						{{if error === 1}}File exceeds upload_max_filesize (php.ini directive)
						{{else error === 2}}File exceeds MAX_FILE_SIZE (HTML form directive)
						{{else error === 3}}File was only partially uploaded
						{{else error === 4}}No File was uploaded
						{{else error === 5}}Missing a temporary folder
						{{else error === 6}}Failed to write file to disk
						{{else error === 7}}File upload stopped by extension
						{{else error === 'maxFileSize'}}File is too big
						{{else error === 'minFileSize'}}File is too small
						{{else error === 'acceptFileTypes'}}Filetype not allowed
						{{else error === 'maxNumberOfFiles'}}Max number of files exceeded
						{{else error === 'uploadedBytes'}}Uploaded bytes exceed file size
						{{else error === 'emptyResult'}}Empty file upload result
						{{else}}${error}
						{{/if}}
					</td>
				{{else}}
					<td class="preview">
						{{if thumbnail_url}}
							<a href="${url}" target="_blank"><img src="${thumbnail_url}"></a>
						{{/if}}
					</td>
					<td class="name">
						<a href="${url}"{{if thumbnail_url}} target="_blank"{{/if}}>${name}</a>
					</td>
					<td class="size">${sizef}</td>
					<td colspan="2"></td>
				{{/if}}
				<td class="delete">
					<button data-type="${delete_type}" data-url="${delete_url}">Delete</button>
				</td>
			</tr>
		</script>
		<script src="js/jquery.form.js"></script>
		<script src="js/jquery.fileupload.js"></script>
		<script src="js/jquery.fileupload-ui.js"></script>
		<script src="js/jquery.iframe-transport.js"></script>
		<script src="js/application.js"></script>
		<script src="js/AudPreloader.js"></script>
	</footer>
</html>