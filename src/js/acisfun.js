// {"collected-xx": {"bugs": {"agrias-butterfly": true}, "fish": {"anchovy": true}}}
const AC_LOCALSTORAGE_VERSION_V2020_04_05 = 'collected-v20200405';


const AC_LOCALSTORAGE_VERSION = AC_LOCALSTORAGE_VERSION_V2020_04_05;

const AC_LOCALSTORAGE_VERSION_HISTORY = [
    AC_LOCALSTORAGE_VERSION_V2020_04_05
];

$(function () {
    let progress = new ProgressClass();

    let toggleCaughtCallback = function (event) {
        toggleCaught(event, progress);
    }.bind(progress);

    $('.caught-checkbox input').on('change', toggleCaughtCallback);

    checkItemsOnLoad(progress);
});

function checkItemsOnLoad(progressClass) {
    // Reset all inputs to unchecked, and then set them to checked
    $('.caught-checkbox input').prop('checked', '');

    let currentStorage = progressClass.currentStorage;

    let group = getCurrentGroupForPage();


    let groupStorage = {};
    if (currentStorage && group in currentStorage) {
        groupStorage = currentStorage[group];
    }

    let groupStorageKeys = Object.keys(groupStorage);

    for (let j = 0; j < groupStorageKeys.length; j++) {
        let key = groupStorageKeys[j];

        let inputSelector = 'input[id="check-' + key + '"]';
        if (true === groupStorage[key] && $(inputSelector).length) {
            $(inputSelector).prop('checked', true);
        }
    }


    setProgressBarWidth(progressClass);
}

function getCurrentGroupForPage() {
    let groups = $('body').attr('class').split(' ');

    return groups[1];
}

function toggleCaught(event, progressClass) {
    let isChecked = event.target.checked;
    let group = $(event.target).attr('data-group');
    let identifier = $(event.target).attr('data-identifier');


    let currentStorage = progressClass.currentStorage;

    if (!(group in currentStorage)) {
        currentStorage[group] = {};
    }

    if (isChecked) {
        if (!(identifier in currentStorage[group])) {
            currentStorage[group][identifier] = isChecked;
        }
        currentStorage[group][identifier] = isChecked;
    } else {
        if (identifier in currentStorage[group]) {
            delete currentStorage[group][identifier];
        }
    }

    progressClass.save(currentStorage);

    setProgressBarWidth(progressClass);
}


function setProgressBarWidth(progressClass) {
    let countItemsOnPage = $('.caught-checkbox input').length;
    if (countItemsOnPage <= 0) {
        return
    }

    let storage = progressClass.currentStorage;

    let group = getCurrentGroupForPage();

    if (!(group in storage)) {
        return;
    }

    let countItemsInStorage = Object.keys(storage[group]).length;

    // 20 out of 80 = (80 / 100 = 0.8), 20 / 0.8 = 25
    let onePercent = countItemsOnPage / 100;

    let currentPercentage = Math.ceil(countItemsInStorage / onePercent);
    if (currentPercentage > 100) {
        throw new Error('Someone messed something up');
    }
    console.log('currentPercentage: ', currentPercentage);

    $('.menu .progress .progress-bar').css('width', currentPercentage + '%');
    $('.menu .progress .js-menu-progress-label').text(countItemsInStorage + ' / ' + countItemsOnPage);
}


class ProgressClass {

    constructor() {
        this.currentStorage = this.loadCurrentStorage();
    }

    loadCurrentStorage() {
        let currentStorage = localStorage.getItem(AC_LOCALSTORAGE_VERSION);
        if (null === currentStorage) {
            // Could migrate from older versions here

            return {};
        }

        return JSON.parse(currentStorage);
    }

    save(data) {
        this.currentStorage = data;
        localStorage.setItem(AC_LOCALSTORAGE_VERSION, JSON.stringify(data));
    }

}