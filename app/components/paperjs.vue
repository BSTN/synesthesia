<template>
  <div id="paperjs">
    <canvas ref="papercanvas" id="paperjs" resize="true"></canvas>
  </div>
</template>
<script>
import paper from "paper";
// // const paper = "false";
// // Ported from original Metaball script by SATO Hiroyuki
// // http://park12.wakwak.com/~shp/lc/et/en_aics_script.html
// // var maincolor = "#ffffff00";
// var maincolor = "#000000";

// var project = new paper.Project();

// var columns = 8;
// var rows = 5;
// var connectionRadius = 250;
// var randomPosition = 50;
// var dotSize = 5;
// var mouseDotSize = 5;

// var ballPositions = [];

// var points = rows * columns;

// var handle_len_rate = 2.4;
// var circlePaths = [];
// var radius = 50;
// var largeCircle;

// var connections = new paper.Group();

// var mousePosition = new paper.Point(0, 0);

// function generateConnections(paths) {
//   // Remove the last connection paths:
//   connections.children = [];

//   for (var i = 0, l = paths.length; i < l; i++) {
//     for (var j = i - 1; j >= 0; j--) {
//       // only connect to mouse
//       if (
//         (paths[i].position.x === mousePosition.x &&
//           paths[i].position.y === mousePosition.y) ||
//         (paths[j].position.x === mousePosition.x &&
//           paths[j].position.y === mousePosition.y)
//       ) {
//         var path = metaball(
//           paths[i],
//           paths[j],
//           0.5,
//           handle_len_rate,
//           connectionRadius
//         );
//         if (path) {
//           connections.appendTop(path);
//           path.removeOnMove();
//         }
//       }
//     }
//   }
// }

// // ---------------------------------------------
// function metaball(ball1, ball2, v, handle_len_rate, maxDistance) {
//   var center1 = ball1.position;
//   var center2 = ball2.position;
//   var radius1 = ball1.bounds.width / 2;
//   var radius2 = ball2.bounds.width / 2;
//   var pi2 = Math.PI / 2;
//   var d = center1.getDistance(center2);
//   var u1, u2;

//   if (radius1 == 0 || radius2 == 0) return;

//   if (d > maxDistance || d <= Math.abs(radius1 - radius2)) {
//     return;
//   } else if (d < radius1 + radius2) {
//     // case circles are overlapping
//     u1 = Math.acos(
//       (radius1 * radius1 + d * d - radius2 * radius2) / (2 * radius1 * d)
//     );
//     u2 = Math.acos(
//       (radius2 * radius2 + d * d - radius1 * radius1) / (2 * radius2 * d)
//     );
//   } else {
//     u1 = 0;
//     u2 = 0;
//   }

//   var angle1 = new paper.Point(
//     center2.x - center1.x,
//     center2.y - center1.y
//   ).getAngleInRadians();
//   var angle2 = Math.acos((radius1 - radius2) / d);
//   var angle1a = angle1 + u1 + (angle2 - u1) * v;
//   var angle1b = angle1 - u1 - (angle2 - u1) * v;
//   var angle2a = angle1 + Math.PI - u2 - (Math.PI - u2 - angle2) * v;
//   var angle2b = angle1 - Math.PI + u2 + (Math.PI - u2 - angle2) * v;

//   var p1a = new paper.Point(
//     center1.x + getVector(angle1a, radius1).x,
//     center1.y + getVector(angle1a, radius1).y
//   );
//   var p1b = new paper.Point(
//     center1.x + getVector(angle1b, radius1).x,
//     center1.y + getVector(angle1b, radius1).y
//   );
//   var p2a = new paper.Point(
//     center1.x + getVector(angle2a, radius2).x,
//     center1.y + getVector(angle2a, radius2).y
//   );
//   var p2b = new paper.Point(
//     center1.x + getVector(angle2b, radius2).x,
//     center1.y + getVector(angle2b, radius2).y
//   );

//   // define handle length by the distance between
//   // both ends of the curve to draw
//   var totalRadius = radius1 + radius2;
//   var d2 = Math.min(
//     v * handle_len_rate,
//     new paper.Point(p1a.x - p2a.x, p1a.y - p2a.y).length / totalRadius
//   );

//   // case circles are overlapping:
//   d2 *= Math.min(1, (d * 2) / (radius1 + radius2));

//   radius1 *= d2;
//   radius2 *= d2;

//   var path = new paper.Path({
//     segments: [p1a, p2a, p2b, p1b],
//     style: ball1.style,
//     closed: true,
//     fillColor: maincolor,
//   });
//   var segments = path.segments;
//   segments[0].handleOut = getVector(angle1a - pi2, radius1);
//   segments[1].handleIn = getVector(angle2a + pi2, radius2);
//   segments[2].handleOut = getVector(angle2b - pi2, radius2);
//   segments[3].handleIn = getVector(angle1b + pi2, radius1);
//   return path;
// }

// // ------------------------------------------------
// function getVector(radians, length) {
//   return new paper.Point({
//     // Convert radians to degrees:
//     angle: (radians * 180) / Math.PI,
//     length: length,
//   });
// }

export default {
  data() {
    return {
      scope: false,
    };
  },
  methods: {
    init() {
      // var color = window
      //   .getComputedStyle(document.getElementById("connected"))
      //   .getPropertyValue("background-color");
      const canvas = this.$refs.papercanvas;
      if (!this.scope) {
        this.scope = new paper.PaperScope();
        this.scope.setup(canvas);
        this.$axios.get("assets/connected.js").then((x) => {
          this.scope.execute(x.data);
        });
      }
    },
  },
  mounted() {
    this.init();
  },
};
</script>
<style lang="less" scoped>
#paperjs {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  box-sizing: border-box;
  canvas {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    box-sizing: border-box;
  }
}
</style>
