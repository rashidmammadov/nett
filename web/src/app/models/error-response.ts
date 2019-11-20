export class ErrorResponse {
  private _message: string;
  private _status_code: number;

  constructor(data) {
      this.message = data.message;
      this.status_code = data.status_code;
  }

  get message(): string {
      return this._message;
  }

  set message(value: string) {
      this._message = value;
  }

  get status_code(): number {
      return this._status_code;
  }

  set status_code(value: number) {
      this._status_code = value;
  }

}
