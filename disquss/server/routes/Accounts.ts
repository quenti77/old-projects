'use strict';

import * as express from 'express'
import * as jwt from 'express-jwt'
import * as token from 'jsonwebtoken'

import { User } from '../models/User'

import { Validators, Validator } from '../validators/Validator'
import { RequireValidator } from '../validators/RequireValidator'
import { RegexValidator } from '../validators/RegexValidator'

// JWT SECRET
const JWT_SECRET : string = process.env.JWT_SECRET || 'D1sQussS3rv3r'

// Nos routes
const accounts = express.Router()

// Check token
const verifyToken = (tokenData : string, secret : string) => {
  return new Promise((resolve, reject) => {
    token.verify(tokenData, secret, (err, decoded) => {
      if (err) {
        reject(err)
        return false
      }

      resolve(decoded)
    })
  })
}

// Activation de la vérification sauf pour l'inscription
accounts.use(jwt({
  secret: JWT_SECRET
}).unless((req) => {
  return (req.method === 'POST')
}))

accounts.use(function (err, req, res, next) {
  if (err.name === 'UnauthorizedError') {
    res.status(401).json({
      "status": 401,
      "code": 401,
      "message": "Authentication required.",
      "developerMessage": "Authentication with a valid API Key is required."
    });
  }
})

// Récupération des informations de l'utilisateur en cours
accounts.get('/', (req, res) => {
  const user = new User(req.db)
  verifyToken(req.bearer, req.secretJWT).then((decoded : any) => {
    return user.createById(decoded.id)
  }).then(() => {
    const info = JSON.parse(JSON.stringify(user.Data))
    delete info['account']

    res.status(200).json(info)
  }).catch((err) => {
    res.status(404).json({
      message: `User not found with this token`
    })
  })
})

// Inscription d'un utilisateur
accounts.post('/', (req, res, next) => {
  // Vérification des champs requis de base
  const bodyRequire = new RequireValidator(['name', 'email', 'account'])
  bodyRequire.StatusCode = 405
  bodyRequire.JsonError = {
    message: `Required fields are missing`
  }

  const accountRequire = new RequireValidator(['kind', 'password'], ['account'])
  accountRequire.StatusCode = 405
  accountRequire.JsonError = {
    message: `Required fields for account are missing`
  }

  // Vérification du mail
  const emailRegex = new RegexValidator(new RegExp(/^[a-z0-9]+@[a-z0-9]+\.[a-z0-9]+$/i), ['email'])
  emailRegex.StatusCode = 405
  emailRegex.JsonError = {
    message: `Email is in an invalid format`
  }

  const validator = new Validators([
      bodyRequire,
      accountRequire,
      emailRegex
  ])

  let user = null
  let body = null
  validator.validate(req.body).then((data : any) => {
    body = data
    user = new User(req.db, {
      id: null,
      name: body.name,
      firstname: body.firstname,
      lastname: body.lastname,
      email: body.email,
      account: body.account
    })

    return user.findByEmail()
  }).then((users : Array<any>) => {
    // Un utilisateur a été trouvé
    if (users.length > 0) {
      res.status(409).json({
        message: `Account already exist`
      })
      return false
    }

    // Enregistrement
    user.save().then(() => {
      res.logger.info(`New user : ${user.Data.id}`)
      res.status(200).json({
        message: 'Your account has been successfully created'
      })
    }).catch((err) => {
      res.logger.warn(`Error : ${err}`)
    })
  }).catch((err : Validator) => {
    res.status(err.StatusCode).json(err.JsonError)
  })

})

export default accounts
