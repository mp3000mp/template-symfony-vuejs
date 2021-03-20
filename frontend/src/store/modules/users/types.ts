export interface User {
  id: number;
  username: string;
  email: string;
}

export interface UserState {
  errorMsg: string|null;
  isLoading: boolean;
  users: User[];
}
