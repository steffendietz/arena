const path = require('path');

module.exports = {
  entry: './src/index.js',
  output: {
    filename: 'main.js',
    path: path.resolve(__dirname, '..', 'public', 'scripts'),
  },
  devServer: {
    index: '',
    contentBase: '../public',
    publicPath: '/scripts',
    hot: true,
    port: 8181,
    proxy: {
      context: () => true,
      target: 'http://localhost:8080',
      changeOrigin: true
    }
  },
};