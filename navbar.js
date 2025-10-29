// Burger toggle für mobile Ansicht
// Einmalig für alle Seiten, wenn Navbar eingebunden ist

document.addEventListener('DOMContentLoaded', function () {
  var burgers = Array.prototype.slice.call(document.querySelectorAll('.navbar-burger'), 0);
  if (burgers.length > 0) {
    burgers.forEach(function (el) {
      el.addEventListener('click', function () {
        var target = el.dataset.target;
        var $target = document.getElementById(target);
        el.classList.toggle('is-active');
        if ($target) $target.classList.toggle('is-active');
      });
    });
  }
});
