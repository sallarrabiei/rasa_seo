(function(){
  document.addEventListener('DOMContentLoaded', function(){
    var toggles = document.querySelectorAll('[data-bss-toggle]');
    toggles.forEach(function(el){
      el.addEventListener('click', function(){
        var target = document.getElementById(el.getAttribute('data-bss-toggle'));
        if (target) {
          target.hidden = !target.hidden;
        }
      });
    });
  });
})();
