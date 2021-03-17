import color from "color";
import { each, isArray, uniq } from 'lodash'

function distance (type, values) {
  if (type === "imagesound") return false;
  if (!values) return false;
  if (!isArray(values)) return false
  if (values.indexOf(undefined) >= 0) return false;
  if (values.indexOf("nocolor") >= 0) return false;
  if (values.indexOf(null) >= 0) return false;
  // calculate
  let all = values.map(x =>
    color("#" + x)
      .rgb()
      .array()
      .map(xx => {
        return parseInt(xx) / 255;
      })
  );
  let distance = 0;
  each(all, (v, k) => {
    let r = Math.abs(v[0] - all[(parseInt(k) + 1) % all.length][0]);
    let g = Math.abs(v[1] - all[(parseInt(k) + 1) % all.length][1]);
    let b = Math.abs(v[2] - all[(parseInt(k) + 1) % all.length][2]);
    distance = distance + r + g + b;
  });
  distance = Math.round(distance * 100) / 100;
  return distance;
}

// function totalScore (type, questions) {
//   if (!questions || typeof questions !== 'object') return false
//   let total = 0
//   let count = 0
//   each(questions, (v) => {
//     let distanceValue = distance(type, v)
//     if (distanceValue) {
//       total = total + distanceValue
//       count = count + 1
//     }
//   })
//   return total + ' total score'
// } 

// function score (type, values) {
//   return distance(type, values)
// }

function likert(data) {
  if (!data) return false
  const check = ['pq1', 'pq2', 'pq3', 'pq4', 'pq5', 'pq6'].filter(x => (x in Object.keys(data)))
  if (check.length > 0) return JSON.stringify(data)
  let res = data.pq1 +
        data.pq2 +
        data.pq3 +
        data.pq4 +
        data.pq5 +
        data.pq6
  return (res / 30) * 100
}

function all(testconfig, questions) {
  let bundled = {}
  // bundle answers per symbol
  each(questions, (v) => {
    let name = typeof v.symbol === 'string' ? v.symbol : JSON.stringify(v.symbol)
    if (testconfig.type === 'imagesound') {
      name = [v.symbol.im1, v.symbol.im2, v.symbol.sound].sort().join('-');
    }
    if (!bundled[name]) bundled[name] = { score: null, data: [], symbol: v.symbol }
    bundled[name].data.push(v)
  })
  // calculate score per symbol
  each(bundled, (v) => {
    let values = v.data.map(x => x.value)
    if (testconfig.type === 'imagesound') {
      if (values.length === 1) v.score = false
      if (values.length === 2) v.score = values[0] === values[1] ? 1 : 0
      if (values.length === 3) {
        v.score = uniq(values).length > 1 ? 0.5 : 1
      }
    } else {
      v.score = distance(testconfig.type, values);
    }
  })
  // calculate total
  let total = undefined
  let count = 0
  if (testconfig.type === 'grapheme' || testconfig.type === 'audio') {
    each(bundled, x => {
      if (total === undefined) total = 0
      if (!isNaN(x.score) && x.score !== false) {
        total = total + x.score
        count = count + 1
      }
    })
    if (total !== undefined) total = total / count
  }
  return { total , symbols: bundled }
}


export default { all, likert }