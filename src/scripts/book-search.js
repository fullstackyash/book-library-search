document.addEventListener('DOMContentLoaded', function () {
    
    // Button click event binded on Search button.
    document.querySelector('button.search_book').addEventListener('click', function(e) {    
      e.preventDefault();   
      var request = new XMLHttpRequest();
      var book_name = document.getElementById('book_name').value;
      var author_name = document.getElementById('author_name').value;
      var publisher_name = document.getElementById('publisher_name').value;
      var book_rating = document.getElementById('book_rating').value;
      var book_price = document.getElementById('book_price').value;
      
      var params = `action=bls_filter_books&_ajaxnonce=${ajaxload_params.nonce}&book_name=${book_name}&author_name=${author_name}&publisher_name=${publisher_name}&book_rating=${book_rating}&book_price=${book_price}`;
      request.open('POST', ajaxload_params.ajax_url, true); 
      request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
      request.onload = function ajaxLoad() {
        if (request.status >= 200 && request.status < 400) {
          var serverResponse = JSON.parse(request.responseText);
          var Obj = document.querySelector('.book_results_wrapper');
          Obj.innerHTML = serverResponse.data; // replace element with contents of serverResponse
  
         /*  if (document.querySelectorAll('.search-results tbody tr').length > 10) {
            document.querySelector('button#load-more').style.display = 'block';
          } else {
            document.querySelector('button#load-more').style.display = 'none';
          } */
        }
      };
  
      request.send(params); 
    });
  
    /* // Button click event binded on Load More button.
    document.querySelector('button#load-more').addEventListener('click', function(e) {
      const _ = jQuery;
      e.preventDefault();
      _('tr.hide').addClass('show');
      _('tr.hide').removeClass('hide');
      this.style.display = 'none';
    }); */

    var slider = document.getElementById('book_price');
    var output = document.getElementById('price_value');
    output.innerHTML = slider.value;

    slider.oninput = function () {
        output.innerHTML = this.value;
    };

  });
  