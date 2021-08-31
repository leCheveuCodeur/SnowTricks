function create(htmlStr) {
	var frag = document.createDocumentFragment(),
		temp = document.createElement("div");

	temp.innerHTML = htmlStr;
	while (temp.firstChild) {
		frag.appendChild(temp.firstChild);
	}
	return frag;
}

window.onload = () => {
	var collectionHolder = document.getElementById("images");
	if (collectionHolder.dataset.imagesContribution !== "null") {
		var existingImages = JSON.parse(
			collectionHolder.dataset.imagesContribution
		);
		var imagesTrick = JSON.parse(collectionHolder.dataset.imagesTrick);
		//----------------------------------------------------------------------------------
		console.log("existants", existingImages);
		console.log("imageTrick", imagesTrick);
		//----------------------------------------------------------------------------------
	}
	var existingCards = [];

	if (collectionHolder.dataset.submitted !== "null") {
		var imagesSubmitted = JSON.parse(collectionHolder.dataset.submitted);
		console.log("soumis", imagesSubmitted);
	}
	var index = parseInt(collectionHolder.dataset.index);
	var removedButtons = [];

	// ---------------------------------------------------------
	// ---------------------------------------------------------
	// ---------------------------------------------------------
	function removeElements() {
		removedButtons.forEach((removedButton) => {
			removedButton.addEventListener("click", function (e) {
				var id = this.getAttribute("data-remove");

				var card = document.querySelector("[data-id='" + id + "']");
				card ? card.remove() : "";

				var modal = document.getElementById("img_" + id + "_Modal");
				modal ? modal.remove() : "";

				removedButtons = removedButtons.filter((item) => item !== this);

				if (existingCards.indexOf(id) !== -1) {
					existingCards = existingCards.filter((item) => item !== id);
					console.log(id, existingCards);
				}
			});
		});
	}

	function addImage(imageObject = null) {
		index++;

		var prototype = collectionHolder.dataset.prototype;

		// rewriting the prototype
		prototype = prototype.replace(/__name__/g, index);
		prototype = prototype.replace(
			/valueTitle/g,
			imageObject ? imageObject.title : ""
		);
		let content = document.createElement("html");
		content.innerHTML = prototype;
		let newForm = content.querySelector("div");

		let src = imageObject
			? "/img_uploads/" + imageObject.path
			: "/src/img/no_image.png";
		// let alt = imageObject ? imageObject.title : "no_image";

		var fragmentCard = create(
			"<div class='card d-inline-block m-1' data-id='" +
				index +
				"'><div class='card-img-top' style='background: url(" +
				src +
				") no-repeat center 40%; background-size: cover; height: 30vh; border-radius: 10px 10px 0 0'></div><div class='card-body text-end'><button type='button' class='btn btn-primary bi bi-pencil-square' data-bs-toggle='modal' data-bs-target='#img_" +
				index +
				"_Modal'></button><button type='button' class='btn btn-danger bi bi-trash-fill ms-4' data-remove='" +
				index +
				"' ></button></div>"
		);

		collectionHolder.append(fragmentCard);
		collectionHolder.append(newForm);

		// updating the counter of images cards
		collectionHolder.dataset.index =
			document.querySelectorAll("[data-id]").length;

		var removedButton = document.querySelector(
			"[data-remove='" + index + "']"
		);
		removedButtons.push(removedButton);

		// delete management
		removeElements();
	}

	// -------------------------------------------------------------
	// -------------------------------------------------------------
	// -------------------------------------------------------------

	if (existingImages) {
		// removal of the unnecessary path field on existing images
		let formPath = document.getElementsByClassName("form_path");
		console.log(formPath);
		while (formPath[0]) {
				formPath[0].parentNode.removeChild(formPath[0]);
		}
		// console.log(formPath);
		// formPath.forEach((item) => {
		// 	console.log("test");
		// });

		let removedButtonsAvailables =
			document.querySelectorAll("[data-remove]");
		removedButtonsAvailables.forEach((item) => {
			removedButtons.push(item);
		});

		let existingCardsAvailables = document.querySelectorAll("[data-id]");
		existingCardsAvailables.forEach((item) => {
			existingCards.push(item.dataset.id);
		});

		console.log(existingCards);
	}

	// -------------------------------------------------------------
	// management of card adding
	var addCardLink = document.querySelector("[data-add-card]");
	addCardLink.addEventListener("click", function (e) {
		addImage();
	});

	// delete management
	removeElements();
};
