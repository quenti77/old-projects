'use strict';
Object.defineProperty(exports, "__esModule", { value: true });
// Les modules externes
var http = require("http");
var r = require("rethinkdb");
var log4js = require("log4js");
var Socket_1 = require("./routes/Socket");
var base_1 = require("./sockets/base");
// On charge les settings depuis les variables d'environnement
var HTTP_PORT = parseInt(process.env.HTTP_PORT) || 7331;
var JWT_SECRET = process.env.JWT_SECRET || 'D1sQussS3rv3r';
// Création du logger
log4js.configure({
    appenders: {
        out: { type: 'stdout' },
        app: { type: 'file', filename: 'server-socket.log' }
    },
    categories: {
        'default': { appenders: ['out', 'app'], level: 'debug' }
    }
});
var log = log4js.getLogger('socket');
// Création de l'application
log.debug('Loading Socket ...');
log.debug('Connecting to database : rethinkdb://127.0.0.1:28015/disquss');
var p = r.connect({
    db: 'disquss'
});
p.then(function (conn) {
    // Quand on est connecté à la DB on peut continuer le lancement
    log.info("Connected to the db");
    var server = http.createServer(function (req, res) {
        // res.end('SOCKET.IO')
    });
    // Nos routes SocketIO
    var socket = new Socket_1.Socket(server, { r: r, conn: conn }, JWT_SECRET, log);
    base_1.base(socket);
    // On lance l'application
    server.listen(HTTP_PORT);
});
p.error(function (error) {
    log.error("Connected to the db failed : " + error);
});
