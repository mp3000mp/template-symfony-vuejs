# frontend

## Project setup
```
yarn install
```
**Note**: it is important to use yarn because package.json `resolutions` is not supported by npm and we need that in order to use cypress v6 instead of v3. 
This will not be necessary when upgrade to cli-plugin-e2e-cypress v5 will be done.


### Configuration

Create variable.json file from variable.example.json in config directory


### Compiles and hot-reloads for development
```
npm run serve
```


### Compiles and minifies for production
```
npm run build
```


### Run your unit tests
```
# Unit test
npm run test:unit

# Unit test with coverage
npm run test:ucov
```


### Run your end-to-end tests
```
# Background
npm run test:e2e

# Open Cypress UI
npm run test:e2eui
```


### Lints and fixes files
```
npm run lint
npm run lint:style
```


### Devtool
[Firefox compatible vuejs 3 plugin](https://github.com/vuejs/vue-devtools/releases/tag/v6.0.0-beta.7)
