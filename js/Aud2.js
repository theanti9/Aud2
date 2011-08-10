var audElem = null;
var audState = false; // true -> playin, false -> paused/stopped
var curSongIndex = 0;
var userid = null;
var libraryJson = null; // Whole library as one.
var curPlaylist = null; // Keep what is currently playing. Subset of libraryJson


function setupAud() {
	audElem = document.getElementById('aud2Audio');
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
	if (audElem = null) {
		alert("Error: no audio element!");
	}
	audElem.pause();
	audElem.src = src;
	audElem.play();
}