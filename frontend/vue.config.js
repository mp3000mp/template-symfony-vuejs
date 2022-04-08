// eslint-disable-next-line
const StyleLintPlugin = require('stylelint-webpack-plugin')

module.exports = {
  configureWebpack: {
    module: {
      rules: [
        {
          test: /\.mjs$/,
          include: /node_modules/,
          type: 'javascript/auto'
        }
      ]
    },
    plugins: [
      new StyleLintPlugin({
        files: ['src/**/*.{vue,scss}'],
        fix: true
      })
    ]
  }
}
