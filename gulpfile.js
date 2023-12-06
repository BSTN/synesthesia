const path = require("path");
const fs = require("fs");
const gulp = require("gulp");
const _ = require("lodash");
const dotenv = require("dotenv").config();

const webpack = require("webpack");
const webpackStream = require("webpack-stream");
const webpackDevServer = require("webpack-dev-server");
const webpackConfig = require("./webpack.config.js");

let production = process.env.NODE_ENV === "production";

const watcher = () => {

  // hot reload
  webpackConfig.entry = [
    "webpack-dev-server/client?http://localhost:3000,
    "webpack/hot/only-dev-server",
    path.resolve("./app"),
  ];
  new webpackDevServer(webpack(webpackConfig), webpackConfig.devServer).listen(
    3000,
    'localhost',
    (err) => {
      console.warn(err);
    }
  );
};

gulp.task("webpackBuild", async () => {
  return gulp
    .src("app/index.js")
    .pipe(webpackStream(webpackConfig))
    .pipe(gulp.dest(path.join('./server/', "js")));
});

// access via package.json

gulp.task("dev", gulp.series([watcher]));

gulp.task("build", gulp.series(["copyfolder", "templates", "webpackBuild"]));

gulp.task("buildandupload", gulp.series(["build", "upload"]));
