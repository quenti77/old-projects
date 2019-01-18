'use strict';

import { Validator } from './Validator'

class RegexValidator extends Validator
{
  private regex : RegExp
  get Regex () : RegExp { return this.regex }

  constructor (regex : RegExp, sub : Array<string> = null)
  {
    super('regex', sub)
    this.regex = regex
  }

  /**
   *
   * @param data
   * @returns {boolean}
   */
  check(data : any): boolean
  {
    return this.subData(data).match(this.regex) !== null
  }

}

export { RegexValidator }
