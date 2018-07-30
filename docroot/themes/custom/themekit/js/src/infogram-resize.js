/**
 * script provided by infogram to reszie iFrames
 * https://developers.infogr.am/oembed/
 */

window.addEventListener('message', function(e) {
  var data;
  try {
    data = JSON.parse(e.data);
  } catch (e) {
    return false;
  }

  if (data.context !== 'iframe.resize') {
    return false;
  }

  var iframe = document.querySelector('iframe[src="' + data.src + '"]');

  if (!iframe) {
    return false;
  }

  iframe.height = data.height;

  return;
});
