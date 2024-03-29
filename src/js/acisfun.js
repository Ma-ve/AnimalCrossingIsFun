// {"collected-xx": {"bugs": {"agrias-butterfly": true}, "fish": {"anchovy": true}}}
const AC_LOCALSTORAGE_VERSION_V2020_04_05 = 'collected-v20200405';


const AC_LOCALSTORAGE_VERSION = AC_LOCALSTORAGE_VERSION_V2020_04_05;

const AC_LOCALSTORAGE_VERSION_HISTORY = [
    AC_LOCALSTORAGE_VERSION_V2020_04_05
];

let user = false;
let progress = false;
let translationsClass = false;
let loadedProfileData = false;

$(function () {
    // Make :contains case insensitive
    $.expr[":"].contains = $.expr.createPseudo(function(arg) {
        return function( elem ) {
            return $(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;
        };
    });

    progress = new ProgressClass();
    translationsClass = new TranslationsClass(progress);

    let toggleCaughtCallback = function (event) {
        toggleCaught(event, progress);
    }.bind(progress);

    $('.caught-checkbox input').on('change', toggleCaughtCallback);


    let promiseFuncs = [];
    let promiseObj = loginPromise();

    $('[data-toggle="tooltip"]').tooltip();

    let path = window.location.pathname;
    let explode = path.split('/');

    let imploded = explode.slice(0, 2).join('/');

    switch (imploded) {
        case '/':
            setTimeout(function () {
                checkProgressForHomepage();
            }, 500);
            break;
        case '/bugs':
        case '/fish':
        case '/fossils':
        case '/deep-sea-creatures':
        case '/recipes':
        case '/songs':
        case '/events':
            checkItemsOnLoad(progress);
            registerFilters();
            break;
        case '/settings':
            promiseFuncs.push(checkProfile);
            promiseFuncs.push(loadStorage);
            promiseFuncs.push(compareStorages);
            setActiveValuesFromStorage();
            registerOnchangeSave();

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
            if (data?.data?.name) {
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

    $('.settings .name').text(user.get().name);
}

function loadStorage(data) {
    loadStorageFromDatabase(user);
    loadStorageFromLocalStorage(progress);
}

function loadStorageFromDatabase(user) {
    $.post('/settings/api/load', function (data) {
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
            '/settings/api/save',
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
                    .addClass('badge')
                    .addClass('badge-danger')
                    .addClass('comparison-badge')
                    .text('-' + (storageCount - browserCount));
            }
            if (storageCount < browserCount) {
                $(browserItem).find('.js-storage-compare')
                    .addClass('badge')
                    .addClass('badge-success')
                    .addClass('comparison-badge')
                    .text('+' + (browserCount - storageCount));
            }
        }

        clearInterval(interval);
    }, 300);
}

function resetAllItems() {
    $('.js-filterable').removeClass('opacity-60')
        .attr('style', '');
}

function registerFilters() {
    $('.js-filter-item').on('click', function () {
        $('.js-search input').val('');

        if ($(this).hasClass('badge-warning')) {
            $(this)
                .removeClass('badge-warning')
                .addClass('badge-light');

            resetAllItems();
            return;
        }

        $('.js-filter-item').removeClass('badge-warning').addClass('badge-light');
        $('.js-filterable')
            .removeClass('opacity-100')
            .addClass('opacity-60')
            .attr('style', '')
        ;
        $(this).removeClass('badge-light').addClass('badge-warning');
        let selector = '.js-filterable[data-filters*=",' + $(this).attr('data-value') + ',"]';
        let items = $(selector);
        for (let i = 0; i < items.length; i++) {
            items[i].style.order = i;
        }
        $(selector).removeClass('opacity-60').addClass('opacity-100');
    });

    $('.js-search').on('submit', function (e) {
        e.preventDefault();

        $('.js-filter-item.badge-warning')
            .removeClass('badge-warning')
            .addClass('badge-light');

        let searchTerm = $(this).find('input').val();

        window.location.hash = '#search-' + encodeURIComponent(encodeURIComponent(searchTerm));
        if (searchTerm === '') {
            resetAllItems();
            return;
        }

        $('.js-filterable')
            .removeClass('opacity-100')
            .addClass('opacity-60')
            .attr('style', '')

        searchTerm = searchTerm.replace('"', '');

        let selector = '.js-translateable:contains("' + searchTerm + '")';
        let items = $(selector);
        for (let i = 0; i < items.length; i++) {
            $(items[i]).closest('.js-filterable')
                .css('order', i)
                .removeClass('opacity-60')
                .addClass('opacity-100');
        }
    });


    if (window.location.hash) {
        let hash = decodeURI(decodeURI(window.location.hash.replace('#', '')));
        if (hash.indexOf('search-') === 0) {
            translationsClass.setSearchHash(hash.replace('search-', ''));
            return;
        }
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

function setActiveValuesFromStorage() {
    if(
        !progress ||
        !('currentStorage' in progress) ||
        !progress.currentStorage ||
        !('settings' in progress.currentStorage) ||
        !progress.currentStorage.settings
    ) {
        return;
    }

    let keys = Object.keys(progress.currentStorage.settings);
    for(let i = 0; i < keys.length; i++) {
        let key = keys[i];
        let selector = 'select[name="{key}"], input[name="{key}"]'.replace('{key}', key);
        $(selector).val(progress.currentStorage.settings[key]);
    }
}

function registerOnchangeSave() {
    $('form input, form select').on('change', function() {
        let storage = progress.currentStorage;
        if(!('settings' in storage)) {
            storage.settings = {};
        }

        var that = $(this);

        let inputName = that.attr('name');
        storage.settings[inputName] = that.val();
        progress.save(storage);

        that.addClass('is-valid');
        setTimeout(function() {
            that.removeClass('is-valid');
        }, 5000);
    })
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

function checkTranslations(translationsClass) {
    if (translationsClass.translations === false) {
        return;
    }

    setTranslations(translationsClass);

    $('.js-filterable .js-translateable:not(.is-translated)').each(function (index, elem) {
        let itemKey = elem.closest('.item-row-container').getAttribute('id');
        $(elem)
            .find('strong')
            .css('border-bottom', '2px dotted #666');
        $(elem)
            .append(
                '<i class="fad fa-comment-alt-edit ml-2"></i>'
            )
            .on('click', function (e) {
                e.preventDefault();
                $('#translations-modal').modal();
                $('.modal-title').text($(this).text());
                $('.modal-translation-code').text(translationsClass.languageCode);
                $('#translations-modal input#translation-suggest-key').val(itemKey);
                $('.modal-translation-code').text(translationsClass.languageCode);
            });
    });

    $('#translations-modal')
        // If started rendering
        .on('show.bs.modal', function (e) {
            $('#translations-modal input#translation-suggest-translation').val('');
            $('#translations-modal button[type="submit"]')
                .removeClass('btn-danger btn-success')
                .addClass('btn-primary')
                .text('Suggest');
        })
        // If done with rendering
        .on('shown.bs.modal', function (e) {
            $('#translations-modal input').focus();
        });

    $('#translations-modal form').on('submit', function (e) {
        e.preventDefault();

        let that = $(this);

        let button = that.find('button[type="submit"]');
        button.html('<i class="fad fa-spinner fa-pulse"></i>');

        let data = {
            langCode: translationsClass.languageCode
        };

        let inputs = that.find('input');
        for (let i = 0; i < inputs.length; i++) {
            data[$(inputs[i]).attr('name')] = $(inputs[i]).val();
        }
        $.post(
            '/translations/suggest',
            JSON.stringify(data),
            function (data) {
                if (data && 'data' in data && data.data) {
                    button
                        .removeClass('btn-error btn-primary')
                        .addClass('btn-success')
                        .html('<i class="fad fa-check"></i>');
                    setTimeout(function () {
                        $('#translations-modal').modal('hide');
                    }, 5000);
                    return;
                }

                button
                    .removeClass('btn-success btn-primary')
                    .addClass('btn-danger')
                    .html('&times;');
            }
        );
    });

}

function setTranslations(translationsClass) {
    let group = getCurrentGroupForPage();

    if (
        !translationsClass ||
        !('translations' in translationsClass) ||
        !translationsClass.translations ||
        !(group in translationsClass.translations)
    ) {
        return;
    }

    let translations = translationsClass.translations[group];
    let keys = Object.keys(translations);
    for (let i = 0; i < keys.length; i++) {
        let key = keys[i];
        if (translations[key].length <= 0) {
            continue;
        }

        $('.js-filterable[id="' + key + '"] .js-translateable')
            .addClass('is-translated')
            .find('strong')
            .text(translations[key]);
    }

    if(translationsClass.searchHash) {
        translationsClass.setSearchHash();
    }

    return;
}


class TranslationsClass {

    searchHash = false;

    constructor(progressClass) {
        this.progressClass = progressClass;
        if(
            !progressClass ||
            !progressClass.currentStorage ||
            !('settings' in progressClass.currentStorage) ||
            !('language' in progressClass.currentStorage.settings) ||
            progressClass.currentStorage.settings.language === 'en'
        ) {
            this.translations = false;
            return;
        }

        this.languageCode = progressClass.currentStorage.settings.language;
        this.loadTranslations();
    }

    setSearchHash(hash) {
        if (typeof hash !== 'undefined') {
            this.searchHash = hash;
        }

        $('.js-search input')
            .val(this.searchHash)
            .closest('form')
            .submit();
    }

    loadTranslations() {
        let langCode = this.languageCode;

        let currentStorage = localStorage.getItem('translations-' + langCode);
        if (null === currentStorage) {
            this.refreshLanguage();
            return;
        }

        // Check for timestamp, if recent, cache for an hour etc

        let parsedJson = JSON.parse(currentStorage);

        if(!('timestamp' in parsedJson)) {
            this.refreshLanguage();
            return;
        }

        // Current time = 22:20
        // Date in cache = 21:45
        // Is expired = false, it needs to be an hour

        // Current time = 22:00
        // Date in cache = 16:00
        // Is expired = true
        let dateFromCache = new Date(parsedJson.timestamp);

        let oneHourAgo = new Date();
        oneHourAgo.setHours(oneHourAgo.getHours() - 1);

        if(oneHourAgo <= dateFromCache) {
            this.translations = parsedJson.data;
            checkTranslations(this);
            return;
        }

        this.refreshLanguage();
    }

    refreshLanguage() {
        let translations = false;

        let that = this;
        $.get('/translations/load/' + this.languageCode, function (data) {
            if(data && 'data' in data && data.data) {
                that.save(data.data);
                checkTranslations(that);
            }
        });
    }

    save(data) {
        this.translations = data;
        localStorage.setItem('translations-' + this.languageCode, JSON.stringify({
            timestamp: new Date().toISOString().split('.')[0] + "Z",
            data: data
        }));
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
