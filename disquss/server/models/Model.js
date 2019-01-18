'use strict';
Object.defineProperty(exports, "__esModule", { value: true });
/**
 * Notre base pour les models
 */
var Model = (function () {
    function Model(db, name) {
        this.db = db;
        this.name = name;
    }
    Object.defineProperty(Model.prototype, "DB", {
        get: function () { return this.db; },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(Model.prototype, "Name", {
        get: function () { return this.name; },
        enumerable: true,
        configurable: true
    });
    /**
     * Récupère tous les éléments de la table
     * @returns {Promise<any>}
     */
    Model.prototype.all = function () {
        var _this = this;
        return new Promise(function (resolve, reject) {
            _this.db.r.table(_this.name).run(_this.db.conn)
                .then(function (cursor) {
                return cursor.toArray();
            })
                .then(function (result) {
                resolve(result);
            })
                .catch(function (err) {
                reject(err);
            });
        });
    };
    return Model;
}());
exports.Model = Model;
