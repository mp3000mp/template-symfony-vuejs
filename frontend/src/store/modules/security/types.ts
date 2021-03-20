export interface SecurityState {
  apiToken: string|null;
  errorMsg: string|null;
  isAuthenticated: boolean;
  isLoading: boolean;
  refreshToken: string|null;
}
