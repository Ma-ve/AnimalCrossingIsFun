// {"collected-xx": {"bugs": {"agrias-butterfly": true}, "fish": {"anchovy": true}}}
const AC_LOCALSTORAGE_VERSION_V2020_04_05 = 'collected-v20200405';


const AC_LOCALSTORAGE_VERSION = AC_LOCALSTORAGE_VERSION_V2020_04_05;

const AC_LOCALSTORAGE_VERSION_HISTORY = [
    AC_LOCALSTORAGE_VERSION_V2020_04_05
];

let user = false;

$(function () {
    let progress = new ProgressClass();

    let toggleCaughtCallback = function (event) {
        toggleCaught(event, progress);
    }.bind(progress);

    $('.caught-checkbox input').on('change', toggleCaughtCallback);


    let promiseFuncs = [];
    let promiseObj = loginPromise();

    let path = window.location.pathname;
    switch(path) {
        case '/bugs':
        case '/fish':
        case '/fossils':
        case '/recipes':
            checkItemsOnLoad(progress);
            break;
        case '/profile/':
            promiseFuncs.push(checkProfile);
            break;
    }

    promiseObj.then(checkLogin, function(Error) {
    });

    for(let i = 0; i < promiseFuncs.length; i++) {
        promiseObj.then(promiseFuncs[i], function(Error) {
        });
    }


});

function loginPromise() {
    return new Promise(function(resolve, reject) {
        $.get('/auth/me', function(data, text, xhr) {
            if(
                200 === xhr.status &&
                (data && 'data' in data && data.data)
            ) {
                resolve(data);
            } else {
                reject(Error('Unexpected response in response or data; xhr.status: ' + xhr.status));
            }
        });
    });
}

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

    $('.menu .progress .progress-bar').css('width', currentPercentage + '%');
    $('.menu .progress .js-menu-progress-label').text(countItemsInStorage + ' / ' + countItemsOnPage);
}

function makeAuthRequest(success, always) {
    let result = false;
    $.get('/auth/me', function (data) {
        if (data && 'data' in data && data.data && 'name' in data.data && data.data.name) {
            (typeof success === 'function') && success(data);

            return result = true;
        }

        return result = false;
    }).always(typeof always === 'function' ? always : function () {
    });

    return false;
}

function checkLogin(data) {
    let authContainer = $('.js-auth-container');

    if ('name' in data.data && data.data.name) {
        authContainer.text(data.data.name);
        user = new User();
        user.setUser(data.data);
    }

    authContainer.css('display', 'block');
}

function checkProfile(data) {
    if(!user) {
        window.location = '/';
        return;
    }

    $('.profile .name').text(user.get().name);
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



class User {

    constructor() {
        this.user = false;
    }

    setUser(data) {
        this.user = data;
    }

    get() {
        return this.user;
    }

}
