'use strict';

const io = require('socket.io-client');

let socket = io('http://localhost:3000');
let loggedIn = false;
let popupPreLogout = false;
let preLogoutHandler;

// socket connection
let p1 = new Promise((resolve) => {
	socket.on('connect', () => {
		socket.on('disconnect', () => {
			console.log('You have benn disconnected');
		});
		resolve();
	});
});

// page ready
let p2 = new Promise((resolve) => {
// on page ready
	window.addEventListener('load', function () {
		resolve();
	});
});

Promise.all([p1, p2])
	.then(() => {
		let appId = document.getElementById('js-infos').getAttribute('data-this-app');
		let deviceToken = document.getElementById('js-infos').getAttribute('data-device-token');
		socket.emit('handshake', {appId: appId, deviceToken: deviceToken});

		// authenticated
		socket.on('handshakeOK', () => {
			loggedIn = true;

			// logout button
			document.getElementById('logout').addEventListener('click', () => {
				socket.emit('logout');
				socket.close();
			});

			// popup disconnected in 30 sec
			socket.on('preLogout', function (countDown) {
				popupPreLogout = true;
				preLogoutHandler = setInterval(() => {
					countDown--;
					console.log(countDown);
				}, 1000);
			});

			// popup logged out
			socket.on('logout', () => {
				clearInterval(preLogoutHandler);
				loggedIn = false;
				socket.close();
				alert('logged out');
				window.location.reload();
			});

			// ping server
			let betweenAliveTime = 2000;
			let lastAlive = Date.now();
			window.addEventListener('mousemove', () => {
				if (loggedIn === true) {
					if (popupPreLogout) {
						clearInterval(preLogoutHandler);
						popupPreLogout = false;
					}
					if (Date.now() - lastAlive > betweenAliveTime) {
						lastAlive = Date.now();
						socket.emit('alive', {});
					}
				}
			});
		});

	})
	.catch(err => {
		console.log(err);
	});

module.exports = socket;
