'use strict';

/**
 * Objet pour stoquer rethinkdb et la connexion
 */
interface IDB
{
  r     : any,
  conn  : any
}

/**
 * Notre base pour les models
 */
class Model
{
  private db : IDB
  get DB () : IDB { return this.db }

  protected name : string
  get Name () : string { return this.name }

  constructor (db : IDB, name : string)
  {
    this.db = db
    this.name = name
  }

  /**
   * Récupère tous les éléments de la table
   * @returns {Promise<any>}
   */
  all () : Promise<any>
  {
    return new Promise<any>((resolve, reject) => {
      this.db.r.table(this.name).run(this.db.conn)
          .then((cursor) => {
            return cursor.toArray()
          })
          .then((result) => {
            resolve(result)
          })
          .catch((err) => {
            reject(err)
          })
    })
  }
}

export { IDB, Model }
