import { AbstractState, StoreRequest } from '@/store/types'

export interface User {
  id: number;
  username: string;
  email: string;
}

export class UserState extends AbstractState {
  users: User[] = [];
}
