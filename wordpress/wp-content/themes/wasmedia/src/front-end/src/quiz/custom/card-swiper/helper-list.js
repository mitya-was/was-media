require('./helper-styles.scss');

module.exports = function (answersList = []) {
	const moreContainer = document.getElementById('moreContainer');
	const IMGX_LINK = 'https://was.imgix.net/wp-content/uploads/static/card-swiper/';

	if(moreContainer){
    answersList.forEach(answer=>{
			const wrapper = document.createElement('div');
			wrapper.classList.add('list-wrapper__custom');

			const title = document.createElement('p');
			title.classList.add('list-wrapper__custom-title');
			title.innerText=answer.title;

			const description = document.createElement('p');
			description.classList.add('list-wrapper__custom-description');
			description.innerText=answer.description;

			const image = document.createElement('img');
			image.classList.add('list-wrapper__custom-img');
			image.src=`${IMGX_LINK+answer.image}.jpg`;

			wrapper.appendChild(description);
			wrapper.appendChild(title);
			wrapper.appendChild(image);

			moreContainer.appendChild(wrapper);
		})
	}
};
