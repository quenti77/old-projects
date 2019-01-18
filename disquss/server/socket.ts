'use strict';

// Les modules externes
import * as http from 'http'
import * as r from 'rethinkdb'
import * as log4js from 'log4js'

import { Socket } from './routes/Socket'
import { base } from './sockets/base'

// On charge les settings depuis les variables d'environnement
const HTTP_PORT  : number = parseInt(process.env.HTTP_PORT)   || 7331
const JWT_SECRET : string = process.env.JWT_SECRET            || 'D1sQussS3rv3r'

// Création du logger
log4js.configure({
  appenders: {
    out: { type: 'stdout' },
    app: { type: 'file', filename: 'server-socket.log' }
  },
  categories: {
    'default': { appenders: [ 'out', 'app' ], level: 'debug' }
  }
});

const log = log4js.getLogger('socket')

// Création de l'application
log.debug('Loading Socket ...')

log.debug('Connecting to database : rethinkdb://127.0.0.1:28015/disquss')
const p = r.connect({
  db: 'disquss'
})

p.then((conn) => {
  // Quand on est connecté à la DB on peut continuer le lancement
  log.info(`Connected to the db`)

  const server = http.createServer((req, res) => {
    // res.end('SOCKET.IO')
  })

  // Nos routes SocketIO
  const socket = new Socket(server, { r, conn }, JWT_SECRET, log)
  base(socket)

  // On lance l'application
  server.listen(HTTP_PORT)
})

p.error((error) => {
  log.error(`Connected to the db failed : ${error}`)
})
