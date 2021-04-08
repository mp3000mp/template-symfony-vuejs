// https://docs.cypress.io/api/introduction/api.html

describe('Forgotten password', () => {
  it('Forgotten password', () => {
    cy.visit('/login')

    cy.intercept('POST', 'http://localhost:5000/api/password/forgotten', {
      statusCode: 200,
      body: {
        message: 'OK'
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

    // todo check #email hidden

    cy.get('button').click()
    cy.get('#email').should('be.visible').type('goodEmail@mp3000.fr')
    cy.get('input[type="submit"]').eq(1).click()

    cy.wait(100)

    cy.get('.success').first().contains('OK')
    cy.get('#email').should('be.empty', '')
  })

  it('Forgotten password reset success', () => {
    cy.visit('/password/forgotten/goodToken')

    // todo blocké par CORS ? pk envoyé au serveur ?
    cy.intercept('GET', 'http://localhost:5000/api/password/forgotten/goodToken', {
      statusCode: 200,
      body: {
        message: 'OK'
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

    cy.intercept('POST', 'http://localhost:5000/api/password/forgotten/goodToken', {
      statusCode: 200,
      body: {
        message: 'Token is valid.'
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

    cy.wait(100)

    cy.get('#pwd').should('be.visible').type('goodPassword')
    cy.get('input[type="submit"]').first().click()

    cy.wait(100)

    cy.get('.success').first().contains('Token is valid')
    cy.get('pwd').eq(1).should('be.empty', '')

    cy.wait(2100)
    cy.location('pathname').should('eq', '/login')
  })

  it('Forgotten password token expired', () => {
    cy.visit('/password/forgotten/badToken')

    // todo blocké par CORS ?
    cy.intercept('GET', 'http://localhost:5000/api/password/forgotten/badToken', {
      statusCode: 404,
      body: {
        message: 'This token has expired.'
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

    cy.wait(100)

    cy.get('.err').first().contains('This token has expired')
  })

  it('Init password success', () => {
    cy.visit('/password/init/goodToken')

    // todo blocké par CORS ? pk envoyé au serveur ?
    cy.intercept('GET', 'http://localhost:5000/api/password/init/goodToken', {
      statusCode: 200,
      body: {
        message: 'OK'
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

    cy.intercept('POST', 'http://localhost:5000/api/password/init/goodToken', {
      statusCode: 200,
      body: {
        message: 'Token is valid.'
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

    cy.wait(100)

    cy.get('#pwd').should('be.visible').type('goodPassword')
    cy.get('input[type="submit"]').first().click()

    cy.wait(100)

    cy.get('.success').first().contains('Token is valid')
    cy.get('pwd').eq(1).should('be.empty', '')

    cy.wait(2100)
    cy.location('pathname').should('eq', '/login')
  })

  it('Init password token expired', () => {
    cy.visit('/password/init/badToken')

    // todo blocké par CORS ?
    cy.intercept('GET', 'http://localhost:5000/api/password/init/badToken', {
      statusCode: 404,
      body: {
        message: 'This token has expired.'
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

    cy.wait(100)

    cy.get('.err').first().contains('This token has expired')
  })
})
