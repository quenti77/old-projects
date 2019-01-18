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
var RegexValidator = (function (_super) {
    __extends(RegexValidator, _super);
    function RegexValidator(regex, sub) {
        if (sub === void 0) { sub = null; }
        var _this = _super.call(this, 'regex', sub) || this;
        _this.regex = regex;
        return _this;
    }
    Object.defineProperty(RegexValidator.prototype, "Regex", {
        get: function () { return this.regex; },
        enumerable: true,
        configurable: true
    });
    /**
     *
     * @param data
     * @returns {boolean}
     */
    RegexValidator.prototype.check = function (data) {
        return this.subData(data).match(this.regex) !== null;
    };
    return RegexValidator;
}(Validator_1.Validator));
exports.RegexValidator = RegexValidator;
