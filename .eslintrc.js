// http://eslint.org/docs/user-guide/configuring

module.exports = {
  root: true,
  env: {
    browser: true,
    node: true,
    es6: true,
    jquery: true,
  },
  globals: {
    google: true,
    document: true,
    navigator: false,
    window: true,
  },
  parserOptions: {
    parser: '@babel/eslint-parser',
  },
  extends: ['prestashop', 'plugin:vue/strongly-recommended'],
  plugins: ['import', 'vue'],
  rules: {
    'class-methods-use-this': 0,
    'func-names': 0,
    'import/no-extraneous-dependencies': 0,
    'max-len': ['error', {code: 120}],
    'no-alert': 0,
    'no-bitwise': 0,
    'no-new': 0,
    'no-param-reassign': ['error', {props: false}],
    'no-restricted-globals': [
      'error',
      {
        name: 'global',
        message: 'Use window variable instead.',
      },
    ],
    'no-restricted-syntax': 0,
    'prefer-destructuring': ['error', {object: true, array: false}],
    'vue/script-indent': [
      'error',
      2,
      {
        baseIndent: 1,
        switchCase: 1,
      },
    ],
    'vue/require-default-prop': 0,
  },
  settings: {
    'import/resolver': {
      node: {},
      webpack: 'webpack.config.js',
    },
  },
  overrides: [
    {
      files: ['*.vue'],
      rules: {
        indent: 0,
      },
    },
  ],
};
