var audElem = null;
var audTime = null;
var audState = false; // true -> playin, false -> paused/stopped
var curSongIndex = 0;
var userid = null;
var libraryJson = null; // Whole library as one.
var curPlaylist = null; // Keep what is currently playing. Subset of libraryJson

var getKeys = function(obj){
   var keys = [];
   for(var key in obj){
      keys.push(key);
   }
   return keys;
}

function setupAud() {
	audElem = document.getElementById('aud2Audio');
	audTime = document.getElementById('audTime');
	setActions();
	makeInitRequests();
}

function setActions() {

	// Play/Pause button
	$('#btnPlPa').click(function() {
		if (audState) {
			audElem.pause();
			audState = false;
		} else {
			audElem.play();
			audState = true;
		}
	});

	// Next button
	$('#btnNext').click(function() {
		curSongIndex++;
		changeSong(curPlayList[curSongIndex].songpath);
	});

	// Previous button
	$('#btnPrev').click(function() {
		curSongIndex--;
		changeSong(curPlayList[curSongIndex].songpath);
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
		$('#tblLibBody').html(tbl.join(''));
	});
}

function changeSong(src) {
	if(audElem == null) {
		alert("Error: no audio element!");
	}
	audElem.pause();
	audElem.src = src;
	audElem.play();
}

function updateTime() {
	audTime.innerHTML = Math.floor(audElem.currentTime) + "/" + Math.floor(audElem.duration)
}

function sliderSetup() {
	console.log(audElem.buffered);
	//Initialize Seeking-slider
	$("#audSeek").slider({max:Math.floor(audElem.duration), slide: function(event, ui){
		audTime.innerHTML = Math.floor(ui.value) + "/" + Math.floor(audElem.duration)
	}});
}

$(document).ready(function(){
	setupAud();
	sliderSetup();

});