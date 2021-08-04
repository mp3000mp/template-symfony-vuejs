// https://docs.cypress.io/api/introduction/api.html

describe('Logout', () => {
  it('Logout', () => {
    cy.loginByForm(['ROLE_USER', 'ROLE_ADMIN'])
    cy.visit('/')
    cy.location('pathname').should('eq', '/')
    cy.get('#nav').find('.nav-item').last().click()
    cy.location('pathname').should('eq', '/login')
    cy.get('#login-form').should('be.visible')
  })
})
