'use strict';
Object.defineProperty(exports, "__esModule", { value: true });
var socketio = require("socket.io");
var Socket = (function () {
    function Socket(http, db, secret, log) {
        this.http = http;
        this.db = db;
        this.secret = secret;
        this.log = log;
        this.io = socketio.listen(this.http);
    }
    Object.defineProperty(Socket.prototype, "IO", {
        get: function () { return this.io; },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(Socket.prototype, "DB", {
        get: function () { return this.db; },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(Socket.prototype, "Secret", {
        get: function () { return this.secret; },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(Socket.prototype, "Log", {
        get: function () { return this.log; },
        enumerable: true,
        configurable: true
    });
    return Socket;
}());
exports.Socket = Socket;
