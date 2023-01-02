/* The folowing is reused form :-  Convert latitude/longitude <=> OS National Grid Reference points (c) Chris Veness 2002-2010
 *
 * convert numeric grid reference (in metres) to standard-form grid ref
 */
function gridrefNumToLet(e, n, digits) {
  // get the 100km-grid indices
  var e100k = Math.floor(e / 100000);
  var n100k = Math.floor(n / 100000);

  if (e100k < 0 || e100k > 6 || n100k < 0 || n100k > 12) return "";

  // translate those into numeric equivalents of the grid letters
  var l1 = 19 - n100k - ((19 - n100k) % 5) + Math.floor((e100k + 10) / 5);
  var l2 = (((19 - n100k) * 5) % 25) + (e100k % 5);

  // compensate for skipped "I" and build grid letter-pairs
  if (l1 > 7) l1++;
  if (l2 > 7) l2++;
  var letPair = String.fromCharCode(
    l1 + "A".charCodeAt(0),
    l2 + "A".charCodeAt(0)
  );

  // strip 100km-grid indices from easting & northing, and reduce precision
  e = Math.floor((e % 100000) / Math.pow(10, 5 - digits / 2));
  n = Math.floor((n % 100000) / Math.pow(10, 5 - digits / 2));
  // note use of floor, as ref is bottom-left of relevant square!

  var gridRef =
    letPair + " " + padLZ(e, digits / 2) + " " + padLZ(n, digits / 2);
  return gridRef;
}

function padLZ(num, width) {
  num = num.toString();
  var len = num.length;
  for (var i = 0; i < width - len; i++) num = "0" + num;
  return num;
}

export { gridrefNumToLet };
