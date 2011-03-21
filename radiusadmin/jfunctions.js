function setMeterBar(id, fraq, text) {
	if(isNaN(fraq)) fraq = 0;
	fraq = Math.max(0, Math.min(100, Math.round(fraq))) + "%";
	var node = (typeof id == "string" ? document.getElementById(id) : id);
	if(node.firstChild) {
		node.firstChild.childNodes[0].style.width = fraq;
		node.firstChild.childNodes[1].firstChild.data = fraq;
		if(defined(text)) node.lastChild.data = text;
	} else {
		node.innerHTML = '<div class="meter"><div class="bar" style="width:' + fraq + ';"></div>' + '<div class="text">' + fraq + '</div></div>' + (defined(text) ? text : "");
	}
}