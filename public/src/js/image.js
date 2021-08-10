window.onload = () => {
	var addImageFormRemoveLink = (imageFormLi) => {
		var removeFormButton = document.createElement("button");
		removeFormButton.classList;
		// removeFormButton.innerHTML = "Supprimer";
		removeFormButton.classList.add("btn", "btn-danger", "bi", "bi-trash-fill");
		removeFormButton.dataset.remove;

		imageFormLi.append(removeFormButton);

		removeFormButton.addEventListener("click", (e) => {
			e.preventDefault();
			// remove the li for the tag form
			imageFormLi.remove();
		});
	};

	var addLinks = document.querySelectorAll("[data-add]");
	addLinks.forEach((addLink) => {
		addLink.addEventListener("click", function (e) {
			e.preventDefault();
			var collectionHolder = document.querySelector(
				"." + e.currentTarget.dataset.collectionHolderClass
			);

			var item = document.createElement("div");

			item.innerHTML = collectionHolder.dataset.prototype.replace(
				/__name__/g,
				collectionHolder.dataset.index
			);

			addImageFormRemoveLink(item);

			collectionHolder.appendChild(item);

			collectionHolder.dataset.index++;
		});
	});
};
