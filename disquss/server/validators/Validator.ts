'use strict';

abstract class Validator
{
  protected name : string
  get Name () : string { return this.name }

  private sub : Array<string>
  get Sub () : Array<string> { return this.sub }

  protected statusCode : number
  get StatusCode () : number { return this.statusCode }
  set StatusCode (value : number) { this.statusCode = value }

  protected jsonError : object
  get JsonError () : object { return this.jsonError }
  set JsonError (value : object) { this.jsonError = value }

  constructor (name : string, sub : Array<string> = null)
  {
    this.name = name
    this.sub = sub
  }

  subData (data)
  {
    if (this.sub) {
      for (let sub of this.sub) {
        if (!data.hasOwnProperty(sub)) {
          return false
        }
        data = data[sub]
      }
    }

    return data
  }

  abstract check (data : any) : boolean
}

class Validators
{
  private validators : Array<Validator>;
  get Validators () : Array<Validator> { return this.validators }

  constructor (validators : Array<Validator>)
  {
    this.validators = validators
  }

  /**
   * Valide les données et en fonction du résultat
   * on resolve ou reject la promesse
   * @param data
   * @returns {Promise<any>}
   */
  validate (data : any)
  {
    return new Promise<any>((resolve, reject) => {
      for (let validator of this.validators) {
        if (!validator.check(data)) {
          reject(validator)
        }
      }

      resolve(data)
    })
  }

}

export { Validators, Validator }
