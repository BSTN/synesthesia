const path = require('path');
const gulp = require("gulp");
const template = require("gulp-template");
const dotenv = require("dotenv").config();

const webpack = require("webpack");
const webpackStream = require("webpack-stream");
const webpackDevServer = require("webpack-dev-server");
const webpackConfig = require("./webpack.config.js");

let production = process.env.NODE_ENV === "production";

// templates

gulp.task("templates", function() {
  let templateVars = {
    SRCV: production ? "js/vendors.js" : "http://localhost:" + process.env.DEV_HOTPORT + "/vendors.js",
    SRC: production ? "js/main.js" : "http://localhost:" + process.env.DEV_HOTPORT + "/main.js",
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
  return gulp
    .src("templates/*.php") // Get source files with gulp.src
    .pipe(template(templateVars)) // Sends it through a gulp plugin
    .pipe(
      gulp.dest(
        production ? process.env.LIVE_BUILD_PATH : process.env.DEV_BUILD_PATH
      )
    ); // Outputs the file in the destination folder
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
    .pipe(gulp.dest(path.join(process.env.LIVE_BUILD_PATH, 'js')));
});

gulp.task("dev", gulp.series(["copyfolder", "templates", watcher]));

gulp.task("build", gulp.series(["copyfolder", "templates", "webpackBuild"]));
