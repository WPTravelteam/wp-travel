// custom.js
function myCustomFunction() {
  // Your custom code here
  console.log('Custom function executed!');
  document.getElementById("booknow-btn-booking").addEventListener("click", function() {
    var modal = document.getElementById("booknow-btn-modal");
    modal.classList.add("show");
  });
}

export default myCustomFunction;
