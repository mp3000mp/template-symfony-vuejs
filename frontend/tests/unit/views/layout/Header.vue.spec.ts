import { shallowMount } from '@vue/test-utils'
import Vuex, { createStore } from 'vuex'
import Header from '@/views/layout/Header.vue'

// todo

const store = createStore({
  state: {

  }
})

describe('header.vue', () => {
  it('role user', () => {
    const wrapper = shallowMount(Header, {
      store
    })
  })

  it('role admin', () => {
    const wrapper = shallowMount(Header, { store })
  })
})
