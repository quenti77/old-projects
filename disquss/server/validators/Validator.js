'use strict';
Object.defineProperty(exports, "__esModule", { value: true });
var Validator = (function () {
    function Validator(name, sub) {
        if (sub === void 0) { sub = null; }
        this.name = name;
        this.sub = sub;
    }
    Object.defineProperty(Validator.prototype, "Name", {
        get: function () { return this.name; },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(Validator.prototype, "Sub", {
        get: function () { return this.sub; },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(Validator.prototype, "StatusCode", {
        get: function () { return this.statusCode; },
        set: function (value) { this.statusCode = value; },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(Validator.prototype, "JsonError", {
        get: function () { return this.jsonError; },
        set: function (value) { this.jsonError = value; },
        enumerable: true,
        configurable: true
    });
    Validator.prototype.subData = function (data) {
        if (this.sub) {
            for (var _i = 0, _a = this.sub; _i < _a.length; _i++) {
                var sub = _a[_i];
                if (!data.hasOwnProperty(sub)) {
                    return false;
                }
                data = data[sub];
            }
        }
        return data;
    };
    return Validator;
}());
exports.Validator = Validator;
var Validators = (function () {
    function Validators(validators) {
        this.validators = validators;
    }
    Object.defineProperty(Validators.prototype, "Validators", {
        get: function () { return this.validators; },
        enumerable: true,
        configurable: true
    });
    /**
     * Valide les données et en fonction du résultat
     * on resolve ou reject la promesse
     * @param data
     * @returns {Promise<any>}
     */
    Validators.prototype.validate = function (data) {
        var _this = this;
        return new Promise(function (resolve, reject) {
            for (var _i = 0, _a = _this.validators; _i < _a.length; _i++) {
                var validator = _a[_i];
                if (!validator.check(data)) {
                    reject(validator);
                }
            }
            resolve(data);
        });
    };
    return Validators;
}());
exports.Validators = Validators;
