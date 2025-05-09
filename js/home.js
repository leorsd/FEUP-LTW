const hireButton = document.getElementById('hire-button');
const offerButton = document.getElementById('offer-button');
const offerSection = document.getElementById('offer');
offerSection.style.display = 'none';

hireButton.addEventListener('click', () => hireSelected());
offerButton.addEventListener('click', () => offerSelected());


function hireSelected() {
    const offerButton = document.getElementById('offer-button');
    offerButton.classList.remove('selected');
    const hireButton = document.getElementById('hire-button');
    hireButton.classList.add('selected');

    const hireSection = document.getElementById('hire');
    const offerSection = document.getElementById('offer');
    offerSection.classList.add('hide');

    setTimeout(() => {
        offerSection.style.display = 'none';
        hireSection.style.display = 'flex';
        hireSection.classList.remove("hide");
    }, 500);
}

function offerSelected() {
    const hireButton = document.getElementById('hire-button');
    hireButton.classList.remove('selected');
    const offerButton = document.getElementById('offer-button');
    offerButton.classList.add('selected');

    const hireSection = document.getElementById('hire');
    const offerSection = document.getElementById('offer');
    hireSection.classList.add('hide');

    setTimeout(() => {
        hireSection.style.display = 'none';
        offerSection.style.display = 'flex';
        offerSection.classList.remove("hide");
    }, 500);
}
