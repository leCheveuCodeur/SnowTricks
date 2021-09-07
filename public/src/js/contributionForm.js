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
	}
	var index = parseInt(collectionHolder.dataset.index);
	var removedButtons = [];
	var requiredsFields = [];
	var invalidsFields = new Set();
	// use of a Set object to prevent duplication when there are two inputs for the same card
	var invalidsImagesCards = new Set();
	var errorDisplay = false;

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

				requiredsFields = requiredsFields.filter(
					(item) => item.dataset.id !== id
				);

				invalidsFields.forEach((item) => {
					if (item.dataset.id === id) {
						invalidsFields.delete(item);
					}
				});

				invalidsImagesCards.forEach((item) => {
					if (item.dataset.id === id) {
						invalidsImagesCards.delete(item);
					}
				});
			});
		});
	}

	// ---------------------------------------------------------

	// manages the display of the remaining character count
	function displayingRemainingsCharacters() {
		requiredsFields.forEach((requiredField) => {
			if (requiredField.hasAttribute("maxlength")) {
				requiredField.addEventListener("input", (e) => {
					if (
						requiredField.parentElement.lastElementChild.classList.contains(
							"maxlength"
						)
					) {
						requiredField.parentElement.lastChild.remove();
					}

					if (
						requiredField.textLength ==
						requiredField.getAttribute("maxlength")
					) {
						let maxlengthMessage = create(
							"<div class='maxlength badge bg-danger'>" +
								requiredField.textLength +
								"/" +
								requiredField.getAttribute("maxlength") +
								"</div>"
						);
						requiredField.parentElement.append(maxlengthMessage);
					} else if (
						requiredField.textLength >=
						requiredField.getAttribute("maxlength") - 20
					) {
						let maxlengthMessage = create(
							"<div class='maxlength badge bg-success'>" +
								requiredField.textLength +
								"/" +
								requiredField.getAttribute("maxlength") +
								"</div>"
						);
						requiredField.parentElement.append(maxlengthMessage);
					}
				});
			}
		});
	}

	// ---------------------------------------------------------

	function addImage() {
		index++;

		var prototype = collectionHolder.dataset.prototype;

		// rewriting the prototype
		prototype = prototype.replace(/__name__/g, index);
		prototype = prototype.replace(/valueTitle/g, "");
		let content = document.createElement("html");
		content.innerHTML = prototype;
		let newForm = content.querySelector("div");

		let src = "/src/img/no_image.png";

		var fragmentCard = create(
			"<div class='card d-inline-block m-1' data-id='" +
				index +
				"'><div class='card-img-top' style='background: url(" +
				src +
				") no-repeat center 40%/cover; height: 30vh; border-radius: 10px 10px 0 0'></div><div class='card-body text-end'><button type='button' class='btn btn-primary bi bi-pencil-square' data-bs-toggle='modal' data-bs-target='#img_" +
				index +
				"_Modal'></button><button type='button' class='btn btn-danger bi bi-trash-fill ms-4' data-remove='" +
				index +
				"' ></button></div>"
		);

		collectionHolder.append(fragmentCard);
		collectionHolder.append(newForm);

		// updating the counter of images
		collectionHolder.dataset.index =
			document.querySelectorAll("[data-id]").length;

		var removedButton = document.querySelector(
			"[data-remove='" + index + "']"
		);
		removedButtons.push(removedButton);

		let new_inputs = [
			...document.querySelectorAll(
				"[id*='images_" + index + "_'][required='required']"
			),
		];
		new_inputs.forEach((input) => {
			requiredsFields.push(input);
		});

		// delete management
		removeElements();

		// manages the display of the remaining character count
		displayingRemainingsCharacters();
	}

	// ---------------------------------------------------------

	function fieldsColoration() {
		requiredsFields = [...document.querySelectorAll("[required]")];

		// detect all invalidsImagesCards with errors
		requiredsFields.forEach((field) => {
			if (field.value === "") {
				invalidsFields.add(field);
				if (field.dataset.field === "image") {
					invalidsImagesCards.add(
						document.querySelector(
							"[data-id='" + field.dataset.id + "']"
						)
					);
				}
			}
		});

		console.log(
			!document.getElementById("error_message"),
			[...invalidsFields].length > 0,
			errorDisplay
		);
		// generate a error message
		if (
			errorDisplay === false &&
			[...invalidsFields].length > 0 &&
			!document.getElementById("error_message")
		) {
			let errorMessage = create(
				"<div class='alert alert-danger d-flex align-items-center'><i class='bi bi-exclamation-triangle-fill'></i><div>&nbsp;<strong>Mince alors !</strong> <span class='alert-link'>Changez quelques trucs</span> et essayez de soumettre à nouveau.</div></div>"
			);
			document.getElementById("form_error").append(errorMessage);
			errorDisplay = true;
		}

		// modification of the styles to indicate the errors zones
		invalidsFields.forEach((invalid_input) => {
			invalid_input.classList.add("is-invalid");
		});

		invalidsImagesCards.forEach((card) => {
			card.classList.add("border", "border-1", "border-danger");
			if (existingImages && existingImages[card.dataset.id]) {
				let copyStyleCard =
					card.firstElementChild.getAttribute("style");
				copyStyleCard = copyStyleCard.replace(
					"background: ",
					"background:url(\"data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23da292e'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23da292e' stroke='none'/%3e%3c/svg%3e\") no-repeat right 0/15%, "
				);
				card.firstElementChild.setAttribute("style", copyStyleCard);
			} else {
				card.firstElementChild.removeAttribute("style");
				card.firstElementChild.classList.add("card-img-invalid");
			}
		});

		// listener on the fields when their values change
		invalidsFields.forEach((modifyField) => {
			modifyField.addEventListener("input", (e) => {
				if (e.target.value.length >= 1) {
					e.target.classList.remove("is-invalid");
					invalidsFields.delete(e.target);

					if (
						[...invalidsFields].filter(
							(item) => item.dataset.id === e.target.dataset.id
						).length < 1
					) {
						let card = [...invalidsImagesCards].filter(
							(item) => item.dataset.id === e.target.dataset.id
						)[0];
						card.classList.remove(
							"border",
							"border-1",
							"border-danger"
						);
						if (existingImages && existingImages[card.dataset.id]) {
							let copyStyleCard =
								card.firstElementChild.getAttribute("style");
							copyStyleCard = copyStyleCard.replace(
								"background:url(\"data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23da292e'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23da292e' stroke='none'/%3e%3c/svg%3e\") no-repeat right 0/15%, ",
								"background: "
							);
							card.firstElementChild.setAttribute(
								"style",
								copyStyleCard
							);
						} else {
							card.firstElementChild.setAttribute(
								"style",
								"background: url(/src/img/no_image.png) no-repeat center 40%/cover; height: 30vh; border-radius: 10px 10px 0 0"
							);
							card.firstElementChild.classList.remove(
								"card-img-invalid"
							);
						}
						invalidsImagesCards = new Set(
							[...invalidsImagesCards].filter(
								(item) =>
									item.dataset.id !== e.target.dataset.id
							)
						);
					}
				} else if (!e.target.classList.contains("is-invalid")) {
					e.target.classList.add("is-invalid");
				}
			});
		});
	}

	// -------------------------------------------------------------

	if (existingImages) {
		// removal of the unnecessary path field on existing images
		let formPath = document.getElementsByClassName("form_path");
		while (formPath[0]) {
			formPath[0].parentNode.removeChild(formPath[0]);
		}
		// register deletion buttons for events
		let removedButtonsAvailables =
			document.querySelectorAll("[data-remove]");
		removedButtonsAvailables.forEach((item) => {
			removedButtons.push(item);
		});
	}

	requiredsFields = [...document.querySelectorAll("[required]")];

	document.getElementById("error_message");

	// -------------------------------------------------------------

	// management of card adding
	var addCardLink = document.querySelector("[data-add-card]");
	addCardLink.addEventListener("click", (e) => {
		addImage();
	});

	// delete management
	removeElements();

	// manages the display of the remaining character count
	displayingRemainingsCharacters();

	fieldsColoration();

	//---------------------------------------------------------------------------

	// SUBMIT - detects unfilled required fields
	var submit = document.querySelector(".submit");
	submit.addEventListener("click", (e) => {
		fieldsColoration();
	});
};
