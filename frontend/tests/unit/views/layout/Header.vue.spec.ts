import { shallowMount } from '@vue/test-utils'
import Header from '@/views/layout/Header.vue'

jest.mock('vue-router', () => ({
  useRouter: jest.fn()
}))
const stubs = {
  RouterLink: true
}

let mockRoles: string[] = []
jest.mock('@/store', () => {
  return {
    useStore: jest.fn(() => {
      return {
        getters: {
          'security/getIsAuth': !mockRoles.includes('ROLE_ANONYMOUS'),
          'security/getRoles': mockRoles
        }
      }
    })
  }
})

describe('header.vue', () => {
  it('role anonym', () => {
    mockRoles = ['ROLE_ANONYMOUS']
    const wrapper = shallowMount(Header, {
      global: {
        stubs
      }
    })
    const menuItems = wrapper.findAll('.nav-link')
    expect(menuItems).toHaveLength(1)
  })

  it('role user', () => {
    mockRoles = ['ROLE_USER']
    const wrapper = shallowMount(Header, {
      global: {
        stubs
      }
    })
    const menuItems = wrapper.findAll('.nav-link')
    expect(menuItems).toHaveLength(3)
  })

  it('role admin', () => {
    mockRoles = ['ROLE_USER', 'ROLE_ADMIN']
    const wrapper = shallowMount(Header, {
      global: {
        stubs
      }
    })
    const menuItems = wrapper.findAll('.nav-link')
    expect(menuItems).toHaveLength(4)
  })
})
