'use strict';

import { Validator } from './Validator'

class RequireValidator extends Validator
{
  private fields : Array<string>
  get Fields () : Array<string> { return this.fields }

  constructor (fields : Array<string>, sub : Array<string> = null)
  {
    super('require', sub)
    this.fields = fields
  }

  /**
   *
   * @param data
   * @returns {boolean}
   */
  check(data : any): boolean
  {
    data = this.subData(data)

    if (Object.keys(data).length < this.fields.length) {
      return false
    }

    for (let field of this.fields) {
      if (!data.hasOwnProperty(field)) {
        return false
      }
    }

    return true
  }

}

export { RequireValidator }
