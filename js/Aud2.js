
// Audio Player Controls, Containers, Info
var audElem = null;
var audAudio = null;
var audPlayer = null;
var audSeek = null;
var audTimePassed = null;
var audTimeLeft = null;
var lastSeekIndex = null;
var audioSupported = false;
var mimesSupported = new Array();
var mimesUnsupported = new Array();
//// Useful audElem properties:
// duration
// currentTime
// currentSrc
// volume
// ended
// seeking
// buffered
// paused

// Aud2 Library Vars
var curSongIndex = 0;
var userid = null;
var libraryJson = null; // Whole library as one.
var curPlaylist = null; // Keep what is currently playing. Subset of libraryJson

// Returns mimetype from url
function getMime(url) {
	var mime = $.ajax({type: "HEAD",url: url, success: function(data, status, xhr){}}).getResponseHeader("Content-Type");
	return mime;
}

function withMinutes(seconds) {
	var minutes = (Math.floor(seconds / 60)).toString();
	seconds -= (minutes * 60).toString();
	if(seconds.length == 1) {
		seconds = ['0', seconds].join('');
	}
	if(minutes.length == 1) {
		minutes = ['0', minutes].join('');
	}
	return [minutes, seconds].join(':')
}

// Updates time to the current time, otherwise updates to the specified time. Optionally seeks to that time
function updateTime(time, seek) {
	if(!time) {
		time = audElem.currentTime;
	}
	// time is time we're currently at/seeking to
	audTimePassed.html(withMinutes(time));
	audTimeLeft.html(withMinutes(Math.floor(audElem.duration - time)));
	//audSeek.value = time;
	if(seek && audElem.currentTime != time) {
		audElem.currentTime = time;
	}
}

// Checks if audio element - and possible mimetypes - are supported by the browser
function audSupportCheck() {
	if(!!document.createElement('audio').canPlayType) {
		audioSupported = true;
		while(!audElem.buffered) {
			
		}
		var mimes = ['audio/mpeg;', 'audio/ogg; codecs="vorbis"', 'audio/wav; codecs="1"', 'audio/mp4; codecs="mp4a.40.2"'];
		for(var type in mimes) {
			if(!!(audElem.canPlayType(type).replace(/no/, ''))) {
				mimesSupported.push(type.substring(0, type.indexOf(';')));
			}
			else {
				mimesUnsupported.push(type.substring(0, type.indexOf(';')));
			}
		}
	}
	else {
		$('audPlayer').html('<p class="ui-state-error">Sorry, your browser does not support the audio element.</p>').fadeIn();
	}
}

// Set up some elements + variables
function audSetup() {
	audElem = document.getElementById('aud2Audio');
	audElem.innerHTML = '<audio id="aud2Audio" src="http://upload.wikimedia.org/wikipedia/commons/a/a9/Tromboon-sample.ogg" ontimeupdate="updateTime();" autobuffer></audio>';
	audTimePassed = $('#audTimePassed');
	audTimeLeft = $('#audTimeLeft');
	audAudio = $('#audAudio');
	audPlayer = $('#audPlayer');
	audSeek = $('#audSeek');
}

function audInit() {
	audPlayer.fadeIn();
}

function makeInitRequests() {
	// Grab the library
	$.post('aud2.php', { action:'getLibrary', userid:userid }, function(data) {
		libraryJson = data;
		curPlayList = data;
		var tbl = [];
		// Generate the library table
		$.each(data, function(i,v) {
			tbl.push(["<tr><td>",i,"</td><td>",v.title,"</td><td>",v.artist,"</td><td>",v.album,"</td></tr>"].join(''));
		});
		// Output
		$('#audLibBody').html(tbl.join(''));
	});
}

function changeSong(src) {
	var mime = getMime(url);
	if(mime in mimesSupported) {
		audElem.pause();
		audElem.src = src;
		audElem.play();
	}
	else {
		audAudio.append(['<div class="ui-state-error">Your browser does not support the audio type "', mime, '"</div>'].join(''))
	}
	
}

// Bind some JQuery + HTML5 event functions
function audBindEvents() {
	
	//// Clicks
	//
	// Play-Pause Button
	$('#audPlPa').click(function() {
		if(audElem.paused) {
			audElem.play();
		}
		else {
			audElem.pause();
		}
	});

	// Next button
	$('#audNext').click(function() {
		curSongIndex++;
		changeSong(curPlayList[curSongIndex].songpath);
	});

	// Previous button
	$('#audPrev').click(function() {
		curSongIndex--;
		changeSong(curPlayList[curSongIndex].songpath);
	});

	////Sliders
	//
	$('audSeek').slider({max: Math.floor(audElem.duration),
		start: function(event, ui) {
			console.log(ui.handle);
			audElem.seeking = true;
			lastSeekIndex = audElem.currentTime;
			updateTime();
		},
		slide: function(event, ui) {
			var seekJump = ui.value - lastSeekIndex
			updateTime(audElem.currentTime + seekJump);
		},
		stop: function(event, ui) {
			updateTime();
			audElem.seeking = false;
		}
	});
	
	//// HTML5 audio events
	//

}


$(document).ready(function(){
	audSetup();
	audSupportCheck();
	if(audioSupported && mimesSupported.length){
		audInit();
	}
});