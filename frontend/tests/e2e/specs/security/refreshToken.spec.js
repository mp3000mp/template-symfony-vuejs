// https://docs.cypress.io/api/introduction/api.html

describe('Refresh token', () => {
  beforeEach(function () {
    cy.interceptInfo()
  })

  // todo factoriser connection
  // https://github.com/cypress-io/cypress-example-recipes/blob/master/examples/logging-in__html-web-forms/cypress/integration/logging-in-html-web-form-spec.js#L122

  it('Refresh success', () => {
    cy.visit('/login')

    // todo
  })

  it('Refresh fail', () => {
    cy.visit('/login')

    // todo
  })
})
