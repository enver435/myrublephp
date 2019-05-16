const nmap = require('libnmap');

nmap.discover(function (err, report) {
    if (err) throw new Error(err);

    for (let item in report) {
        console.log(JSON.stringify(report[item]));
    }
});