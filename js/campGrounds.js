document
	.getElementById("forrestWind")
	.play()
	.then(() => console.log("playing"))
	.catch((e) => console.error("blocked:", e));
