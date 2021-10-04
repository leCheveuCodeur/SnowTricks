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
	var collectionHolder = document.getElementById("medias");
	var existingMedias = document.querySelectorAll(".card[data-target]");
	var mediasIndex = parseInt(collectionHolder.dataset.index, 10);

	var removedButtons = [];
	var requiredsFields = [];
	var invalidsFields = new Set();
	// use of a Set object to prevent duplication when there are two inputs for the same card
	var invalidedCards = new Set();
	var fileFields = new Set();
	var errorDisplay = false;

	// ---------------------------------------------------------

	function removeElements() {
		removedButtons.forEach((removedButton) => {
			removedButton.addEventListener("click", function (e) {
				var idRemovedButton = this.getAttribute("data-remove");
				var typeOfMedia = this.getAttribute("data-type");

				var card = document.querySelector(
					".card [data-type='" +
						typeOfMedia +
						"'][data-id='" +
						idRemovedButton +
						"']"
				);
				card ? card.remove() : "";

				var modal = document.getElementById(
					typeOfMedia + "_" + idRemovedButton + "_Modal"
				);
				modal ? modal.remove() : "";

				removedButtons = removedButtons.filter((item) => item !== this);

				requiredsFields = requiredsFields.filter(
					(item) =>
						item.dataset.id !== idRemovedButton ||
						item.dataset.type !== typeOfMedia
				);

				invalidsFields.forEach((item) => {
					if (
						item.dataset.id === idRemovedButton &&
						item.dataset.type === typeOfMedia
					) {
						invalidsFields.delete(item);
					}
				});

				invalidedCards.forEach((item) => {
					if (
						item.dataset.id === idRemovedButton &&
						item.dataset.type === typeOfMedia
					) {
						invalidedCards.delete(item);
					}
				});

				if (typeOfMedia === "img") {
					document
						.querySelector(
							"OPTION[data-id='" + idRemovedButton + "']"
						)
						.remove();
				}
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

	function updateListOfImagesInFront() {
		let selectImageInFront = document.getElementById(
			"contribution_image_in_front"
		);
		requiredsFields.forEach((requiredField) => {
			if (
				requiredField.hasAttribute("type") &&
				requiredField.getAttribute("type") === "file" &&
				fileFields.has(requiredField) === false
			) {
				fileFields.add(requiredField);
				requiredField.addEventListener("change", (e) => {
					let fileName = e.target.files[0].name;

					let optionSelect =
						"<option value='" +
						fileName +
						"' data-id='" +
						mediasIndex +
						"'>" +
						fileName +
						"</option>";
					selectImageInFront.insertAdjacentHTML(
						"beforeend",
						optionSelect
					);
				});
			}
		});
	}

	// ---------------------------------------------------------

	function addMediaCard(typeOfMedia) {
		let isImage = typeOfMedia === "img" ? true : false;
		mediasIndex++;

		var prototype = isImage
			? collectionHolder.dataset.imagePrototype
			: collectionHolder.dataset.videoPrototype;

		// rewriting the Prototype
		prototype = prototype.replace(/__name__/g, mediasIndex);

		let content = document.createElement("html");
		content.innerHTML = prototype;
		let newForm = content.querySelector("div");

		let src = "/src/img/no_" + (isImage ? "img" : "video") + ".svg";

		var fragmentCard = create(
			"<div class='card d-inline-block m-1' data-type='" +
				(isImage ? "img" : "video") +
				"' data-id='" +
				mediasIndex +
				"'><div class='card-img-top' style='background: url(" +
				src +
				") no-repeat center/50% #f8fafb; height: 30vh; border-radius: 10px 10px 0 0'></div><div class='card-body text-end'><button type='button' class='btn btn-primary bi bi-pencil-square' data-bs-toggle='modal' data-bs-target='#" +
				((isImage ? "img_" : "video_") + mediasIndex) +
				"_Modal'></button><button type='button' class='btn btn-danger bi bi-trash-fill ms-4' data-type='" +
				(isImage ? "img" : "video") +
				"' data-remove='" +
				mediasIndex +
				"' ></button></div>"
		);
		collectionHolder.append(fragmentCard);
		collectionHolder.append(newForm);

		// updating the counter of medias
		collectionHolder.dataset.index++;

		var removedButton = document.querySelector(
			"[data-type='" +
				(isImage ? "img" : "video") +
				"'][data-remove='" +
				mediasIndex +
				"']"
		);
		removedButtons.push(removedButton);

		let newInputs = [
			...document.querySelectorAll(
				"[id*='" +
					((isImage ? "images_" : "videos_") + mediasIndex) +
					"_'][required='required']"
			),
		];
		newInputs.forEach((input) => {
			requiredsFields.push(input);
		});

		// delete management
		removeElements();

		// manages the display of the remaining character count
		displayingRemainingsCharacters();

		updateListOfImagesInFront();
	}

	// ---------------------------------------------------------
	function fieldsColoration() {
		requiredsFields = [...document.querySelectorAll("[required]")];
		let errorBadge =
			"background:url(\"data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23da292e'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23da292e' stroke='none'/%3e%3c/svg%3e\") no-repeat right 0/15%, ";

		// detect all invalidedCards with null fields
		// (when loading the page, it only detects file fields that are reset after each submission and are therefore null).
		requiredsFields.forEach((field) => {
			if (field.value === "" || field.validity.patternMismatch) {
				invalidsFields.add(field);
				if (
					field.dataset.type === "img" ||
					field.dataset.type === "video"
				) {
					invalidedCards.add(
						document.querySelector(
							".card[data-type='" +
								field.dataset.type +
								"'][data-id='" +
								field.dataset.id +
								"']"
						)
					);
				}
			}
		});

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
		invalidsFields.forEach((invalidField) => {
			invalidField.classList.add("is-invalid");
		});
		invalidedCards.forEach((card) => {
			card.classList.add("border", "border-1", "border-danger");
			if ([...existingMedias][card.dataset.id]) {
				let copyStyleCard =
					card.firstElementChild.getAttribute("style");
				copyStyleCard = copyStyleCard.replace(
					"background: ",
					errorBadge
				);
				card.firstElementChild.setAttribute("style", copyStyleCard);
			} else {
				card.firstElementChild.removeAttribute("style");
				card.firstElementChild.classList.add(
					"card-" + card.dataset.type + "-invalid"
				);
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
						let card = [...invalidedCards].filter(
							(item) => item.dataset.id === e.target.dataset.id
						)[0];
						card.classList.remove(
							"border",
							"border-1",
							"border-danger"
						);
						if ([...existingMedias][card.dataset.id]) {
							let copyStyleCard =
								card.firstElementChild.getAttribute("style");
							copyStyleCard = copyStyleCard.replace(
								errorBadge,
								"background: "
							);
							card.firstElementChild.setAttribute(
								"style",
								copyStyleCard
							);
						} else {
							card.firstElementChild.setAttribute(
								"style",
								"background: url(/src/img/no_" +
									card.dataset.type +
									".svg) no-repeat center/50% #f8fafb; height: 30vh; border-radius: 10px 10px 0 0"
							);
							card.firstElementChild.classList.remove(
								"card-img-invalid",
								"card-video-invalid"
							);
						}
						invalidedCards = new Set(
							[...invalidedCards].filter(
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
	// -------------------------------------------------------------
	// INIT INIT INIT INIT INIT INIT INIT INIT INIT INIT INIT INIT

	if (existingMedias) {
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

	// management of image card adding
	var addMediaCardButtons = document.querySelectorAll("[data-add-card]");
	addMediaCardButtons.forEach((button) => {
		button.addEventListener("click", (e) => {
			addMediaCard(button.dataset.type);
		});
	});

	// delete management
	removeElements();

	// manages the display of the remaining character count
	displayingRemainingsCharacters();

	updateListOfImagesInFront();

	if (document.querySelector("[data-new-trick='1']") === false) {
		fieldsColoration();
	}
	//---------------------------------------------------------------------------

	// SUBMIT - detects unfilled required fields
	var submit = document.querySelector(".submit");
	submit.addEventListener("click", (e) => {
		fieldsColoration();
	});
};
