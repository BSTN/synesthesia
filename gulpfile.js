const path = require("path");
const fs = require("fs");
const gulp = require("gulp");
const _ = require("lodash");
const template = require("gulp-template");
const dotenv = require("dotenv").config();

const webpack = require("webpack");
const webpackStream = require("webpack-stream");
const webpackDevServer = require("webpack-dev-server");
const webpackConfig = require("./webpack.config.js");

var rsync = require("gulp-rsync");

let production = process.env.NODE_ENV === "production";

// templates

gulp.task("templates", function(cb) {
  let templateVars = {
    SRCV: production
      ? "js/vendors.js"
      : "http://localhost:" + process.env.DEV_HOTPORT + "/vendors.js",
    SRC: production
      ? "js/main.js"
      : "http://localhost:" + process.env.DEV_HOTPORT + "/main.js",
    BASE: production ? process.env.LIVE_BASE : process.env.DEV_BASE,
    MYSQL_USERNAME: production
      ? process.env.LIVE_MYSQL_USERNAME
      : process.env.DEV_MYSQL_USERNAME,
    MYSQL_PASSWORD: production
      ? process.env.LIVE_MYSQL_PASSWORD
      : process.env.DEV_MYSQL_PASSWORD,
    MYSQL_HOST: production
      ? process.env.LIVE_MYSQL_HOST
      : process.env.DEV_MYSQL_HOST,
    MYSQL_PORT: production
      ? process.env.LIVE_MYSQL_PORT
      : process.env.DEV_MYSQL_PORT,
    MYSQL_DBNAME: production
      ? process.env.LIVE_MYSQL_DBNAME
      : process.env.DEV_MYSQL_DBNAME,
  };

  // define path
  let pad = production
    ? process.env.LIVE_BUILD_PATH
    : process.env.DEV_BUILD_PATH;
  pad = path.join(pad, "config.php");
  // compile string
  let filestring = "<?php\n";
  _.each(templateVars, (v, k) => {
    filestring += `define("${k}", "${v}");\n`;
  });
  fs.writeFile(pad, filestring, cb);
});

gulp.task("copyfolder", () => {
  return gulp
    .src(["server/**/*"], {
      dot: true,
    })
    .pipe(
      gulp.dest(
        production ? process.env.LIVE_BUILD_PATH : process.env.DEV_BUILD_PATH
      )
    );
});

const watcher = () => {
  // watch templates
  gulp.watch("templates/*.php", gulp.series(["templates"]));

  // watch and copy folder contents
  gulp.watch(
    ["server/**/*", "server/**/.htaccess"],
    gulp.series(["copyfolder"])
  );

  // hot reload
  webpackConfig.entry = [
    "webpack-dev-server/client?http://localhost:" + process.env.DEV_HOTPORT,
    "webpack/hot/only-dev-server",
    path.resolve("./app"),
  ];
  new webpackDevServer(webpack(webpackConfig), webpackConfig.devServer).listen(
    process.env.DEV_HOTPORT,
    "localhost",
    (err) => {
      console.warn(err);
    }
  );
};

gulp.task("webpackBuild", async () => {
  return gulp
    .src("app/index.js")
    .pipe(webpackStream(webpackConfig))
    .pipe(gulp.dest(path.join(process.env.LIVE_BUILD_PATH, "js")));
});

// access via package.json

gulp.task("dev", gulp.series(["copyfolder", "templates", watcher]));

gulp.task("build", gulp.series(["copyfolder", "templates", "webpackBuild"]));

gulp.task("upload", function() {
  return gulp.src("live/**").pipe(
    rsync({
      root: "live/",
      hostname: process.env.SSH_HOST,
      port: process.env.SSH_PORT,
      destination: process.env.SSH_PATH,
      progress: false,
      silent: false,
      compress: true,
      update: true,
    })
  );
});
