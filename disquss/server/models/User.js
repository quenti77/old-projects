'use strict';
var __extends = (this && this.__extends) || (function () {
    var extendStatics = Object.setPrototypeOf ||
        ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
        function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
var bcrypt = require("bcrypt");
var Model_1 = require("./Model");
var SALT_WORK_FACTOR = 10;
var REASON;
(function (REASON) {
    REASON[REASON["NOT_FOUND"] = 0] = "NOT_FOUND";
    REASON[REASON["PASSWORD_MATCH"] = 1] = "PASSWORD_MATCH";
    REASON[REASON["ERROR"] = 2] = "ERROR";
})(REASON || (REASON = {}));
exports.REASON = REASON;
/**
 * Notre model User
 */
var User = (function (_super) {
    __extends(User, _super);
    function User(db, data) {
        if (data === void 0) { data = null; }
        var _this = _super.call(this, db, 'users') || this;
        _this.data = data;
        return _this;
    }
    Object.defineProperty(User.prototype, "Data", {
        get: function () { return this.data; },
        enumerable: true,
        configurable: true
    });
    User.prototype.createById = function (id) {
        var _this = this;
        return new Promise(function (resolve, reject) {
            _this.db.r.table(_this.name).get(id).run(_this.db.conn).then(function (result) {
                _this.data = result;
                resolve();
            }).catch(function (err) {
                reject(err);
            });
        });
    };
    /**
     *
     * @param email
     * @returns {Promise<any>}
     */
    User.prototype.findByEmail = function (email) {
        var _this = this;
        if (email === void 0) { email = this.Data.email; }
        return new Promise(function (resolve, reject) {
            _this.db.r.table(_this.name).filter({
                email: email
            }).run(_this.db.conn).then(function (cursor) {
                return cursor.toArray();
            }).then(function (users) {
                resolve(users);
            }).catch(function (err) {
                reject(err);
            });
        });
    };
    User.prototype.getAuth = function (email, pass) {
        var _this = this;
        return new Promise(function (resolve, reject) {
            var user = null;
            _this.findByEmail(email).then(function (users) {
                if (users.length === 0) {
                    reject({ status: REASON.NOT_FOUND });
                    return false;
                }
                user = users[0];
                return bcrypt.compare(pass, user.account.password);
            }).then(function (isMatch) {
                if (!isMatch) {
                    reject({ status: REASON.PASSWORD_MATCH });
                    return false;
                }
                resolve(user);
            }).catch(function () {
                reject({ status: REASON.ERROR });
            });
        });
    };
    /**
     *
     * @param id
     * @returns {Promise<any>}
     */
    User.prototype.find = function (id) {
        var _this = this;
        return new Promise(function (resolve, reject) {
            _this.db.r.table(_this.name).get(id).run(_this.db.conn).then(function (result) {
                resolve(result);
            }).catch(function (err) {
                reject(err);
            });
        });
    };
    /**
     * Ajoute l'utilisateur
     * @returns {Promise<any>}
     */
    User.prototype.save = function () {
        if (this.data.id) {
            return this.update();
        }
        else {
            return this.insert();
        }
    };
    /**
     * Ajoute l'utilisateur
     * @returns {Promise<any>}
     */
    User.prototype.insert = function () {
        var _this = this;
        return new Promise(function (resolve, reject) {
            _this.data.createdAt = new Date();
            _this.data.modifieAt = null;
            bcrypt.genSalt(SALT_WORK_FACTOR).then(function (salt) {
                return bcrypt.hash(_this.data.account.password, salt);
            }).then(function (hashPass) {
                _this.data.account.password = hashPass;
                return _this.db.r.table(_this.name).insert({
                    name: _this.data.name,
                    firstname: _this.data.firstname || null,
                    lastname: _this.data.lastname || null,
                    email: _this.data.email,
                    emailVerified: false,
                    createdAt: _this.data.createdAt,
                    modifieAt: null,
                    account: _this.data.account
                }).run(_this.db.conn);
            }).then(function (result) {
                if (result.generated_keys) {
                    _this.data.id = result.generated_keys[0];
                }
                resolve(result);
            }).catch(function (err) {
                reject(err);
            });
        });
    };
    /**
     *
     * @returns {Promise<any>}
     */
    User.prototype.update = function () {
        var _this = this;
        return new Promise(function (resolve, reject) {
            _this.db.r.table(_this.name).filter({
                "id": _this.data.id
            }).update({
                name: _this.data.name,
                firstname: _this.data.firstname || null,
                lastname: _this.data.lastname || null,
                email: _this.data.email,
                emailVerified: (_this.data.emailVerified != null),
                modifieAt: new Date(),
                account: _this.data.account
            }).run(_this.db.conn).then(function (result) {
                resolve(result);
            }).catch(function (err) {
                reject(err);
            });
        });
    };
    return User;
}(Model_1.Model));
exports.User = User;
