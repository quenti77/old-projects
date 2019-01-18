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
var Validator_1 = require("./Validator");
var RequireValidator = (function (_super) {
    __extends(RequireValidator, _super);
    function RequireValidator(fields, sub) {
        if (sub === void 0) { sub = null; }
        var _this = _super.call(this, 'require', sub) || this;
        _this.fields = fields;
        return _this;
    }
    Object.defineProperty(RequireValidator.prototype, "Fields", {
        get: function () { return this.fields; },
        enumerable: true,
        configurable: true
    });
    /**
     *
     * @param data
     * @returns {boolean}
     */
    RequireValidator.prototype.check = function (data) {
        data = this.subData(data);
        if (Object.keys(data).length < this.fields.length) {
            return false;
        }
        for (var _i = 0, _a = this.fields; _i < _a.length; _i++) {
            var field = _a[_i];
            if (!data.hasOwnProperty(field)) {
                return false;
            }
        }
        return true;
    };
    return RequireValidator;
}(Validator_1.Validator));
exports.RequireValidator = RequireValidator;
