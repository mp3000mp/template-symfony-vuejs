// https://docs.cypress.io/api/introduction/api.html

describe('Security', () => {
  it('Visits the app root url', () => {
    cy.visit('/')

    cy.get('#nav').find('a').should('have.length', 2)
  })
})
