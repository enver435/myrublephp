// const find = require('local-devices');

// // Find all local network devices.
// find().then(devices => {
//   console.log(devices);
// })

// const netList = require('network-list');

// netList.scanEach({}, (err, obj) => {
//     console.log(obj); // device object
// });

const getmac = require('getmac');
const http = require('http');

// create server
http.createServer(function (req, res) {
    getmac.getMac(function (err, macAddress) {
        if (err) throw err

        res.write('Mac Address: ' + macAddress);
        res.end();
    });
}).listen(8080);