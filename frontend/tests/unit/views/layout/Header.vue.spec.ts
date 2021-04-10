import { shallowMount } from '@vue/test-utils'
import Vuex, { createStore } from 'vuex'
import Header from '@/views/layout/Header.vue'

const stubs = {
  RouterLink: true
}

function mockStore (roles: string[]) {
  return createStore({
    modules: {
      security: {
        namespaced: true,
        getters: {
          getIsAuth: jest.fn(() => !roles.includes('ROLE_ANONYMOUS'))
        },
        state: {
          me: {
            username: 'test',
            roles: roles
          }
        }
      }
    }
  })
}

describe('header.vue', () => {
  it('role anonym', () => {
    const wrapper = shallowMount(Header, {
      global: {
        plugins: [mockStore(['ROLE_ANONYMOUS'])],
        stubs
      }
    })
    const menuItems = wrapper.findAll('.nav-link')
    expect(menuItems).toHaveLength(1)
  })

  it('role user', () => {
    const wrapper = shallowMount(Header, {
      global: {
        plugins: [mockStore(['ROLE_USER'])],
        stubs
      }
    })
    const menuItems = wrapper.findAll('.nav-link')
    expect(menuItems).toHaveLength(3)
  })

  it('role admin', () => {
    const wrapper = shallowMount(Header, {
      global: {
        plugins: [mockStore(['ROLE_USER', 'ROLE_ADMIN'])],
        stubs
      }
    })
    const menuItems = wrapper.findAll('.nav-link')
    expect(menuItems).toHaveLength(4)
  })
})
