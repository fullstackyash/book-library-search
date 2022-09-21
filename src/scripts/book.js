var slider = document.getElementById('book_price');
var output = document.getElementById('price_value');
output.innerHTML = slider.value;

slider.oninput = function () {
	output.innerHTML = this.value;
};
