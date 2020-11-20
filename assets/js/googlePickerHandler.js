'use_strict';

import loadGoogleApi from 'google-api-load'

class GooglePickerHandler
{
    constructor(options) {
        this.pickerApiLoaded = false;
        this.oauthToken = null;
        this.gapi = null;


        this.loadPicker = this.loadPicker.bind(this);
        this.onAuthApiLoad = this.onAuthApiLoad.bind(this);
        this.onPickerApiLoad = this.onPickerApiLoad.bind(this);
        this.handleAuthResult = this.handleAuthResult.bind(this);
        this.createPicker = this.createPicker.bind(this);
        this.pickerCallback = this.pickerCallback.bind(this);
        this.loadAuth = this.loadAuth.bind(this);

        let defOptions = {
            scope: 'https://www.googleapis.com/auth/drive.file',
            pickerCallback: this.pickerCallback,
            selectCallback: (fileId, token) => {
                alert(fileId);
            },
            authLoadedCallback: (token) => {
                this.createPicker();
            },
            pickerLoadedCallback: (picker) => {
                this.createPicker();
            },
            pickerCreatedCallback: (picker) => {
                picker.addView(new google.picker.DocsView().setIncludeFolders(true));
                picker.addView(new google.picker.DocsUploadView().setIncludeFolders(true));

                picker.enableFeature(google.picker.Feature.NAV_HIDDEN);
                picker.enableFeature(google.picker.Feature.MULTISELECT_ENABLED);
                picker.enableFeature(google.picker.Feature.MINE_ONLY);
            },
            language: 'hu',
        }

        if (undefined !== options) {
            this.options = $.extend(defOptions, options);
        } else {
            this.options = defOptions;
        }
        if (! 'developerKey' in this.options) {
            throw 'Missing option: developerKey';
        }
        if (! 'clientId' in this.options) {
            throw 'Missing option: clientId';
        }
        if (! 'appId' in this.options) {
            throw 'Missing option: appId';
        }
    }

    loadApi() {
        loadGoogleApi().then((gapi) => {
            this.gapi = gapi;
        });
    }


    loadAuth() {
        this.gapi.load('auth', this.onAuthApiLoad);
    }

    // Use the Google API Loader script to load the google.picker script.
    loadPicker() {
        this.gapi.load('picker', this.onPickerApiLoad);
    }

    start() {
        if (!this.pickerApiLoaded) {
            this.loadPicker();
        }

        if (!this.oauthToken) {
            this.loadAuth();
        }
    }

    onAuthApiLoad() {
        this.gapi.auth.authorize(
            {
                'client_id': this.options.clientId,
                'scope': this.options.scope,
                'immediate': false
            },
            this.handleAuthResult);
    }

    handleAuthResult(authResult) {
        if (authResult && !authResult.error) {
            this.oauthToken = authResult.access_token;
            
            (this.options.authLoadedCallback)(this.oauthToken);
        }
    }

    onPickerApiLoad() {
        this.pickerApiLoaded = true;
        (this.options.pickerLoadedCallback)(google.picker);
    }

    // Create and render a Picker object for searching images.
    createPicker() {
        if (this.pickerApiLoaded && this.oauthToken) {
            let picker = new google.picker.PickerBuilder()
                .setAppId(this.options.appId)
                .setOAuthToken(this.oauthToken)
                .setDeveloperKey(this.options.developerKey)
                .setCallback(this.options.pickerCallback)
                .setLocale(this.options.language)
            ;
            (this.options.pickerCreatedCallback)(picker);

            let builtPicker = picker.build();
            builtPicker.setVisible(true);
        }
    }

    // A simple callback implementation.
    pickerCallback(data) {
        if (data.action === google.picker.Action.PICKED) {
            let fileId = data[google.picker.Response.DOCUMENTS][0].id;
            (this.options.selectCallback)(fileId, this.oauthToken);
        }
    }
}


export default GooglePickerHandler;