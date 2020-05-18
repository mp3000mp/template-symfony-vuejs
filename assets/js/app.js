"use strict";

// css
require('../css/app.scss');

// jquery
window.$ = require('jquery');

// bootstrap
require('bootstrap');

// socket
require('../modules/device-session/device-session');

// on page ready
window.addEventListener('load', function () {

	// socket modules
	require('../modules/device-session/status-session');

});
