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

    $('[data-toggle="tooltip"]').tooltip();

    let path = window.location.pathname;
    switch (path) {
        case '/':
            setTimeout(function () {
                checkProgressForHomepage();
            }, 500);
            break;
        case '/bugs':
        case '/fish':
        case '/fossils':
        case '/recipes':
        case '/songs':
        case '/recipes/cherry-blossom-season':
            checkItemsOnLoad(progress);
            registerFilters();
            break;
        case '/profile/':
            promiseFuncs.push(checkProfile);
            promiseFuncs.push(loadStorage);
            promiseFuncs.push(compareStorages);
            registerSaveLoadButtons();
            break;
    }


    let checkLoginFuncs = checkLogin();
    promiseObj.then(checkLoginFuncs[0], checkLoginFuncs[1]);

    for (let i = 0; i < promiseFuncs.length; i++) {
        promiseObj.then(promiseFuncs[i], function (Error) {
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
                authContainer.find('a')
                    .attr('href', '/profile/')
                    .find('span')
                    .text(user.get().name);
            }
            authContainer.css('opacity', '1')
        },
        function (Error) {
            authContainer.css('opacity', '1');
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
            $('.js-storage-container').addClass('loaded');
            return;
        }

        loadedProfileData = data.data;
        if (JSON.stringify(loadedProfileData) === JSON.stringify(progress.currentStorage)) {
            disableSaveLoadButtons();
        }
        writeStorageDataToDocument('.js-storage-container', loadedProfileData);
        $('.js-storage-container').addClass('loaded');
    }, undefined, 'json');
}

function loadStorageFromLocalStorage(progress) {
    if (!progress || !progress.currentStorage || Object.keys(progress.currentStorage).length === 0) {
        writeStorageDataToDocument('.js-browser-container', 0);
        $('.js-browser-container').addClass('loaded');
        return;
    }

    writeStorageDataToDocument('.js-browser-container', progress.currentStorage);
    $('.js-browser-container').addClass('loaded');
}

function writeStorageDataToDocument(selector, data) {
    if (typeof data === 'number' && 0 === data) {
        $(selector + ' .js-storage-text-container .js-storage-text').text(0);
        return;
    }

    var objKeys = Object.keys(data);
    for (var i = 0; i < objKeys.length; i++) {
        let groupKey = objKeys[i];
        let groupData = data[groupKey];
        let groupLength = Object.keys(groupData).length;

        $(selector + ' .js-storage-text-container[data-group="' + groupKey + '"] .js-storage-text').text(groupLength);
    }
}

function disableSaveLoadButtons() {
    $('.js-save-to-account, .js-load-into-browser').css({
        opacity: 0.3,
        pointerEvents: 'none',
    });
    $('.js-storage-compare').addClass('d-none');
}

function registerSaveLoadButtons() {
    $('.js-save-to-account').on('click', function () {
        disableSaveLoadButtons();
        $.post(
            '/profile/api/save',
            JSON.stringify(progress.currentStorage),
            function (data) {
                if (data && 'success' in data && data.success) {
                    writeStorageDataToDocument('.js-storage-container', progress.currentStorage);
                }
            }
        );
    });
    $('.js-load-into-browser').on('click', function () {
        disableSaveLoadButtons();
        if (false !== loadedProfileData) {
            progress.save(loadedProfileData);
        }
        writeStorageDataToDocument('.js-browser-container', loadedProfileData);
    });
}

function compareStorages() {
    let interval = setInterval(function () {

        if(
            !$('.js-browser-container').hasClass('loaded') ||
            !$('.js-storage-container').hasClass('loaded')
        ) {
            return;
        }

        let itemsInBrowser = $('.js-browser-container .js-storage-text-container');
        let itemsInStorage = $('.js-storage-container .js-storage-text-container');

        for (let i = 0; i < itemsInBrowser.length; i++) {
            let browserItem = itemsInBrowser[i];
            let browserCount = parseInt(browserItem.innerText);
            if (isNaN(browserCount)) {
                browserCount = 0;
            }

            let storageItem = itemsInStorage[i];
            let storageCount = parseInt(storageItem.innerText);
            if (isNaN(storageCount)) {
                storageCount = 0;
            }

            if (storageCount > browserCount) {
                $(browserItem).find('.js-storage-compare')
                    .addClass('text-danger')
                    .text('-' + (storageCount - browserCount));
            }
            if (storageCount < browserCount) {
                $(browserItem).find('.js-storage-compare')
                    .addClass('text-success')
                    .text('+' + (browserCount - storageCount));
            }
        }

        clearInterval(interval);
    }, 300);
}

function registerFilters() {
    $('.js-filter-item').on('click', function () {
        if ($(this).hasClass('badge-warning')) {
            $(this)
                .removeClass('badge-warning')
                .addClass('badge-light');

            $('.js-filterable').removeClass('opacity-40')
                .attr('style', '');
            return;
        }

        $('.js-filter-item').removeClass('badge-warning').addClass('badge-light');
        $('.js-filterable')
            .removeClass('opacity-100')
            .addClass('opacity-40')
            .attr('style', '')
        ;
        $(this).removeClass('badge-light').addClass('badge-warning');
        let selector = '.js-filterable[data-filters*=",' + $(this).attr('data-value') + ',"]';
        let items = $(selector);
        for (let i = 0; i < items.length; i++) {
            items[i].style.order = i;
        }
        $(selector).removeClass('opacity-40').addClass('opacity-100');
    });


    if (window.location.hash) {
        let hash = decodeURI(decodeURI(window.location.hash.replace('#', '')));
        $('.js-filter-item[data-value="' + hash + '"]').click();
    }
}

function checkProgressForHomepage() {
    let dataGroupDivs = $('div[data-group]');
    for (let i = 0; i < dataGroupDivs.length; i++) {
        let dataGroupDiv = dataGroupDivs[i];
        let items = $(dataGroupDiv).attr('data-items').split(',');
        let group = $(dataGroupDiv).attr('data-group');

        let count = 0;

        for (let j = 0; j < items.length; j++) {
            if (!(group in progress.currentStorage)) {
                continue;
            }

            if (items[j] in progress.currentStorage[group]) {
                count++;
            }
        }

        setTimeout(function () {
            $(dataGroupDiv).find('.progress-bar').css({
                width: Math.ceil((count / items.length) * 100) + '%',
            });
            $(dataGroupDiv).find('.js-progress-text').text(count);
        }, i * 150);
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
