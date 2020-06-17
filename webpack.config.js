const path = require("path");
const VueLoaderPlugin = require("vue-loader/lib/plugin");
const dotenv = require("dotenv").config();
const webpack = require("webpack");
const production = process.env.NODE_ENV === "production";

let plugins = [];
plugins.push(new VueLoaderPlugin());
if (!production) plugins.push(new webpack.HotModuleReplacementPlugin());

module.exports = {
  mode: process.env.NODE_ENV || "production",
  entry: [path.resolve("./app")],
  output: {
    filename: "[name].js",
    path: path.resolve(
      path.join(
        production ? process.env.LIVE_BUILD_PATH : process.env.DEV_BUILD_PATH,
        "js"
      )
    ),
    publicPath: path.resolve(
      production ? process.env.LIVE_BASE : process.env.DEV_BASE
    ),
  },
  devServer: {
    contentBase: path.join(
      production ? process.env.LIVE_BUILD_PATH : process.env.DEV_BUILD_PATH,
      "js"
    ),
    port: 3000,
    hot: !production,
  },
  optimization: {
    splitChunks: {
      cacheGroups: {
        commons: {
          test: /[\\/]node_modules[\\/]/,
          name: "vendors",
          chunks: "all",
        },
      },
    },
  },
  plugins: plugins,
  resolve: {
    alias: {
      assets: path.resolve(__dirname, "./server/assets"),
      vue$: "vue/dist/vue.esm.js",
    },
  },
  module: {
    rules: [
      {
        test: /\.(png|jpe?g|gif|otf)$/i,
        loader: "file-loader",
        options: {
          name(resourcePath, resourceQuery) {
            if (process.env.NODE_ENV === "development") {
              return resourcePath.split("/server")[1];
            } else {
              return "[path][name].[ext]";
            }
          },
          emitFile: false,
        },
      },
      {
        test: /\.txt$/i,
        use: "raw-loader",
      },
      {
        test: /\.md$/i,
        use: [
          {
            loader: "html-loader",
          },
          {
            loader: "markdown-loader",
          },
        ],
      },
      {
        test: /\.vue$/,
        loader: "vue-loader",
        options: {
          loaders: {
            css: [
              "vue-style-loader",
              "css-loader",
              {
                loader: "less-loader",
              },
            ],
          },
        },
      },
      {
        test: /\.less$/,
        use: [
          "vue-style-loader",
          "css-loader",
          "postcss-loader",
          {
            loader: "less-loader",
          },
          {
            loader: "text-transform-loader",
            options: {
              prependText: '@import "./app/less/globals.less";',
            },
          },
        ],
      },
    ],
  },
};
