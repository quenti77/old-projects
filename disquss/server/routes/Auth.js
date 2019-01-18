'use strict';
Object.defineProperty(exports, "__esModule", { value: true });
var express = require("express");
var token = require("jsonwebtoken");
var User_1 = require("../models/User");
var Validator_1 = require("../validators/Validator");
var RequireValidator_1 = require("../validators/RequireValidator");
// Nos routes
var auth = express.Router();
auth.post('/', function (req, res) {
    // Vérification des champs requis de base
    var bodyRequire = new RequireValidator_1.RequireValidator(['email', 'password']);
    bodyRequire.StatusCode = 405;
    bodyRequire.JsonError = {
        message: "Required fields are missing"
    };
    var validator = new Validator_1.Validators([
        bodyRequire
    ]);
    validator.validate(req.body).then(function (data) {
        var user = new User_1.User(req.db);
        user.getAuth(data.email, data.password).then(function (user) {
            var u = JSON.parse(JSON.stringify(user));
            delete u['account'];
            var tokenData = token.sign(u, req.secretJWT, {
                expiresIn: 24 * 3600
            });
            res.status(200).json({
                message: 'Authentication complete',
                token: tokenData
            });
        }).catch(function (err) {
            res.status(403).json({
                message: 'Authentication failed'
            });
        });
    }).catch(function (err) {
        res.status(err.StatusCode).json(err.JsonError);
    });
});
exports.default = auth;
