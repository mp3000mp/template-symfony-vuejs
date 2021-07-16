import { AppState } from '@/store/modules/app/types'
import { SecurityState } from '@/store/modules/security/types'
import { UserState } from '@/store/modules/users/types'

type Method = 'GET' | 'POST' | 'DELETE' | 'PUT' | 'PATCH'

export class StoreRequest {
  auth = true;
  callCount = 0;
  isError = false;
  loading = false;
  message = '';
  method: Method = 'GET';
  status: number|null = null;

  url: string;

  constructor (method: Method, url: string, auth = true) {
    this.auth = auth
    this.method = method
    this.url = url
  }

  private reset () {
    this.status = 0
    this.message = ''
    this.loading = false
    this.isError = false
  }

  public start () {
    this.reset()
    this.callCount++
    this.loading = true
  }

  public end (status: number, message: string) {
    this.loading = false
    this.status = status
    this.message = message
    this.isError = status < 200 || status >= 300
  }
}

export abstract class AbstractState {
  actionRequest: {
    [key: string]: StoreRequest;
  } = {};
}

export interface RootState {
  app: AppState;
  security: SecurityState;
  users: UserState;
}
