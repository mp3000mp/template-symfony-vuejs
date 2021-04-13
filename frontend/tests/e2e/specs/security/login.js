// https://docs.cypress.io/api/introduction/api.html

describe('Login', () => {
  it('Login success user', () => {
    cy.intercept('http://localhost:5000/api/logincheck', (req) => {
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
    cy.intercept('http://localhost:5000/api/me', (req) => {
      req.reply({
        statusCode: 200,
        body: {
          username: 'goodUsername',
          roles: ['ROLE_USER']
        }
      })
    })

    cy.visit('/login')
    cy.get('#nav').find('.nav-item').should('have.length', 1)

    // login
    cy.get('#username').type('goodUsername')
    cy.get('#password').type('goodPassword')
    cy.get('input[type="submit"]').first().click()

    cy.location('pathname').should('eq', '/')
    cy.get('#nav').find('.nav-item').should('have.length', 3)
  })

  // todo factoriser
  it('Login success admin', () => {
    cy.visit('/login')
    cy.get('#nav').find('.nav-item').should('have.length', 1)

    cy.intercept('POST', 'http://localhost:5000/api/logincheck', {
      statusCode: 200,
      body: {
        token: 'goodToken',
        refreshToken: 'refreshToken'
      },
      delay: 25
    })
    // todo check request.body avant de mocker la response
    /* cy.intercept('POST', 'http://localhost:5000/api/logincheck', req => {
      expect(req.body.username).to.eq('goodUsername')
      expect(req.body.password).to.eq('goodPassword')
      req.reply(res => {
        res.send({
          statusCode: 200,
          body: {
            token: 'goodToken',
            refreshToken: 'refreshToken'
          },
          delay: 25
        })
      })
    }) */

    cy.intercept('GET', 'http://localhost:5000/api/me', {
      statusCode: 200,
      body: {
        username: 'goodUsername',
        roles: ['ROLE_USER', 'ROLE_ADMIN']
      },
      delay: 25
    })
    // todo check request.body avant de mocker la response
    /* cy.intercept('POST', 'http://localhost:5000/api/logincheck', req => {
      expect(req.body.username).to.eq('goodUsername')
      expect(req.body.password).to.eq('goodPassword')
      req.reply(res => {
        res.send({
          statusCode: 200,
          body: {
            token: 'goodToken',
            refreshToken: 'refreshToken'
          },
          delay: 25
        })
      })
    }) */

    // login
    cy.get('#username').type('goodUsername')
    cy.get('#password').type('goodPassword')
    cy.get('input[type="submit"]').first().click()

    cy.wait(100)

    cy.location('pathname').should('eq', '/')
    cy.get('#nav').find('.nav-item').should('have.length', 4)
  })

  it('Login fail', () => {
    cy.visit('/login')
    cy.get('#nav').find('.nav-item').should('have.length', 1)

    cy.intercept('POST', 'http://localhost:5000/api/logincheck', {
      statusCode: 401,
      body: {
        message: 'Invalid credentials.'
      },
      delay: 25
    })
    // todo check request.body avant de mocker la response
    /* cy.intercept('POST', 'http://localhost:5000/api/logincheck', req => {
      expect(req.body.username).to.eq('goodUsername')
      expect(req.body.password).to.eq('goodPassword')
      req.reply(res => {
        res.send({
          statusCode: 200,
          body: {
            token: 'goodToken',
            refreshToken: 'refreshToken'
          },
          delay: 25
        })
      })
    }) */

    // login
    cy.get('#username').type('badUsername')
    cy.get('#password').type('badPassword')
    cy.get('input[type="submit"]').first().click()

    cy.wait(100)

    cy.location('pathname').should('eq', '/login')
    cy.get('#nav').find('.nav-item').should('have.length', 1)
  })

  it('Call endpoint, then expired failed, then redirect login', () => {
    cy.visit('/login')

    // todo
  })
})
