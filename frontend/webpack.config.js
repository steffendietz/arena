const path = require('path');

module.exports = {
  entry: './src/index.js',
  output: {
    filename: 'main.js',
    path: path.resolve(__dirname, '..', 'public', 'scripts'),
  },
  devServer: {
    hot: true,
    devMiddleware: {
      publicPath: '/scripts',
    },
    static: {
      directory: path.resolve(__dirname, '..', 'public'),
    },
    port: 8282,
    proxy: {
      context: () => true,
      target: 'http://localhost:8181'
    }
  },
};