const form = document.querySelector("form");

form.addEventListener("submit", (e) => {
	e.preventDefault();
	const formData = new FormData(e.target);

	fetch("http://localhost/testfile/test.php", {
		method: "POST",
		body: formData,
	})
		.then((resp) => resp.text())
		.then((data) => console.log(data));
});

const pdfList = document.querySelector(".pdf-list");

fetch("http://localhost/testfile/test.php")
	.then((response) => response.blob())
	.then((blob) => {
		const url = URL.createObjectURL(blob);

		const downloadButton = document.createElement("a");
		downloadButton.href = url;
		downloadButton.download = "file.pdf";
		downloadButton.textContent = "Download PDF";

		document.body.appendChild(downloadButton);
	})
	.catch((error) => console.log(error));
