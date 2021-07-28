// ***********************************************
// This example commands.js shows you how to
// create various custom commands and overwrite
// existing commands.
//
// For more comprehensive examples of custom
// commands please read more here:
// https://on.cypress.io/custom-commands
// ***********************************************
//
//
// -- This is a parent command --
// Cypress.Commands.add("login", (email, password) => { ... })
//
//
// -- This is a child command --
// Cypress.Commands.add("drag", { prevSubject: 'element'}, (subject, options) => { ... })
//
//
// -- This is a dual command --
// Cypress.Commands.add("dismiss", { prevSubject: 'optional'}, (subject, options) => { ... })
//
//
// -- This is will overwrite an existing command --
// Cypress.Commands.overwrite("visit", (originalFn, url, options) => { ... })

Cypress.Commands.add('interceptInfo', () => {
  cy.intercept('/api/info', (req) => {
    req.reply({
      statusCode: 200,
      body: {
        version: '1.e2e'
      }
    })
  })
})

Cypress.Commands.add('loginByForm', (roles) => {
  cy.intercept('/api/logincheck', (req) => {
    expect(req.body.username).to.include('goodUsername')
    expect(req.body.password).to.include('goodPassword')
    req.reply({
      statusCode: 200,
      body: {
        token: 'goodToken',
        refreshToken: 'refreshToken'
      }
    })
  })
  cy.intercept('/api/me', (req) => {
    req.reply({
      statusCode: 200,
      body: {
        username: 'goodUsername',
        roles: roles
      }
    })
  })

  // login
  cy.visit('/login')
  cy.get('#username').type('goodUsername')
  cy.get('#password').type('goodPassword')
  cy.get('input[type="submit"]').first().click()

  cy.location('pathname').should('eq', '/')
})
