'use strict';
Object.defineProperty(exports, "__esModule", { value: true });
// Les modules externes
var express = require("express");
var bodyParser = require("body-parser");
var r = require("rethinkdb");
var log4js = require("log4js");
// Nos routes
var Auth_1 = require("./routes/Auth");
var Accounts_1 = require("./routes/Accounts");
// On charge les settings depuis les variables d'environnement
var HTTP_PORT = parseInt(process.env.HTTP_PORT) || 1337;
var JWT_SECRET = process.env.JWT_SECRET || 'D1sQussS3rv3r';
// Création du logger
log4js.configure({
    appenders: {
        out: { type: 'stdout' },
        app: { type: 'file', filename: 'server-api.log' }
    },
    categories: {
        'default': { appenders: ['out', 'app'], level: 'debug' }
    }
});
var log = log4js.getLogger('api');
// Création de l'application
log.debug('Loading API ...');
var app = express();
// Désactivation d'un header
app.disable('x-powered-by');
// Ajout des middlewares
app.use(bodyParser.urlencoded({ extended: true }));
app.use(bodyParser.json());
log.debug('Connecting to database : rethinkdb://127.0.0.1:28015/disquss');
var p = r.connect({
    db: 'disquss'
});
p.then(function (conn) {
    // Quand on est connecté à la DB on peut continuer le lancement
    log.info("Connected to the db");
    // Middleware pour les informations nécessaire
    app.use(function (req, res, next) {
        req.secretJWT = JWT_SECRET;
        req.db = {
            r: r,
            conn: conn
        };
        // Récupération du token JWT
        req.bearer = null;
        if (req.headers.authorization) {
            var auth_1 = req.headers.authorization.split(' ');
            if (auth_1.length === 2 && auth_1[0] === 'bearer') {
                req.bearer = auth_1[1];
            }
        }
        res.logger = log;
        next();
    });
    // Ajout des routes
    log.debug('Add paths for the API');
    app.get('/', function (req, res) {
        res.status(200).json({
            message: "Welcome to DisQuss API"
        });
    });
    app.use('/auth', Auth_1.default);
    app.use('/accounts', Accounts_1.default);
    // On lance l'application
    app.listen(HTTP_PORT, function () {
        log.info("API running on port " + HTTP_PORT);
    });
});
p.error(function (error) {
    log.error("Connected to the db failed : " + error);
});
