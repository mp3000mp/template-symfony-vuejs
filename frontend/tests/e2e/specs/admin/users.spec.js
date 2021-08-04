// https://docs.cypress.io/api/introduction/api.html

describe('Admin users page', () => {
  beforeEach(function () {
    cy.interceptInfo()

    cy.intercept('/api/users', (req) => {
      req.reply({
        statusCode: 200,
        body: [
          {
            id: 1,
            email: 'user@mp3000.fr',
            username: 'user',
            isEnabled: true,
            roles: ['ROLE_USER']
          },
          {
            id: 2,
            email: 'admin@mp3000.fr',
            username: 'admin',
            isEnabled: true,
            roles: ['ROLE_USER', 'ROLE_ADMIN']
          },
          {
            id: 3,
            email: 'disabled@mp3000.fr',
            username: 'disabled',
            isEnabled: false,
            roles: ['ROLE_USER']
          }
        ]
      })
    })
  })

  it('Unauthenticated', () => {
    cy.visit('/admin/users')
    cy.location('pathname').should('eq', '/login')
  })

  it('Fodbidden', () => {
    cy.loginByForm(['ROLE_USER'])
    cy.visit('/admin/users')
    cy.location('pathname').should('eq', '/')
  })

  it('Show page', () => {
    cy.loginByForm(['ROLE_USER', 'ROLE_ADMIN'])
    cy.visit('/admin/users')
    cy.get('tr').should('have.length', 4)
  })
})
