// Elements
var audElem = null;
var audAudio = null;
var audPlayer = null;
var audSeek = null;
var audBuffer = null;
var audPlPa = null;
var audTimePassed = null;
var audTimeLeft = null;

// Statistics
var songSeconds = 0;
var sessionSongs = 0;

// Other Vars
var lastValue = null;
var seekPaused = null;
var audioSupported = false;
var mimesSupported = [];
var mimesUnsupported = [];
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
	if(seconds != Math.abs(seconds) || minutes != Math.abs(minutes)) {
		return "00:00";
	}
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

	//Prevent seeking past buffer length
	if(time >= audElem.buffered.end(0)) {
		time = Math.floor(audElem.buffered.end(0)) - 1;
	}

	var passed = withMinutes(Math.floor(time));
	var left = withMinutes(Math.floor(Math.floor(audElem.duration) - Math.floor(time)));
	if(passed && left) {
		audTimePassed.html(passed);
		audTimeLeft.html(left);
		//audSeek.value = time;
		if(seek && audElem.currentTime != time) {
			audElem.currentTime = time;
		}
	}
}

function saveStats(callback) {
	if(songSeconds >= 0.80*audElem.duration) {
		sessionSongs++;
	}
	songSeconds = 0;

	// TODO: Send sessionSongs + songSeconds to PHP

	sessionSongs = 0;
	if (callback && typeof(callback) === "function") {
		callback();
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
	audPlPa = $("#audPlPa");
	audSeek = $('#audSeek');
	audBuffer = $("#audBuffer");
	audBindEvents();
}

// Checks if audio element - and possible mimetypes - are supported by the browser
function audSupportCheck() {
	if(!!document.createElement('audio').canPlayType) {
		audioSupported = true;
		var mimes = ['audio/mpeg;', 'audio/mp3;', 'audio/ogg; codecs="vorbis"', 'audio/wav; codecs="1"', 'audio/mp4; codecs="mp4a.40.2"'];
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
	$("#audPageLoaded").fadeIn("slow", function(){
		audPlayer.animate({'opacity': '1.0'});
	});
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

//// Other
//
function changeSong(src) {
	saveStats(function() {
		var mime = getMime(url);
		if(mime in mimesSupported) {
			audElem.pause();
			audElem.src = src;
			audElem.play();
		}
		else {
			audAudio.append(['<div class="ui-state-error">Your browser does not support the audio type "', mime, '"</div>'].join(''))
		}
	});
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

	// Volume Slider
	$("#audVol").slider({max: 1, step:0.01, value:0.5, orientation:'vertical', animate:'fast',
		start: function(event, ui) {
			audElem.volume = ui.value;
		},
		slide: function(event, ui) {
			audElem.volume = ui.value;
		},
		stop: function(event, ui) {
			audElem.volume = ui.value;
		}
	});

	// Mousedown + Mouseup for seeking
	$("#audSeekCont").mousedown(function(){
		if(!audElem.paused) {
			seekPaused = true;
			audPlPa.trigger('click');
		}
		audElem.seeking = true;
		updateTime($("#audSeek").slider('value'));
	})

	$(document).mouseup(function(){
		audElem.seeking = false;
		if(audElem.paused && seekPaused) {
			seekPaused = false;
			audPlPa.trigger('click');
		}
	})

	// Save statistics before user leaves the page
	window.onbeforeunload = saveStats();

	// Initiate ajax form submit plugin
	$("#audUpload").ajaxForm();
	
	//// HTML5 audio events
	//
	$(audElem).bind("timeupdate", function(){
		if(!audElem.seeking && !audElem.paused) {
			updateTime();
			$("#audSeek").slider("value", Math.floor(audElem.currentTime));
			songSeconds++;
		}
	});

	$(audElem).bind("loadedmetadata", function(){
		updateTime();
		audSupportCheck();
		audNewSeeker();
		audElem.volume = 0.5;
		if(audioSupported && mimesSupported.length){
			audInit();
		}
		else {
			audPlayer.html('<div class="ui-state-error">Sorry, your browser does not support the HTML5 audio tag.</div>');
		}
	});

	
	$(audElem).bind("loadstart", function(){ // When the media starts loading
		if(audElem.buffered != undefined) { // If browser supports .buffered
			$(audElem).bind("progress", function(){ // Every time we add to the buffer
				if(audElem.buffered.length != 0) {
					audBuffer.animate({'width': [(audElem.buffered.end(0)/audElem.duration)*100, '%'].join('')});
				}
				else { // length == 0 if fully cached
					audBuffer.animate({'width': '100%'});
				}
			});
		}
	});
}

function audNewSeeker(repl) {
	if(repl) {
		$("#audSeekCont").html('<div class="audCont" id="audSeek"></div>');
	}
	audSeek.slider({value: 0.5, max: Math.floor(audElem.duration), animate:'fast',
		start: function(event, ui) {
			if(ui.value > audElem.buffered.end(0)) { // Prevent slide past buffer
				return false;
			}
			// Set lastValue to where we currently are
			lastValue = ui.value;
		},
		slide: function(event, ui) {
			if(ui.value > audElem.buffered.end(0)) { // Prevent slide past buffer
				return false;
			}
			// WhereWeAreNow - WhereWeWereLastMove = HowMuchWeMoved
			var seekJump = ui.value - lastValue;
			// Seek to WhereWeAreNow +- HowMuchWeMoved
			updateTime(ui.value + seekJump, true);
			lastValue = ui.value;
		},
		stop: function(event, ui) {
			if(ui.value > audElem.buffered.end(0)) { // Prevent slide past buffer
				return false;
			}
			updateTime(ui.value, true);
			lastValue = ui.value;
			
		}
	});
	audSeek.append('<span class="audCont" id="audBuffer"></span>');
	audBuffer = $('#audBuffer');
}

$(document).ready(function(){
	if($.browser.msie) {
		alert("IE Not Supported");
	}
	audSetup();
});