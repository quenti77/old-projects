'use strict';

import * as express from 'express'
import * as token from 'jsonwebtoken'

import { User } from '../models/User'

import { Validators, Validator } from '../validators/Validator'
import { RequireValidator } from '../validators/RequireValidator'

// Nos routes
const auth = express.Router()

auth.post('/', (req, res) => {
  // Vérification des champs requis de base
  const bodyRequire = new RequireValidator(['email', 'password'])
  bodyRequire.StatusCode = 405
  bodyRequire.JsonError = {
    message: `Required fields are missing`
  }

  const validator = new Validators([
    bodyRequire
  ])

  validator.validate(req.body).then((data) => {
    const user = new User(req.db)
    user.getAuth(data.email, data.password).then((user) => {
      const u = JSON.parse(JSON.stringify(user))
      delete u['account']

      const tokenData = token.sign(u, req.secretJWT, {
        expiresIn: 24 * 3600
      })

      res.status(200).json({
        message: 'Authentication complete',
        token: tokenData
      })
    }).catch((err) => {
      res.status(403).json({
        message: 'Authentication failed'
      })
    })
  }).catch((err : Validator) => {
    res.status(err.StatusCode).json(err.JsonError)
  })

})

export default auth
