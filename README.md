About Aud2
==========

Aud2 is an open source HTML5 media player made using the HTML5 audio element, Javascript + JQuery, and PHP.

Dependencies
============

Client:
* An HTML5 + Javascript capable web browser. Aud2 is tested to work in the latest versions of Chrome, Firefox, and Safari. Internet Explorer is not supported.

Server:
* PHP4 or newer (also see "Notes" section)

Javascript/Jquery Libraries:
* [JQuery](http://jquery.com/) (Included)
* [JQueryUI](http://jqueryui.com/) (Included w/Theme)
* [JQuery Templates Beta](http://api.jquery.com/category/plugins/templates/) (Included)
* [JQuery DataTables plugin](http://datatables.net/) (Included)
* [JEditable JQuery plugin](http://www.appelsiini.net/projects/jeditable) (Included)
* [JQuery Multiple File Upload Widget](https://github.com/blueimp/jQuery-File-Upload) (Included, modified)
* [JQuery Ajax Form plugin](http://www.malsup.com/jquery/form/) (Included)

Feature List
============
\* is completed, + is in progress, ~ is planned

* \*Registration/Logins
* ~Sessions
* \*Audio file uploads
* \*Basic Audio Controls (Play/Pause, Next, Previous, Seeking, Volume, Time Elapsed, Time Left, Buffer Bar, Mute/Unmute)
* +Shuffle
* +Repeat All/Repeat One
* +Music Library
* \*Basic ID3 read support
* ~Full ID3 read support
* -Album Artwork display
* -Current Artist/Album/Song display
* ~Playlists
* ~Non-Flash Visualizer
* -Per-user play statistics

Notes
=====

### File Upload Problems
If you have issues uploading files after deploying Aud2, you may have to tweak some php.ini settings in order to get it to work. Mainly, increasing the values of upload_max_filesize, memory_limit, max_input_time, max_file_uploads, and post_max_size. See the doc page on [Common File Upload Pitfalls](http://www.php.net/manual/en/features.file-upload.common-pitfalls.php) for more information.

License
=======

The open source license for Aud2 has not been decided yet.