# frontend

## Project setup
```
yarn install
```
**Note**: it is important to use yarn package.json resolutions is not supported by npm and we need that in order to use cypress v6 instead of v3. This will not be necessary when upgrade to cli-plugin-e2e-cypress v5 will be done.


### Configuration

Create variable.json file from variable.example.json


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
npm run test:unit
```

### Run your end-to-end tests
```
# background
npm run test:e2e
# open cypress UI
npm run test:e2eui
```

### Lints and fixes files
```
npm run lint
npm run lint:style
```

### Customize configuration
See [Configuration Reference](https://cli.vuejs.org/config/).

### Devtool
[Firefox compatible vuejs 3 plugin](https://github.com/vuejs/vue-devtools/releases/tag/v6.0.0-beta.7)
