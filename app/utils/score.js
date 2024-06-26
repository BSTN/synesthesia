import color from "color";
import { each, isArray, uniq } from 'lodash'

function distance (type, values) {
  // to be sure, filter imagesound (tested before)
  if (type === "imagesound") return false;
  if (!values) return false;
  if (!isArray(values)) return false
  // return false if 1 value is undefined/nocolor/null
  if (values.indexOf(undefined) >= 0) return false;
  if (values.indexOf("nocolor") >= 0) return false;
  if (values.indexOf(null) >= 0) return false;
  if (type === 'grapheme' && values.length === 2) {
    return values[0] === values[1] ? 1 : 0
  }
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
    // get absolute distance per color (r,g,b)
    let r = Math.abs(v[0] - all[(parseInt(k) + 1) % all.length][0]);
    let g = Math.abs(v[1] - all[(parseInt(k) + 1) % all.length][1]);
    let b = Math.abs(v[2] - all[(parseInt(k) + 1) % all.length][2]);
    distance = distance + r + g + b;
  });
  distance = distance > 0 ? Math.round(distance * 100) / 100 : 0;
  return distance;
}

function likert(testid, data) {
  if (!data) return false
  const check = [testid + '1', testid + '2', testid + '3', testid + '4', testid + '5', testid + '6'].filter(x => (x in Object.keys(data)))
  if (check.length > 0) return JSON.stringify(data)
  let res = data[testid + '1'] +
        data[testid + '2'] +
        data[testid + '3'] +
        data[testid + '4'] +
        data[testid + '5'] +
        data[testid + '6']
  return res
}

function all(testconfig, questions) {
  let bundled = {}
  
  // bundle answers per symbol
  each(questions, (v) => {
    // define name
    let name = typeof v.symbol === 'string' ? v.symbol : JSON.stringify(v.symbol)
    // compress name for imagesound
    if (testconfig.type === 'imagesound') {
      name = [v.symbol.im1, v.symbol.im2, v.symbol.sound].sort().join('-');
    }
    // create score object if not exists
    if (!bundled[name]) bundled[name] = { score: null, data: [], symbol: v.symbol }
    
    // push it
    bundled[name].data.push(v)
  })
  
  // calculate score per symbol
  each(bundled, (v) => {
    // get all values
    let values = v.data.map(x => x.value)
    // different for imagesound
    if (testconfig.type === 'imagesound') {
      if (values.filter(x => x !== null).length > 0) {
        if (values.length === 1) v.score = false
        if (values.length === 2) v.score = values[0] === values[1] ? 1 : 0
        if (values.length === 3) {
          v.score = uniq(values).length > 1 ? 0 : 1
        }
      }
    } else {
      console.log("values", values)
      // get score by distance
      v.score = distance(testconfig.type, values);
    }
  })

  console.log("bundled", bundled)
  // calculate total
  let total = undefined
  let count = 0
  let hasDoubles = false
  if (testconfig.type === 'grapheme' || testconfig.type === 'audio') {
    each(bundled, x => {
      if (x.data.length === 2) { hasDoubles = true }
    })
    each(bundled, x => {
      if (!isNaN(x.score) && x.score !== false) {
        if (total === undefined) total = 0
        total = total + x.score
        count = count + 1
      }
    })
    if (total !== undefined && count > 1 && !hasDoubles) total = total === 0 ? 0 : total / count
  } else if (testconfig.type === "imagesound") {
    each(bundled, x => {
      if (!isNaN(x.score) && x.score !== false && x.score !== null) {
        if (total === undefined) total = 0
        total = total + x.score
        count = count + 1
      }
    })
    if (total !== undefined && count > 1) {
      if (!hasDoubles) total = total === 0 ? 0 : total / count
    }
  }

  // sort
  bundled = Object.keys(bundled).sort((a,b) => {
    a = a.toLowerCase().trim()
    b = b.toLowerCase().trim()
    const days = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
    const months = ['january','february','march','april','may','june','july','august','september','october','november','december'];
    const dagen = ['maandag','dinsdag','woensdag','donderdag','vrijdag','zaterdag','zondag']
    const maanden = ['januari','februari','maart','april','mei','juni','juli','augustus','september','oktober','november','december']
    if (a.startsWith('t:') && !b.startsWith('t:')) {
      return -1
    }
    if (!a.startsWith('t:') && b.startsWith('t:')) {
      return 1
    }
    if (a.startsWith('t:') && b.startsWith('t:')) {
      const aa = a.replace(/^t:/,'')
      const bb = b.replace(/^t:/,'')
      if (days.indexOf(aa) > -1 && !(days.indexOf(bb) > -1)) { return -1 }
      if (!(days.indexOf(aa) > -1) && days.indexOf(bb) > -1) { return 1 }
      if (days.indexOf(aa) > -1 && days.indexOf(bb) > -1) { 
        return days.indexOf(aa) < days.indexOf(bb);
      }
      if (months.indexOf(aa) > -1 && !(months.indexOf(bb) > -1)) { return -1 }
      if (!(months.indexOf(aa) > -1) && months.indexOf(bb) > -1) { return 1 }
      if (months.indexOf(aa) > -1 && months.indexOf(bb) > -1) { 
        return months.indexOf(aa) < months.indexOf(bb);
      }
    }
    if (dagen.indexOf(a) > -1 && dagen.indexOf(b) > -1) { 
      return dagen.indexOf(a) > dagen.indexOf(b);
    }
    if (maanden.indexOf(a) > -1 && maanden.indexOf(b) > -1) { 
      return maanden.indexOf(a) > maanden.indexOf(b);
    }
    return a.localeCompare(b)
  }).reduce((obj, key) => { 
      obj[key] = bundled[key]; 
      return obj;
  }, {});
  // return results
  console.log("RESS", total, bundled, hasDoubles)
  return { total , symbols: bundled, hasDoubles }
}


export default { all, likert }