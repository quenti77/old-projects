'use strict';

// Les modules externes
import * as express from 'express'
import * as bodyParser from 'body-parser'
import * as r from 'rethinkdb'
import * as log4js from 'log4js'

// Nos routes
import { default as auth } from './routes/Auth'
import { default as accounts } from './routes/Accounts'

// On charge les settings depuis les variables d'environnement
const HTTP_PORT  : number = parseInt(process.env.HTTP_PORT)   || 1337
const JWT_SECRET : string = process.env.JWT_SECRET            || 'D1sQussS3rv3r'

// Création du logger
log4js.configure({
  appenders: {
    out: { type: 'stdout' },
    app: { type: 'file', filename: 'server-api.log' }
  },
  categories: {
    'default': { appenders: [ 'out', 'app' ], level: 'debug' }
  }
});

const log = log4js.getLogger('api')

// Création de l'application
log.debug('Loading API ...')
const app = express()

// Désactivation d'un header
app.disable('x-powered-by')

// Ajout des middlewares
app.use(bodyParser.urlencoded({ extended: true }))
app.use(bodyParser.json())

log.debug('Connecting to database : rethinkdb://127.0.0.1:28015/disquss')
const p = r.connect({
  db: 'disquss'
})

p.then((conn) => {
  // Quand on est connecté à la DB on peut continuer le lancement
  log.info(`Connected to the db`)

  // Middleware pour les informations nécessaire
  app.use((req, res, next) => {
    req.secretJWT = JWT_SECRET
    req.db = {
      r,
      conn
    }

    // Récupération du token JWT
    req.bearer = null
    if (req.headers.authorization) {
      const auth = req.headers.authorization.split(' ')

      if (auth.length === 2 && auth[0] === 'bearer') {
        req.bearer = auth[1]
      }
    }

    res.logger = log

    next()
  })

  // Ajout des routes
  log.debug('Add paths for the API')
  app.get('/', (req, res) => {
    res.status(200).json({
      message: `Welcome to DisQuss API`
    })
  })

  app.use('/auth', auth)
  app.use('/accounts', accounts)


  // On lance l'application
  app.listen(HTTP_PORT, () => {
    log.info(`API running on port ${HTTP_PORT}`)
  })
})

p.error((error) => {
  log.error(`Connected to the db failed : ${error}`)
})
