	var link	= document.querySelector(".message_btn");
	var popup	= document.querySelector(".modal-content");
	var close	= document.querySelector(".modal-content-close");
	var login	= popup.querySelector("[name=username]");

	link.addEventListener("click", function(event) {
		event.preventDefault();
		popup.classList.add("modal-content-show");
		login.focus();
	});

	close.addEventListener("click", function(event) {
		event.preventDefault();
		popup.classList.remove("modal-content-show");
	});

	window.addEventListener("keydown", function(event) {
		if (event.keyCode == 27) {
			if (popup.classList.contains("modal-content-show")) {
				popup.classList.remove("modal-content-show");
			}
		}
	});