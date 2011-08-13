// Audio Player Controls, Containers, Info
var audElem = null;
var audAudio = null;
var audPlayer = null;
var audSeek = null;
var audTimePassed = null;
var audTimeLeft = null;
var lastValue = null;
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


////Utilities
//
// Returns mimetype from url
function getMime(url) {
	var mime = $.ajax({type: "HEAD",url: url, success: function(data, status, xhr){}}).getResponseHeader("Content-Type");
	return mime;
}

function withMinutes(seconds) {
	var minutes = Math.floor(seconds / 60);
	seconds -= Math.floor(minutes * 60);
	if(seconds.toString().length == 1) {
		seconds = ['0', seconds].join('');
	}
	if(minutes.toString().length == 1) {
		minutes = ['0', minutes].join('');
	}
	return [minutes, seconds].join(':');
}

// Updates time to the current time, otherwise updates to the specified time. Optionally seeks to that time
function updateTime(time, seek) {
	if(!time) {
		time = Math.floor(audElem.currentTime);
	}
	// time is time we're currently at/seeking to
	audTimePassed.html(withMinutes(Math.floor(time)));
	audTimeLeft.html(withMinutes(Math.floor(Math.floor(audElem.duration) - Math.floor(time))));
	//audSeek.value = time;
	if(seek && audElem.currentTime != time) {
		audElem.currentTime = time;
	}
}


////Startup
//
// Set up some elements + variables
function audSetup() {
	audAudio = $('#audAudio');
	audAudio.html('<audio id="aud2Audio" src="http://theanti9.com/Angelica.mp3" autobuffer></audio>');
	audElem = document.getElementById('aud2Audio');
	audTimePassed = $('#audTimePassed');
	audTimeLeft = $('#audTimeLeft');
	audPlayer = $('#audPlayer');
	audSeek = $('#audSeek');
	audBindEvents();
}

// Checks if audio element - and possible mimetypes - are supported by the browser
function audSupportCheck() {
	if(!!document.createElement('audio').canPlayType) {
		audioSupported = true;
		var mimes = ['audio/mpeg;', 'audio/ogg; codecs="vorbis"', 'audio/wav; codecs="1"', 'audio/mp4; codecs="mp4a.40.2"'];
		for(i=0;i<mimes.length;++i) {
			if(!!(audElem.canPlayType(mimes[i]).replace(/no/, ''))) {
				mimesSupported.push(mimes[i].substring(0, mimes[i].indexOf(';')));
			}
			else {
				mimesUnsupported.push(mimes[i].substring(0, mimes[i].indexOf(';')));
			}
		}
	}
	else {
		$('audPlayer').html('<p class="ui-state-error">Sorry, your browser does not support the audio element.</p>').fadeIn();
	}
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
			$("#audPlPa").html("Pause");
		}
		else {
			audElem.pause();
			$("#audPlPa").html("Play");
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
	
	//// HTML5 audio events
	//
	$(audElem).bind("timeupdate", function(){
		updateTime();
	});

	$(audElem).bind("loadedmetadata", function(){
		updateTime();
		audSupportCheck();
		audNewSeeker();
		if(audioSupported && mimesSupported.length){
			audInit();
		}
		else {
			audPlayer.html('<div class="ui-state-error">Sorry, your browser does not support the HTML5 audio tag.</div>');
		}
	});
}

function audNewSeeker() {
	$("#audSeekCont").html('<div class="audCont" id="audSeek"></div>');
	$('#audSeek').slider({max: Math.floor(audElem.duration),
		start: function(event, ui) {
			audElem.seeking = true;
			// Set lastValue to where we currently are
			lastValue = ui.value;
		},
		slide: function(event, ui) {
			// WhereWeAreNow - WhereWeWereLastMove = HowMuchWeMoved
			var seekJump = ui.value - lastValue;
			// Seek to WhereWeAreNow +- HowMuchWeMoved
			updateTime(ui.value + seekJump, true);
			lastValue = ui.value;
		},
		stop: function(event, ui) {
			updateTime(ui.value, true);
			lastValue = ui.value;
			audElem.seeking = false;
		}
	});
}

$(document).ready(function(){
	if($.browser.msie) {
		window.location.href = "html/ie/html";
	}
	audSetup();
});