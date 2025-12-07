const readMore = ()=>{
	const readMoreButton = document.getElementById('shareReadMore');

	function clickHandler() {
		const texContainer = document.getElementById('moreContainer');
		texContainer.classList.toggle('active');
	}

	if(readMoreButton){
		readMoreButton.addEventListener('click', clickHandler)
	}
};

module.exports = readMore;
