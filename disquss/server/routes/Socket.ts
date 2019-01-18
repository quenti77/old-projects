'use strict';

import * as socketio from 'socket.io'
import { IDB } from '../models/Model'

class Socket
{
  private http : any

  private io : any
  get IO() : any { return this.io }

  private db : IDB
  get DB() : IDB { return this.db }

  private secret : string
  get Secret() : string { return this.secret }

  private log : any
  get Log() : any { return this.log }

  constructor (http : any, db : IDB, secret : string, log : any)
  {
    this.http = http
    this.db = db

    this.secret = secret
    this.log = log

    this.io = socketio.listen(this.http)
  }
}

export { Socket }
