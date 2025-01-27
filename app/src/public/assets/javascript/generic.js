document.addEventListener('DOMContentLoaded', () => {
    const $navbarBurgers = Array.prototype.slice.call(document.querySelectorAll('.navbar-burger'), 0);
  
    $navbarBurgers.forEach( el => {
      el.addEventListener('click', () => {
        const target = el.dataset.target;
        const $target = document.getElementById(target);
  
        el.classList.toggle('is-active');
        $target.classList.toggle('is-active');
  
      });
    });

    document.getElementById('logout-button').addEventListener('click', function(event) {
      event.preventDefault();
      if (confirm("Are you sure you want to log out?")) {
          window.location.href = './logout.php';
      }
  });
});