document
	.getElementById("forrestWind")
	.play()
	.volume(0.5)
	.then(() => console.log("playing"))
	.catch((e) => console.error("blocked:", e));
