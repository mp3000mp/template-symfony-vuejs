import apiRegistry from '@/helpers/apiRegistry'

describe('apiRegistry.ts', () => {
  it('retrieve set api', () => {
    const testedBaseUrl = 'http://mp3000.fr'
    apiRegistry.set('test', testedBaseUrl, function () {
      // do nothing
    })
    const apiClient = apiRegistry.get('test')

    expect(apiClient.baseUrl).toStrictEqual(testedBaseUrl)
  })
})
