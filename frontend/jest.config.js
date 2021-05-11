module.exports = {
  preset: '@vue/cli-plugin-unit-jest/presets/typescript-and-babel',
  transform: {
    '^.+\\.vue$': 'vue-jest'
  },
  collectCoverageFrom: ['src/**/*.{js,ts,vue}'],
  coveragePathIgnorePatterns: ['tests', 'src/router/*.*', 'src/*.{js,ts,vue}'],
  coverageDirectory: './ci/coverage/unit'
}
