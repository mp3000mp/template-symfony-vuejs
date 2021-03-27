interface MenuItem {
  label: string;
  to: string;
  role: string;
}
export const menuItems: MenuItem[] = [
  {
    label: 'Home',
    to: '/',
    role: 'ROLE_USER'
  },
  {
    label: 'Users',
    to: '/admin/users',
    role: 'ROLE_ADMIN'
  },
  {
    label: 'Login',
    to: '/login',
    role: 'ROLE_ANONYMOUS'
  },
  {
    label: 'Account',
    to: '/account',
    role: 'ROLE_USER'
  }
]
