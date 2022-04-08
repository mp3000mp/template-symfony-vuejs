import { AbstractState } from '@/stores/types'

export class Me {
  roles: string[] = ['ROLE_ANONYMOUS'];
  username = 'Anonymous';
}

export class SecurityState extends AbstractState {
  apiToken: string|null = null;
  me: Me = new Me();
  refreshToken: string|null = null;
}
