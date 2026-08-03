const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

const pageNames = ['dashboard', 'content', 'page', 'settings', 'analytics'];
const entries = {
  bootstrap: [
    './src/Assets/js/bootstrap.js',
    './src/Assets/scss/bootstrap.scss',
  ],
};

pageNames.forEach((page) => {
  entries[`admin.${page}`] = [ `./src/Assets/js/admin.${page}.js`, `./src/Assets/scss/_${page}.scss` ];
});

module.exports = {
  mode: process.env.NODE_ENV === 'development' ? 'development' : 'production',
  entry: entries,
  output: {
    path: path.resolve(__dirname, 'src/Assets/dist'),
    filename: 'js/[name].js',
    clean: true,
  },
  devtool: process.env.NODE_ENV === 'development' ? 'source-map' : false,
  module: {
    rules: [
      {
        test: /\.scss$/,
        use: [
          MiniCssExtractPlugin.loader,
          'css-loader',
          {
            loader: 'sass-loader',
            options: {
              api: 'modern',
              sassOptions: {
                quietDeps: true,
                includePaths: [path.resolve(__dirname, 'src/Assets/scss')],
              },
            },
          },
        ],
      },
      {
        test: /\.css$/,
        use: [MiniCssExtractPlugin.loader, 'css-loader'],
      },
      {
        test: /\.js$/,
        exclude: /node_modules/,
        type: 'javascript/auto',
      },
    ],
  },
  optimization: {
    splitChunks: false,
  },
  plugins: [
    new MiniCssExtractPlugin({
      filename: 'css/[name].css',
    }),
  ],
};
