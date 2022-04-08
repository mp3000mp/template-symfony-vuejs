import { shallowMount } from '@vue/test-utils'
import Header from '@/views/layout/Header.vue'
import { createTestingPinia } from '@pinia/testing'
import { useSecurityStore } from '@/stores/security'

jest.mock('vue-router', () => ({
  useRouter: jest.fn()
}))
const plugins = [createTestingPinia()]
const stubs = {
  RouterLink: true
}
const mockStore = useSecurityStore()

describe('header.vue', () => {
  it('role anonym', () => {
    mockStore.me.roles = ['ROLE_ANONYMOUS']
    const wrapper = shallowMount(Header, {
      global: {
        stubs,
        plugins
      }
    })
    const menuItems = wrapper.findAll('.nav-link')
    expect(menuItems).toHaveLength(1)
  })

  it('role user', () => {
    mockStore.me.roles = ['ROLE_USER']
    const wrapper = shallowMount(Header, {
      global: {
        stubs,
        plugins
      }
    })
    const menuItems = wrapper.findAll('.nav-link')
    expect(menuItems).toHaveLength(3)
  })

  it('role admin', () => {
    mockStore.me.roles = ['ROLE_USER', 'ROLE_ADMIN']
    const wrapper = shallowMount(Header, {
      global: {
        stubs,
        plugins
      }
    })
    const menuItems = wrapper.findAll('.nav-link')
    expect(menuItems).toHaveLength(4)
  })
})
