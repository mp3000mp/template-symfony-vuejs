// https://docs.cypress.io/api/introduction/api.html

describe('Account page', () => {
  beforeEach(function () {
    cy.interceptInfo()
  })

  it('Unauthenticated', () => {
    cy.visit('/account')
    cy.location('pathname').should('eq', '/login')
  })

  it('Show page', () => {
    cy.loginByForm(['ROLE_USER'])

    cy.visit('/account')
    cy.get('td').first().should('have.text', 'goodUsername')
  })
})
