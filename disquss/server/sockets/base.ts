'use strict';

import { Socket } from '../routes/Socket'

import { channels } from './channels'

const base = (instance : Socket) => {
  const { IO, DB, Log } = instance

  IO.on('connection', (client) => {
    channels(instance, client)
  })
}

export { base }
