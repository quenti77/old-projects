'use strict';

import * as bcrypt from 'bcrypt'
import { IDB, Model } from './Model'

const SALT_WORK_FACTOR = 10

enum REASON {
  NOT_FOUND,
  PASSWORD_MATCH,
  ERROR
}

/**
 * Défini le type de connexion utilisé (interne, facebook, twitter, etc.)
 */
interface IAccount
{
  kind      : string,
  password  : string
}

/**
 * Représente notre model User
 */
interface IUserModel
{
  id              : string,
  name            : string,
  firstname?      : string,
  lastname?       : string,
  email           : string,
  createdAt?      : Date,
  modifieAt?      : Date,
  emailVerified?  : Boolean,
  account         : IAccount
}

/**
 * Notre model User
 */
class User extends Model
{
  private data : IUserModel
  get Data () : IUserModel { return this.data }

  constructor (db : IDB, data : IUserModel = null)
  {
    super(db, 'users')
    this.data = data
  }

  createById(id : string)
  {
    return new Promise<any>((resolve, reject) => {
      this.db.r.table(this.name).get(id).run(this.db.conn).then((result) => {
        this.data = result
        resolve()
      }).catch((err) => {
        reject(err)
      })
    })
  }

  /**
   *
   * @param email
   * @returns {Promise<any>}
   */
  findByEmail(email : string = this.Data.email)
  {
    return new Promise<any>((resolve, reject) => {
      this.db.r.table(this.name).filter({
        email: email
      }).run(this.db.conn).then((cursor) => {
        return cursor.toArray()
      }).then((users) => {
        resolve(users)
      }).catch((err) => {
        reject(err)
      })
    })
  }

  getAuth(email, pass)
  {
    return new Promise((resolve, reject) => {
      let user = null
      this.findByEmail(email).then((users) => {
        if (users.length === 0) {
          reject({ status: REASON.NOT_FOUND })
          return false
        }

        user = users[0]
        return bcrypt.compare(pass, user.account.password)
      }).then((isMatch) => {
        if (!isMatch) {
          reject({ status: REASON.PASSWORD_MATCH })
          return false
        }

        resolve(user)
      }).catch(() => {
        reject({ status: REASON.ERROR })
      })
    })
  }

  /**
   *
   * @param id
   * @returns {Promise<any>}
   */
  find (id : string) : Promise<any>
  {
    return new Promise<any>((resolve, reject) => {
      this.db.r.table(this.name).get(id).run(this.db.conn).then((result) => {
        resolve(result)
      }).catch((err) => {
        reject(err)
      })
    })
  }

  /**
   * Ajoute l'utilisateur
   * @returns {Promise<any>}
   */
  save () : Promise<any>
  {
    if (this.data.id) {
      return this.update()
    } else {
      return this.insert()
    }
  }

  /**
   * Ajoute l'utilisateur
   * @returns {Promise<any>}
   */
  insert () : Promise<any>
  {
    return new Promise<any>((resolve, reject) => {
      this.data.createdAt = new Date()
      this.data.modifieAt = null

      bcrypt.genSalt(SALT_WORK_FACTOR).then((salt) => {
        return bcrypt.hash(this.data.account.password, salt)
      }).then((hashPass) => {
        this.data.account.password = hashPass

        return this.db.r.table(this.name).insert({
          name: this.data.name,
          firstname: this.data.firstname || null,
          lastname: this.data.lastname || null,
          email: this.data.email,
          emailVerified: false,
          createdAt: this.data.createdAt,
          modifieAt: null,
          account: this.data.account
        }).run(this.db.conn)
      }).then((result) => {
        if (result.generated_keys) {
          this.data.id = result.generated_keys[0]
        }

        resolve(result)
      }).catch((err) => {
        reject(err)
      })
    })
  }

  /**
   *
   * @returns {Promise<any>}
   */
  update () : Promise<any>
  {
    return new Promise<any>((resolve, reject) => {
      this.db.r.table(this.name).filter({
        "id": this.data.id
      }).update({
        name: this.data.name,
        firstname: this.data.firstname || null,
        lastname: this.data.lastname || null,
        email: this.data.email,
        emailVerified: (this.data.emailVerified != null),
        modifieAt: new Date(),
        account: this.data.account
      }).run(this.db.conn).then((result) => {
        resolve(result)
      }).catch((err) => {
        reject(err)
      })
    })
  }
}

export { IAccount, IUserModel, User, REASON }
