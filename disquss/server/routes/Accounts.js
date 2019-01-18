'use strict';
Object.defineProperty(exports, "__esModule", { value: true });
var express = require("express");
var jwt = require("express-jwt");
var token = require("jsonwebtoken");
var User_1 = require("../models/User");
var Validator_1 = require("../validators/Validator");
var RequireValidator_1 = require("../validators/RequireValidator");
var RegexValidator_1 = require("../validators/RegexValidator");
// JWT SECRET
var JWT_SECRET = process.env.JWT_SECRET || 'D1sQussS3rv3r';
// Nos routes
var accounts = express.Router();
// Check token
var verifyToken = function (tokenData, secret) {
    return new Promise(function (resolve, reject) {
        token.verify(tokenData, secret, function (err, decoded) {
            if (err) {
                reject(err);
                return false;
            }
            resolve(decoded);
        });
    });
};
// Activation de la vérification sauf pour l'inscription
accounts.use(jwt({
    secret: JWT_SECRET
}).unless(function (req) {
    return (req.method === 'POST');
}));
accounts.use(function (err, req, res, next) {
    if (err.name === 'UnauthorizedError') {
        res.status(401).json({
            "status": 401,
            "code": 401,
            "message": "Authentication required.",
            "developerMessage": "Authentication with a valid API Key is required."
        });
    }
});
// Récupération des informations de l'utilisateur en cours
accounts.get('/', function (req, res) {
    var user = new User_1.User(req.db);
    verifyToken(req.bearer, req.secretJWT).then(function (decoded) {
        return user.createById(decoded.id);
    }).then(function () {
        var info = JSON.parse(JSON.stringify(user.Data));
        delete info['account'];
        res.status(200).json(info);
    }).catch(function (err) {
        res.status(404).json({
            message: "User not found with this token"
        });
    });
});
// Inscription d'un utilisateur
accounts.post('/', function (req, res, next) {
    // Vérification des champs requis de base
    var bodyRequire = new RequireValidator_1.RequireValidator(['name', 'email', 'account']);
    bodyRequire.StatusCode = 405;
    bodyRequire.JsonError = {
        message: "Required fields are missing"
    };
    var accountRequire = new RequireValidator_1.RequireValidator(['kind', 'password'], ['account']);
    accountRequire.StatusCode = 405;
    accountRequire.JsonError = {
        message: "Required fields for account are missing"
    };
    // Vérification du mail
    var emailRegex = new RegexValidator_1.RegexValidator(new RegExp(/^[a-z0-9]+@[a-z0-9]+\.[a-z0-9]+$/i), ['email']);
    emailRegex.StatusCode = 405;
    emailRegex.JsonError = {
        message: "Email is in an invalid format"
    };
    var validator = new Validator_1.Validators([
        bodyRequire,
        accountRequire,
        emailRegex
    ]);
    var user = null;
    var body = null;
    validator.validate(req.body).then(function (data) {
        body = data;
        user = new User_1.User(req.db, {
            id: null,
            name: body.name,
            firstname: body.firstname,
            lastname: body.lastname,
            email: body.email,
            account: body.account
        });
        return user.findByEmail();
    }).then(function (users) {
        // Un utilisateur a été trouvé
        if (users.length > 0) {
            res.status(409).json({
                message: "Account already exist"
            });
            return false;
        }
        // Enregistrement
        user.save().then(function () {
            res.logger.info("New user : " + user.Data.id);
            res.status(200).json({
                message: 'Your account has been successfully created'
            });
        }).catch(function (err) {
            res.logger.warn("Error : " + err);
        });
    }).catch(function (err) {
        res.status(err.StatusCode).json(err.JsonError);
    });
});
exports.default = accounts;
