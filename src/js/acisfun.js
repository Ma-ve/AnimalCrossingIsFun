// {"collected-xx": {"bugs": {"agrias-butterfly": true}, "fish": {"anchovy": true}}}
const AC_LOCALSTORAGE_VERSION_V2020_04_05 = 'collected-v20200405';


const AC_LOCALSTORAGE_VERSION = AC_LOCALSTORAGE_VERSION_V2020_04_05;

const AC_LOCALSTORAGE_VERSION_HISTORY = [
    AC_LOCALSTORAGE_VERSION_V2020_04_05
];

let user = false;
let progress = false;
let loadedProfileData = false;

$(function () {
    progress = new ProgressClass();

    let toggleCaughtCallback = function (event) {
        toggleCaught(event, progress);
    }.bind(progress);

    $('.caught-checkbox input').on('change', toggleCaughtCallback);


    let promiseFuncs = [];
    let promiseObj = loginPromise();

    let path = window.location.pathname;
    switch (path) {
        case '/bugs':
        case '/fish':
        case '/fossils':
        case '/recipes':
            checkItemsOnLoad(progress);
            break;
        case '/profile/':
            promiseFuncs.push(checkProfile);
            promiseFuncs.push(loadStorage);
            $('.js-save-to-account').on('click', function () {
                $(this).css({
                    opacity: 0.3,
                    pointerEvents: 'none',
                });
                $.post(
                    '/profile/api/save',
                    JSON.stringify(progress.currentStorage),
                    function (data) {
                        if(data && 'success' in data && data.success) {
                            writeStorageDataToDocument('.js-storage-container', progress.currentStorage);
                        }
                    }
                );
            });
            $('.js-load-into-browser').on('click', function () {
                $(this).css({
                    opacity: 0.3,
                    pointerEvents: 'none',
                });

                if(false !== loadedProfileData) {
                    progress.save(loadedProfileData);
                }
                writeStorageDataToDocument('.js-browser-container', loadedProfileData);
            });
            break;
    }


    let checkLoginFuncs = checkLogin();
    promiseObj.then(checkLoginFuncs[0], checkLoginFuncs[1]);

    for(let i = 0; i < promiseFuncs.length; i++) {
        promiseObj.then(promiseFuncs[i], function(Error) {
        });
    }


});

function loginPromise() {
    return new Promise(function (resolve, reject) {
        $.get('/auth/me', function (data, text, xhr) {
            if (
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

function checkLogin() {
    let authContainer = $('.js-auth-container');

    return [
        function (data) {
            if ('name' in data.data && data.data.name) {
                user = new User();
                user.setUser(data.data);
                authContainer.html('<a href="/profile/">' + user.get().name + '</a>');
            }
            authContainer.css('display', 'block')
        },
        function (Error) {
            authContainer.css('display', 'block');
        }
    ];
}

function checkProfile(data) {
    if (!user) {
        window.location = '/';
        return;
    }

    $('.profile .name').text(user.get().name);
}

function loadStorage(data) {
    loadStorageFromDatabase(user);
    loadStorageFromLocalStorage(progress);
}

function loadStorageFromDatabase(user) {
    $.post('/profile/api/load', function (data) {
        if (!data || !('data' in data) || !data.data) {
            writeStorageDataToDocument('.js-storage-container', 0);
            return;
        }

        loadedProfileData = data.data;
        writeStorageDataToDocument('.js-storage-container', loadedProfileData);
    }, undefined, 'json');
}

function loadStorageFromLocalStorage(progress) {
    if(!progress || !progress.currentStorage || Object.keys(progress.currentStorage).length === 0) {
        writeStorageDataToDocument('.js-browser-container', 0);
        return;
    }

    writeStorageDataToDocument('.js-browser-container', progress.currentStorage);
}

function writeStorageDataToDocument(selector, data) {
    if(typeof data === 'number' && 0 === data) {
        $(selector + ' .js-storage-text').text(0);
        return;
    }

    var objKeys = Object.keys(data);
    for (var i = 0; i < objKeys.length; i++) {
        let groupKey = objKeys[i];
        let groupData = data[groupKey];
        let groupLength = Object.keys(groupData).length;

        $(selector + ' .js-storage-text[data-group="' + groupKey + '"]').text(groupLength);
    }
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
