// https://docs.cypress.io/api/introduction/api.html

describe('Account page', () => {
  beforeEach(function () {
    cy.interceptInfo()
  })

  it('Unauthenticated', () => {
    cy.visit('/account')
    cy.location('pathname').should('eq', '/login')
  })

  it('Show page', () => {
    cy.loginByForm(['ROLE_USER'])

    cy.visit('/account')
    cy.get('td').first().should('have.text', 'goodUsername')
  })

  it('Reset password ok', () => {
    cy.loginByForm(['ROLE_USER'])

    cy.visit('/account')
    cy.get('td').first().should('have.text', 'goodUsername')

    cy.intercept('/api/password/reset', (req) => {
      expect(req.body.currentPassword).to.eq('currentPassword')
      expect(req.body.newPassword).to.eq('newPassword')
      expect(req.body.newPassword2).to.eq('newPassword')
      req.reply({
        statusCode: 200,
        body: {
          message: 'The password has been reset successfully'
        }
      })
    })

    cy.get('button.btn-primary').first().should('have.text', 'Reset password').click()
    cy.get('#current_password').type('currentPassword')
    cy.get('#new_password').type('newPassword')
    cy.get('#new_password2').type('newPassword')
    cy.get('form input.btn-primary').first().should('have.value', 'Reset password').click()

    cy.get('.success').first().should('have.text', 'The password has been reset successfully')
  })
})
