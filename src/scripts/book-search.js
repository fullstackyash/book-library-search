document.addEventListener('DOMContentLoaded', function () {
  
    function ajaxLoadBooks(page) {
      var request = new XMLHttpRequest();
      var book_name = document.getElementById('book_name')?.value;
      var author_name = document.getElementById('author_name')?.value;
      var publisher_name = document.getElementById('publisher_name')?.value;
      var book_rating = document.getElementById('book_rating')?.value;
      var book_price_min = document.getElementById('book_price_min')?.value;
      var book_price_max = document.getElementById('book_price_max')?.value;
      
      var params = `action=bls_filter_books&_ajaxnonce=${ajaxload_params.nonce}&book_name=${book_name}&author_name=${author_name}&publisher_name=${publisher_name}&book_rating=${book_rating}&book_price_min=${book_price_min}&book_price_max=${book_price_max}&page=${page}`;
      request.open('POST', ajaxload_params.ajax_url, true); 
      request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
      request.onload = function ajaxLoad() {
        if (request.status >= 200 && request.status < 400) {
        var serverResponse = JSON.parse(request.responseText);
        var Obj = document.querySelector('.book_results_wrapper');
        Obj.innerHTML = serverResponse.data; // replace element with contents of serverResponse
        }
      };
  
      request.send(params); 
    }

    document.querySelector('button.search_book').addEventListener('click', function(e) {
        e.preventDefault();
        var page = this?.getAttribute('p');
        ajaxLoadBooks(page);
    });
    
    // selecting by querySelector
    liveQuery('.bls-universal-pagination li.active', 'click', function(e) {     
        
        e.preventDefault();   
        if(e.target.classList.contains('search_book')){
            return false;
        } 
       
        if(e.target.hasAttribute('p') ){
            var page = e.target.getAttribute('p');
            ajaxLoadBooks(page);
            return false;
        }       
    });

    // selecting by querySelector
    function liveQuery(selector, eventType, callback, context) {
        (context || document).addEventListener(eventType, function(event) {
        var nodeList = document.querySelectorAll(selector);
        // convert nodeList into matches array
        var matches = [];
        for (var i = 0; i < nodeList.length; ++i) {
            matches.push(nodeList[i]);
        }
        // if there are matches
        if (matches) {
            var element = event.target;
            var index   = -1;
            // traverse up the DOM tree until element can't be found in matches array
            while (element && (index = matches.indexOf(element) === -1)) {
            element = element.parentElement;
            }
            // when element matches the selector, apply the callback
            if (index > -1) {
            callback.call(element, event);
            }
        }
        }, false);
    } 

    var book_price_min_slider = document.getElementById("book_price_min");   
   
  
    // Update the current slider value (each time you drag the slider handle)
    book_price_min_slider.oninput = function() {
        var book_price_min_slider_val = this.value;        
        var book_price_max_slider_val = document.getElementById("book_price_max").value;
        this.parentNode.style.setProperty('--value-a',book_price_min_slider_val);
        this.parentNode.style.setProperty('--text-value-a', JSON.stringify(book_price_min_slider_val));       
        if(parseInt(book_price_min_slider_val) >= parseInt(book_price_max_slider_val)){            
            book_price_min_slider.value = book_price_max_slider_val;
            this.parentNode.style.setProperty('--value-a', book_price_min_slider.value);
            this.parentNode.style.setProperty('--text-value-a', JSON.stringify(book_price_min_slider.value));
        }

       
    }
  
    var book_price_max_slider = document.getElementById("book_price_max"); 

    book_price_max_slider.oninput = function() {
        var book_price_max_slider_val = this.value;        
        var book_price_min_slider_val = document.getElementById("book_price_min").value;
        this.parentNode.style.setProperty('--value-b',book_price_max_slider_val);
        this.parentNode.style.setProperty('--text-value-b', JSON.stringify(book_price_max_slider_val));        
        if(parseInt(book_price_max_slider_val) <= parseInt(book_price_min_slider_val)){                     
            book_price_max_slider.value = book_price_min_slider.value;
            this.parentNode.style.setProperty('--value-b', book_price_max_slider.value);
            this.parentNode.style.setProperty('--text-value-b', JSON.stringify(book_price_max_slider.value));
        }
    }     

  });
  
  