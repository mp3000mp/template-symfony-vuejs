// https://docs.cypress.io/api/introduction/api.html

describe('Security', () => {
  it('Login success and refresh success', () => {
    cy.visit('/')

    // mock response
    cy.intercept('POST', 'http://localhost:5000/api/logincheck', {
      statusCode: 200,
      body: {
        token: 'token',
        refreshToken: 'refreshToken'
      },
      delay: 50
    })
    cy.intercept('POST', 'http://localhost:5000/api/token/refresh', {
      statusCode: 200,
      body: {
        token: 'token2',
        refreshToken: 'refreshToken2'
      },
      delay: 50
    })

    // login
    cy.get('#log_but').click()
    cy.get('#log_loading').should($p => {
      expect($p).to.contain('oui')
    })
    cy.get('#log_status').should($p => {
      expect($p).to.contain('non')
    })
    cy.wait(100)
    cy.get('#log_loading').should($p => {
      expect($p).to.contain('non')
    })
    cy.get('#log_status').should($p => {
      expect($p).to.contain('oui')
    })

    // refresh
    cy.get('#refresh_but').click()
    cy.get('#log_loading').should($p => {
      expect($p).to.contain('oui')
    })
    cy.get('#log_status').should($p => {
      expect($p).to.contain('oui')
    })
    cy.wait(100)
    cy.get('#log_loading').should($p => {
      expect($p).to.contain('non')
    })
    cy.get('#log_status').should($p => {
      expect($p).to.contain('oui')
    })
  })

  it('Login fail', () => {
    cy.visit('/')

    // mock response
    cy.intercept('POST', 'http://localhost:5000/api/logincheck', {
      statusCode: 401,
      body: {
        message: 'err msg'
      },
      delay: 50
    })

    cy.get('#log_but').click()
    cy.get('#log_loading').should($p => {
      expect($p).to.contain('oui')
    })
    cy.get('#log_status').should($p => {
      expect($p).to.contain('non')
    })
    cy.wait(100)
    cy.get('#log_loading').should($p => {
      expect($p).to.contain('non')
    })
    cy.get('#log_status').should($p => {
      expect($p).to.contain('non')
    })
    cy.get('#log_err').should($p => {
      expect($p).to.contain('err msg')
    })
  })

  it('Login network error', () => {
    cy.visit('/')

    // simulate network error
    cy.intercept('POST', 'http://localhost:5000/api/logincheck', {
      forceNetworkError: true
    })

    cy.get('#log_but').click()
    // todo: mettre un délai
    /* cy.get('#log_loading').should($p => {
      expect($p).to.contain('oui')
    }) */
    cy.get('#log_status').should($p => {
      expect($p).to.contain('non')
    })
    cy.wait(100)
    cy.get('#log_loading').should($p => {
      expect($p).to.contain('non')
    })
    cy.get('#log_status').should($p => {
      expect($p).to.contain('non')
    })
    cy.get('#log_err').should($p => {
      expect($p).to.contain('Network Error')
    })
  })

  it('Expired refresh success', () => {
    cy.visit('/')

    // mock response
    cy.intercept('POST', 'http://localhost:5000/api/logincheck', {
      statusCode: 200,
      body: {
        token: 'token',
        refreshToken: 'refreshToken'
      },
      delay: 50
    })
    cy.intercept('POST', 'http://localhost:5000/api/token/refresh', {
      statusCode: 200,
      body: {
        token: 'token2',
        refreshToken: 'refreshToken2'
      },
      delay: 200
    })
    const usersCall = 0
    /* cy.intercept('GET', 'http://localhost:5000/api/users', req => {
      req.reply(res => {
        if (usersCall === 0) {
          usersCall++
          res.send({
            statusCode: 401,
            body: {
              message: 'Expired JWT Token'
            },
            delay: 50
          })
        } else {
          res.send({
            statusCode: 200,
            body: [{
              id: 1,
              username: 'mp3000'
            }],
            delay: 50
          })
        }
      })
    }) */
    // get refresh token
    cy.get('#log_but').click()
    cy.wait(100)

    cy.get('#user_but').click()
    cy.get('#user_loading').should($p => {
      expect($p).to.contain('oui')
    })
    cy.get('#user_status').should($p => {
      expect($p).to.contain('0')
    })
    cy.wait(100)
    cy.get('#user_loading').should($p => {
      expect($p).to.contain('oui')
    })
    cy.get('#user_status').should($p => {
      expect($p).to.contain('0')
    })
    cy.get('#user_err').should($p => {
      expect($p).to.not.contain('token')
    })

    cy.wait(300)
    cy.get('#user_loading').should($p => {
      expect($p).to.contain('non')
    })
    cy.get('#user_status').should($p => {
      expect($p).to.contain('1')
    })
  })

  it('Expired refresh fail', () => {
    cy.visit('/')

    // mock response
    cy.intercept('POST', 'http://localhost:5000/api/logincheck', {
      statusCode: 200,
      body: {
        token: 'token',
        refreshToken: 'refreshToken'
      },
      delay: 50
    })
    cy.intercept('POST', 'http://localhost:5000/api/token/refresh', {
      statusCode: 401,
      body: {
        message: 'Bad token'
      },
      delay: 50
    })
    cy.intercept('GET', 'http://localhost:5000/api/users', {
      statusCode: 401,
      body: {
        message: 'Expired JWT Token'
      },
      delay: 50
    })
    // get refresh token
    cy.get('#log_but').click()
    cy.wait(100)

    cy.get('#user_but').click()
    cy.get('#user_loading').should($p => {
      expect($p).to.contain('oui')
    })
    cy.get('#user_status').should($p => {
      expect($p).to.contain('0')
    })
    cy.wait(200)
    cy.get('#user_loading').should($p => {
      expect($p).to.contain('non')
    })
    cy.get('#user_status').should($p => {
      expect($p).to.contain('0')
    })
    cy.get('#user_err').should($p => {
      expect($p).to.contain('Token')
    })
  })

  it('Expired refresh network error', () => {
    cy.visit('/')

    // mock response
    cy.intercept('POST', 'http://localhost:5000/api/logincheck', {
      statusCode: 200,
      body: {
        token: 'token',
        refreshToken: 'refreshToken'
      },
      delay: 50
    })
    cy.intercept('POST', 'http://localhost:5000/api/token/refresh', {
      forceNetworkError: true
    })
    cy.intercept('GET', 'http://localhost:5000/api/users', {
      statusCode: 401,
      body: {
        message: 'Expired JWT Token'
      },
      delay: 50
    })
    // get refresh token
    cy.get('#log_but').click()
    cy.wait(100)

    cy.get('#user_but').click()
    cy.get('#user_loading').should($p => {
      expect($p).to.contain('oui')
    })
    cy.get('#user_status').should($p => {
      expect($p).to.contain('0')
    })
    cy.wait(200)
    cy.get('#user_loading').should($p => {
      expect($p).to.contain('non')
    })
    cy.get('#user_status').should($p => {
      expect($p).to.contain('0')
    })
    cy.get('#log_err').should($p => {
      expect($p).to.contain('Network')
    })
    cy.get('#user_err').should($p => {
      expect($p).to.contain('Token')
    })
  })

  it('It requests and ask for log in', () => {
    // todo
  })
})
