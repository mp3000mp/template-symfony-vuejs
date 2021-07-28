// https://docs.cypress.io/api/introduction/api.html

describe('Refresh password', () => {
  beforeEach(function () {
    cy.interceptInfo()
  })

  // todo factoriser connection
  // https://github.com/cypress-io/cypress-example-recipes/blob/master/examples/logging-in__html-web-forms/cypress/integration/logging-in-html-web-form-spec.js#L122

  it('Reset success', () => {
    cy.visit('/account')

    // todo
  })

  it('Reset fail', () => {
    cy.visit('/account')

    // todo
  })
})
