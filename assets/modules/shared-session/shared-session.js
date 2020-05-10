'use strict';

const io = require('socket.io-client');

let socket = io('http://localhost:3000');

// socket connection
let p1 = new Promise((resolve, reject) => {
	socket.on('connect', () => {
		socket.on('disconnect', () => {
			console.log('You have benn disconnected');
		});
		socket.on('logout', () =>{
		  alert('timeout');
		});
		resolve();
	});
});

// page ready
let p2 = new Promise((resolve, reject) => {
// on page ready
	window.addEventListener('load', function () {
		resolve();
	});
});

Promise.all([p1, p2])
	.then(([]) => {
		let token = document.getElementById('js-infos').getAttribute('data-token');
		socket.emit('handshake', {token: token});
	})
	.catch(err => {
		console.log(err);
	});

module.exports = socket;
