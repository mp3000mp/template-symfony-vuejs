// https://docs.cypress.io/api/introduction/api.html

describe('Login', () => {
  beforeEach(function () {
    cy.interceptInfo()
  })

  const testingDatasets = [
    {
      name: 'user',
      roles: ['ROLE_USER'],
      expectedMenuItemCount: 3
    },
    {
      name: 'admin',
      roles: ['ROLE_USER', 'ROLE_ADMIN'],
      expectedMenuItemCount: 4
    }
  ]
  for (const testingDataset of testingDatasets) {
    it(`Login success ${testingDataset.name}`, () => {
      cy.visit('/')
      cy.get('#nav').find('.nav-item').should('have.length', 1)
      cy.loginByForm(testingDataset.roles)
      cy.get('#nav').find('.nav-item').should('have.length', testingDataset.expectedMenuItemCount)
    })
  }

  it('Login fail', () => {
    cy.intercept('/api/logincheck', (req) => {
      req.reply({
        statusCode: 401,
        body: {
          message: 'Invalid credentials.'
        }
      })
    })

    cy.visit('/login')
    cy.get('#nav').find('.nav-item').should('have.length', 1)

    // login
    cy.get('#username').type('badUsername')
    cy.get('#password').type('badPassword')
    cy.get('input[type="submit"]').first().click()

    cy.location('pathname').should('eq', '/login')
    cy.get('#nav').find('.nav-item').should('have.length', 1)
    cy.get('#login-form').find('.err').first().should('have.text', 'Invalid credentials.')
  })

  it('Call endpoint, then expired failed, then redirect login', () => {
    cy.visit('/login')

    // todo
  })
})
