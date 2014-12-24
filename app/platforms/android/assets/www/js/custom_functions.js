var chatscroll = new Object();
chatscroll.Pane = function(scrollContainerId) {
	this.bottomThreshold = 50;
	this.scrollContainerId = scrollContainerId;
}

chatscroll.Pane.prototype.activeScroll = function() {
	var scrollDiv = document.getElementById(this.scrollContainerId);
	var currentHeight = 0;

	if (scrollDiv.scrollHeight > 0)
		currentHeight = scrollDiv.scrollHeight;
	else if (objDiv.offsetHeight > 0)
		currentHeight = scrollDiv.offsetHeight;

	if (currentHeight
			- scrollDiv.scrollTop
			- ((scrollDiv.style.pixelHeight) ? scrollDiv.style.pixelHeight
					: scrollDiv.offsetHeight) < this.bottomThreshold)
		scrollDiv.scrollTop = currentHeight;

	scrollDiv = null;
}