jQuery.noConflict();
  (function( $ ) {

      $(function() { 
          console.log('plugin scripts call');
          $('#product_table').DataTable({
               responsive: true
          });
    });
  });