window.onload = () => {
	function loadMore(targetIdName) {
		let page = 1;

		const btnLoadMore = document.getElementById("loadMore");
		btnLoadMore.addEventListener("click", (e) => {
			page++;
			// On récupère l'url active
			const Url = new URL(window.location.href);
			// On lance la requête ajax
			let url = Url.pathname === "/" ? page : Url.pathname + "/" + page;
			fetch(url, {
				headers: {
					"X-Requested-With": "XMLHttpRequest",
				},
			})
				.then((response) => response.json())
				.then((data) => {
					// On va chercher la zone de contenu
					const content = document.getElementById(
						targetIdName.toString()
					);

					// On remplace le contenu
					content.innerHTML = data.content;

					// On met à jour l'url
					history.pushState({}, null, Url.pathname);

					const endOfCollection = data.endOfCollection;
					endOfCollection
						? document.getElementById("loadMore").remove()
						: "";
				});
				// .catch((e) => alert(e));
		});
	}

	loadMore("comments");

	loadMore("tricks");
};
