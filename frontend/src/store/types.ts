type Method = 'GET' | 'POST' | 'DELETE' | 'PUT' | 'PATCH'

export class StoreRequest {
  auth = true;
  callCount = 0;
  status: number|null = null;
  message = '';
  method: Method = 'GET';
  loading = false;
  url: string;

  constructor (method: Method, url: string, auth = true) {
    this.auth = auth
    this.method = method
    this.url = url
  }

  start () {
    this.callCount++
    this.loading = true
  }

  end (status: number, message: string) {
    this.loading = false
    this.status = status
    this.message = message
  }
}

export abstract class AbstractState {
  actionRequest: {
    [key: string]: StoreRequest;
  } = {};
}

export class RootState extends AbstractState {

}
