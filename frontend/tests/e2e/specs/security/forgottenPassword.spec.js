// https://docs.cypress.io/api/introduction/api.html

describe('Forgotten password', () => {
  beforeEach(function () {
    cy.interceptInfo()
  })

  it('Login form forgotten password 200', () => {
    cy.intercept('/api/password/forgotten', (req) => {
      expect(req.body.email).to.include('goodEmail@mp3000.fr')
      req.reply({
        statusCode: 200,
        body: {
          message: 'If this account exists, an email has been sent to goodEmail@mp3000.fr'
        }
      })
    })

    cy.visit('/login')

    cy.get('#email').should('not.exist')
    cy.get('button[data-cy=forgottenPasswordButton]').click()
    cy.get('#email').should('be.visible').type('goodEmail@mp3000.fr')
    cy.get('input[type="submit"]').eq(1).click()

    cy.get('#forgotten-password-form').find('.success').first().contains('goodEmail@mp3000.fr')
    cy.get('#email').should('be.empty', '')
  })

  it('Forgotten password reset success', () => {
    cy.intercept('/api/password/forgotten/goodToken', (req) => {
      req.reply({
        statusCode: 200,
        body: {
          message: 'Token is valid.'
        }
      })
    })
    cy.intercept('POST', '/api/password/forgotten/goodToken', (req) => {
      expect(req.body.password).toStrictEqual('goodPassword')
      expect(req.body.passwordConfirm).toStrictEqual('goodPassword')
      req.reply({
        statusCode: 200,
        body: {
          message: 'The password has been reset successfully.'
        }
      })
    })

    cy.visit('/password/forgotten/goodToken')

    cy.get('#pwd').should('be.visible').type('goodPassword')
    cy.get('#pwd_confirm').should('be.visible').type('goodPassword')
    cy.get('input[type="submit"]').first().click()

    cy.get('#init-password-form').find('.success').first().contains('Token is valid.')
    cy.get('#pwd').should('not.exist')

    cy.location('pathname').should('eq', '/login')
  })

  it('Forgotten password token expired', () => {
    cy.intercept('GET', '/api/password/forgotten/badToken', (req) => {
      req.reply({
        statusCode: 404,
        body: {
          message: 'This token has expired.'
        }
      })
    })

    cy.visit('/password/forgotten/badToken')

    cy.get('#init-password-form').find('.err').first().contains('This token has expired.')
  })

  it('Init password success', () => {
    cy.intercept('GET', '/api/password/init/goodToken', (req) => {
      req.reply({
        statusCode: 200,
        body: {
          message: 'Token is valid.'
        }
      })
    })
    cy.intercept('POST', '/api/password/init/goodToken', (req) => {
      req.reply({
        statusCode: 200,
        body: {
          message: 'The password has been reset successfully.'
        }
      })
    })

    cy.visit('/password/init/goodToken')

    cy.get('#pwd').should('be.visible').type('goodPassword')
    cy.get('#pwd_confirm').should('be.visible').type('goodPassword')
    cy.get('input[type="submit"]').first().click()

    cy.get('#init-password-form').find('.success').first().contains('The password has been reset successfully.')
    cy.get('#pwd').should('not.exist')

    cy.location('pathname').should('eq', '/login')
  })

  it('Init password token expired', () => {
    cy.intercept('GET', '/api/password/init/badToken', (req) => {
      req.reply({
        statusCode: 404,
        body: {
          message: 'This token has expired.'
        }
      })
    })

    cy.visit('/password/init/badToken')

    cy.get('#init-password-form').find('.err').first().contains('This token has expired.')
  })
})
