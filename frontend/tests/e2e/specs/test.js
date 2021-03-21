// https://docs.cypress.io/api/introduction/api.html

describe('My First Test', () => {
  it('Visits the app root url', () => {
    cy.visit('/')

    // mock response
    cy.intercept('POST', 'http://localhost:5000/api/logincheck', {
      statusCode: 200,
      body: {
        token: 'token',
        refreshToken: 'refreshToken'
      },
      delay: 250
    })

    // simulate network error
    /* cy.intercept('POST', 'http://localhost:5000/api/logincheck', {
      forceNetworkError: true
    }) */

    // request assertion
    /* cy.intercept('GET', '/api/users', req => {
      expect(req.headers.Authorization).toEqual('Bearer token')
    }) */

    cy.get('#nav').find('a').should('have.length', 2)

    cy.get('#11').should($p => {
      expect($p).to.contain('non')
    })
    cy.get('#22').should($p => {
      expect($p).to.contain('non')
    })
    cy.get('#logg').click()
    cy.get('#11').should($p => {
      expect($p).to.contain('oui')
    })
    cy.get('#22').should($p => {
      expect($p).to.contain('non')
    })

    cy.wait(1000)
    cy.get('#11').should($p => {
      expect($p).to.contain('non')
    })
    cy.get('#22').should($p => {
      expect($p).to.contain('oui')
    })
  })
})
