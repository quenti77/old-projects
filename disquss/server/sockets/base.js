'use strict';
Object.defineProperty(exports, "__esModule", { value: true });
var channels_1 = require("./channels");
var base = function (instance) {
    var IO = instance.IO, DB = instance.DB, Log = instance.Log;
    IO.on('connection', function (client) {
        channels_1.channels(instance, client);
    });
};
exports.base = base;
