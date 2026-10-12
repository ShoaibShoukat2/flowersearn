// Yay for SO - http://stackoverflow.com/questions/23847310/css-and-jquery-analog-clock-smooth-animation
// use requestAnimationFrame for smoothness (shimmed with setTimeout fallback)
window.requestAnimFrame = (function() {
  return window.requestAnimationFrame ||
    window.webkitRequestAnimationFrame ||
    window.mozRequestAnimationFrame ||
    function(callback) {
      window.setTimeout(callback, 1000 / 60);
    };
})();

// initialize the clock in a self-invoking function
(function clock() {
  var hour = document.getElementById("hour"),
    min = document.getElementById("min"),
    sec = document.getElementById("sec");
  // set up a loop
  (function loop() {
    requestAnimFrame(loop);
    draw();
  })();
  // position the hands
  function draw() {
    var now = new Date(), // now
      then = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 0, 0, 0), // midnight
      diffInMil = (now.getTime() - then.getTime()), // difference in milliseconds
      h = (diffInMil / (1000 * 60 * 60)), // hours
      m = (h * 60), // minutes
      s = (m * 60); // seconds
    // rotate the hands accordingly
    sec.style.webkitTransform = "rotate(" + (s * 6) + "deg)";
    hour.style.webkitTransform = "rotate(" + (h * 30 + (h / 2)) + "deg)";
    min.style.webkitTransform = "rotate(" + (m * 6) + "deg)";

    // Calculate remaining time until the deadline
    var deadline = then.getTime() + (5 * 60 * 60 * 1000); // Set the deadline to 5 hours from midnight
    var remainingTime = deadline - now.getTime();
    if (remainingTime > 0) {
      var remainingHours = Math.floor(remainingTime / (1000 * 60 * 60));
      var remainingMinutes = Math.floor((remainingTime % (1000 * 60 * 60)) / (1000 * 60));
      var remainingSeconds = Math.floor((remainingTime % (1000 * 60)) / 1000);
      
      // Update the HTML elements with the remaining time
      document.getElementById("remaining-hours").textContent = remainingHours.toString().padStart(2, '0');
      document.getElementById("remaining-minutes").textContent = remainingMinutes.toString().padStart(2, '0');
      document.getElementById("remaining-seconds").textContent = remainingSeconds.toString().padStart(2, '0');
    }
  }
})();
