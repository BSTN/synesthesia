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
  stats: "minimal",
  output: {
    filename: "[name].js",
    path: path.resolve(
      path.join(
        production ? process.env.LIVE_BUILD_PATH : process.env.DEV_BUILD_PATH,
        "js"
      )
    ),
    publicPath: production
      ? process.env.LIVE_BASE
      : "http://" +
        process.env.DEV_HOTHOST +
        ":" +
        process.env.DEV_HOTPORT +
        "/",
  },
  devServer: {
    contentBase: process.env.DEV_BUILD_PATH,
    publicPath: "/",
    hot: !production,
    port: process.env.DEV_HOTPORT || 3000,
    host: process.env.DEV_HOTHOST,
    headers: {
      "Access-Control-Allow-Origin": "*",
    },
    stats: {
      colors: true,
      hash: false,
      version: false,
      timings: false,
      assets: false,
      chunks: false,
      modules: false,
      reasons: false,
      children: false,
      source: false,
      errors: true,
      errorDetails: true,
      warnings: true,
      publicPath: false,
    },
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
      assets: path.resolve(__dirname, "./server/assets/"),
      vue$: "vue/dist/vue.esm.js",
    },
  },
  module: {
    rules: [
      {
        test: /\.(png|jpe?g|gif|otf|woff|mp4)$/i,
        loader: "file-loader",
        options: {
          name(resourcePath, resourceQuery) {
            return resourcePath.split("/server")[1];
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
