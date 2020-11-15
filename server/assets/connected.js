// modified from paperjs examples: http://paperjs.org/examples/meta-balls/
// Ported from original Metaball script by SATO Hiroyuki
// http://park12.wakwak.com/~shp/lc/et/en_aics_script.html
var color = window
  .getComputedStyle(document.getElementById("connected"))
  .getPropertyValue("background-color");

var canvas = document.getElementById("paperjs");

project.currentStyle = {
  fillColor: color,
};

var columns = 8;
var rows = 5;
var connectionRadius = 250;
var randomPosition = 150;
var dotSize = 5;
var mouseDotSize = 5;
var largeCircle;
var circlePath;

var handle_len_rate = 2.4;
var circlePaths = [];
var radius = 50;
var ballPositions = [];

var connections = new Group();

function generateConnections(paths) {
  // Remove the last connection paths:
  connections.children = [];

  for (var i = 0, l = paths.length; i < l; i++) {
    for (var j = i - 1; j >= 0; j--) {
      // only connect to mouse
      if (
        (paths[i].position.x === largeCircle.position.x &&
          paths[i].position.y === largeCircle.position.y) ||
        (paths[j].position.x === largeCircle.position.x &&
          paths[j].position.y === largeCircle.position.y)
      ) {
        var path = metaball(
          paths[i],
          paths[j],
          0.5,
          handle_len_rate,
          connectionRadius
        );
        if (path) {
          connections.appendTop(path);
          path.removeOnMove();
        }
      }
    }
  }
}

// ---------------------------------------------
function metaball(ball1, ball2, v, handle_len_rate, maxDistance) {
  var center1 = ball1.position;
  var center2 = ball2.position;
  var radius1 = ball1.bounds.width / 2;
  var radius2 = ball2.bounds.width / 2;
  var pi2 = Math.PI / 2;
  var d = center1.getDistance(center2);
  var u1, u2;

  if (radius1 == 0 || radius2 == 0) return;

  if (d > maxDistance || d <= Math.abs(radius1 - radius2)) {
    return;
  } else if (d < radius1 + radius2) {
    // case circles are overlapping
    u1 = Math.acos(
      (radius1 * radius1 + d * d - radius2 * radius2) / (2 * radius1 * d)
    );
    u2 = Math.acos(
      (radius2 * radius2 + d * d - radius1 * radius1) / (2 * radius2 * d)
    );
  } else {
    u1 = 0;
    u2 = 0;
  }

  var angle1 = (center2 - center1).getAngleInRadians();
  var angle2 = Math.acos((radius1 - radius2) / d);
  var angle1a = angle1 + u1 + (angle2 - u1) * v;
  var angle1b = angle1 - u1 - (angle2 - u1) * v;
  var angle2a = angle1 + Math.PI - u2 - (Math.PI - u2 - angle2) * v;
  var angle2b = angle1 - Math.PI + u2 + (Math.PI - u2 - angle2) * v;
  var p1a = center1 + getVector(angle1a, radius1);
  var p1b = center1 + getVector(angle1b, radius1);
  var p2a = center2 + getVector(angle2a, radius2);
  var p2b = center2 + getVector(angle2b, radius2);

  // define handle length by the distance between
  // both ends of the curve to draw
  var totalRadius = radius1 + radius2;
  var d2 = Math.min(v * handle_len_rate, (p1a - p2a).length / totalRadius);

  // case circles are overlapping:
  d2 *= Math.min(1, (d * 2) / (radius1 + radius2));

  radius1 *= d2;
  radius2 *= d2;

  var path = new Path({
    segments: [p1a, p2a, p2b, p1b],
    style: ball1.style,
    closed: true,
    fillColor: color,
  });
  var segments = path.segments;
  segments[0].handleOut = getVector(angle1a - pi2, radius1);
  segments[1].handleIn = getVector(angle2a + pi2, radius2);
  segments[2].handleOut = getVector(angle2b - pi2, radius2);
  segments[3].handleIn = getVector(angle1b + pi2, radius1);
  return path;
}

function getVector(radians, length) {
  return new Point({
    // Convert radians to degrees:
    angle: (radians * 180) / Math.PI,
    length: length,
  });
}

function setup() {
  // color
  color = window
    .getComputedStyle(document.getElementById("connected"))
    .getPropertyValue("background-color");

  // paths
  if (circlePaths.length > 0) {
    for (var i in circlePaths) {
      circlePaths[i].remove();
    }
  }
  circlePaths = [];
  ballPositions = [];

  var cs = canvas.offsetWidth < 800 ? 100 : 150;
  var rs = canvas.offsetWidth < 800 ? 100 : 150;
  connectionRadius = canvas.offsetWidth < 800 ? 150 : 250;

  columns = parseInt(canvas.offsetWidth / cs);
  rows = parseInt(canvas.offsetHeight / rs);

  var points = rows * columns;
  var iy = -1;
  for (var i = 0; i < points; i++) {
    var ix = i % columns;
    if (ix === 0) iy++;
    var x =
      ix * (canvas.offsetWidth / (columns - 1)) +
      (Math.random() - 0.5) * randomPosition;
    var y =
      iy * (canvas.offsetHeight / (rows / 2)) +
      (Math.random() - 0.5) * randomPosition;
    ballPositions.push([x, y]);
  }

  for (var i = 0, l = ballPositions.length; i < l; i++) {
    var circlePath = new Path.Circle({
      center: ballPositions[i],
      radius: dotSize,
      fillColor: color,
    });
    circlePaths.push(circlePath);
  }

  largeCircle = new Path.Circle({
    center: [676, 433],
    radius: mouseDotSize,
    fillColor: color,
  });
  circlePaths.push(largeCircle);
}

// ------------------------------------------------

setup();

generateConnections(circlePaths);

var mouse = false;
var touchy = false;

window.addEventListener("touchstart", function() {
  touchy = true;
});

function onMouseMove(event) {
  mouse = true;
  if (!touchy) {
    largeCircle.position = event.point;
    generateConnections(circlePaths);
  }
}

var cx = 0;
var cy = 0;
var prevx = 0.5 * canvas.offsetWidth;
var prevy = 0.5 * canvas.offsetHeight;
var nextx = Math.random() * canvas.offsetWidth;
var nexty = Math.random() * canvas.offsetHeight;

function onFrame(event) {
  if (touchy || !mouse) {
    cx++;
    cy++;
    var newx = ((nextx - prevx) / (Math.abs(nextx - prevx) * 0.3)) * cx + prevx;
    var newy = ((nexty - prevy) / (Math.abs(nexty - prevy) * 0.3)) * cy + prevy;
    if (newx > canvas.offsetWidth || newx < 0) {
      prevx = newx;
      cx = 0;
    }
    if (newy > canvas.offsetHeight || newy < 0) {
      prevy = newy;
      cy = 0;
    }
    // console.log(event.count);
    // var x = largeCircle.position.x;
    // var y = largeCircle.position.y;
    // var speed = 3;
    var pos = new Point(newx, newy);
    largeCircle.position = pos;
    generateConnections(circlePaths);
  }
}

window.addEventListener("resize", function() {
  setup();
});

document.body.addEventListener("changetheme", function() {
  setup();
});

canvas.addEventListener("resize", function() {
  setup();
});
