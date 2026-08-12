const path = require('path');
const fs = require('fs');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

const entries = {
  shadow: './src/Assets/js/shadow.js',
  bootstrap: [
    './src/Assets/js/bootstrap.js',
    './src/Assets/scss/bootstrap.scss',
  ],
  'admin.ui': [
    './src/Assets/js/admin.ui.js',
    './src/Assets/scss/admin.ui.scss',
  ],
  'wpoverride': './src/Assets/scss/wpoverride.scss',
  'bootstrap-select': [
    './src/Assets/js/bootstrap-select.js',
    './src/Assets/scss/bootstrap-select.scss',
  ],
  'admin.internal-wiki': './src/Includes/Plugins/InternalWiki/Assets/js/admin.internal-wiki.js',
};

const fontAwesomeEntries = {
  'icon-picker': [
    './src/includes/Plugins/FontAwesome/Assets/js/icon-picker.js',
    './src/includes/Plugins/FontAwesome/Assets/scss/icon-picker.scss',
  ],
};

const elementorEntries = {
  wiki: [
    './src/includes/Plugins/Elementor/Assets/js/WikiPress/wiki.js',
    './src/includes/Plugins/Elementor/Assets/scss/WikiPress/wiki.scss',
  ],
};

const gutenburgEntries = {
  blocks: './src/includes/Plugins/Gutenburg/Assets/js/blocks.js',
};

const userRolesManagerEntries = {
  'user-roles-manager': [
    './src/includes/Plugins/UserRolesManager/Assets/js/user-roles-manager.js',
    './src/includes/Plugins/UserRolesManager/Assets/scss/user-roles-manager.scss',
  ],
};

const jsDirectory = path.resolve(__dirname, 'src/Assets/js');
fs.readdirSync(jsDirectory)
  .filter((file) => /^admin\.[^.]+\.js$/.test(file) && file !== 'admin.ui.js')
  .forEach((file) => {
    const page = file.match(/^admin\.([^.]+)\.js$/)[1];
    const entry = [`./src/Assets/js/${file}`];
    entries[`admin.${page}`] = entry;
  });

const shared = {
  mode: process.env.NODE_ENV === 'development' ? 'development' : 'production',
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
  optimization: { splitChunks: false },
};

module.exports = [
  {
    ...shared,
    entry: entries,
    output: {
      path: path.resolve(__dirname, 'src/Assets/dist'),
      filename: 'js/[name].js',
      clean: true,
    },
    plugins: [
      new MiniCssExtractPlugin({ filename: 'css/[name].css' }),
    ],
  },
  {
    ...shared,
    entry: fontAwesomeEntries,
    output: {
      path: path.resolve(__dirname, 'src/includes/Plugins/FontAwesome/Assets/dist'),
      filename: 'js/[name].js',
      clean: true,
    },
    plugins: [
      new MiniCssExtractPlugin({ filename: 'css/[name].css' }),
    ],
  },
  {
    ...shared,
    entry: elementorEntries,
    output: {
      path: path.resolve(__dirname, 'src/includes/Plugins/Elementor/Assets/dist'),
      filename: 'js/[name].js',
      clean: true,
    },
    plugins: [
      new MiniCssExtractPlugin({ filename: 'css/[name].css' }),
    ],
  },
  {
    ...shared,
    entry: gutenburgEntries,
    output: {
      path: path.resolve(__dirname, 'src/includes/Plugins/Gutenburg/Assets/dist'),
      filename: 'js/[name].js',
      clean: true,
    },
    plugins: [
      new MiniCssExtractPlugin({ filename: 'css/[name].css' }),
    ],
  },
  {
    ...shared,
    entry: userRolesManagerEntries,
    output: {
      path: path.resolve(__dirname, 'src/includes/Plugins/UserRolesManager/Assets/dist'),
      filename: 'js/[name].js',
      clean: true,
    },
    plugins: [
      new MiniCssExtractPlugin({ filename: 'css/[name].css' }),
    ],
  },
];
