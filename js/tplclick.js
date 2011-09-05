(function($){

  // Click speed threshold, defaults to 500.
  $.tplclickThreshold = 500;

  // Special event definition.
  $.event.special.tplclick = {
	setup: function(data) {
	  // When the event is first bound, initialize the element plugin data
	  // (including clicks counter, last-clicked timestamp, and a threshold
	  // value if specified), and bind the "click" event handler that will
	  // be used to power the custom "tplclick" event.
	  $(this)
		.data('tplclick', { clicks: 0, last: 0, threshold: data })
		.bind('click', click_handler);
	},
	teardown: function() {
	  // When the last event is unbound, remove all element plugin data and
	  // unbind the "click" event handler.
	  $(this)
		.removeData('tplclick')
		.unbind('click', click_handler);
	}
  };

  // This function is executed every time an element is clicked.
  function click_handler(event) {
	var elem = $(this),

	  // Get plugin data stored on the element.
	  data = elem.data('tplclick'),

	  // Use the specified threshold, otherwise use the global value.
	  threshold = data.threshold || $.tplclickThreshold;

	// If more than `threshold` time has passed since the last click, reset
	// the clicks counter.
	if ( event.timeStamp - data.last > threshold ) {
	  data.clicks = 0;
	}

	// Update the element's last-clicked timestamp.
	data.last = event.timeStamp;

	// Increment the clicks counter. If the counter has reached 3, trigger
	// the "tplclick" event and reset the clicks counter to 0. Trigger
	// bound handlers using triggerHandler so the event doesn't propagate.
	if (++data.clicks === 3) {
	  elem.triggerHandler('tplclick');
	  data.clicks = 0;
	}
  };

})(jQuery);